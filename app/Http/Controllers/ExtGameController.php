<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Gameslist;
use App\Models\CommonSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class ExtGameController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function createSession($data) {
 
    	$data = json_decode($data);
    	$user = $data->user;
    	$game = $data->game;
    	$player_id = $data->player_id;
    	$currency = $data->currency;
    	$mode = $data->mode;

    	$apikey = CommonSettings::get('external_apikey');
    	$external_ownerkey = CommonSettings::get('external_ownerkey');

    	$createGameSession = 'https://rdev1.bets.sh/game/createSession?apikey='.$apikey.'&apikey_owner='.$external_ownerkey.'&currency='.$currency.'&game='.$game.'&playerid='.$player_id;

    	$flight = Http::get($createGameSession);

    	if($flight->status() === 200) {
            return response()->json([
            	'status' => 200,
                'url' => $flight['url']
            ], 200);

    	} else {
    		$error = 'Error creating session: '.$createGameSession.$flight;
    		Log::error($error);
            return response()->json([
            	'status' => 500,
                'error' => $error
            ], 500);
    	}
    }

    public static function callbackBalance(Request $request) 
    {
            if(env('APP_CALLBACK_LOG') === true) {
                $assignRandomId = Str::Uuid();
                Log::debug('Balance Callback: '.$assignRandomId.' Request: '.$request);
            }

            $validator = Validator::make($request->all(), [
                'playerid' => ['required', 'min:1', 'max:25'],
                'currency' => ['required', 'min:1', 'max:25'],
            ]);

            $playerid = $request->playerid;
            $currency = $request->currency;
            $getBalance = User::balance($playerid, $currency);
            if($getBalance) {
                $createArray = array(
                    'result' => array(
                        'balance' => (int) $getBalance * 100,
                        'freegames' => 0,
                    ),
                    'id' => 0,
                );
                return response()->json($createArray);

            }
            else {
                die('400');
            };
    }


    public static function callbackBet(Request $request) 
    {

            if(env('APP_CALLBACK_LOG') === true) {
                $assignRandomId = Str::Uuid();
                Log::debug('Game Callback: '.$assignRandomId.' URL: '.$request->fullUrl());
                Log::debug($request);
            } 
            $validator = Validator::make($request->all(), [
                'bet' => ['required', 'min:1', 'max:25'],
                'win' => ['required', 'min:1', 'max:25'],
                'currency' => ['required', 'min:2', 'max:15'],
                'gameid' => ['required', 'min:2', 'max:100'],
                'game_provider' => ['min:2', 'max:40'],
                'playerid' => ['required', 'min:2', 'max:50'],
                'roundid' => ['required', 'min:2', 'max:50'],
                'sign' => ['required', 'min:2', 'max:100'],
            ]);

            //If not using cloudflare, make sure to switch the IP vars below, also make sure to set 'external_api_allowed_ips' in the CommonSettings module to allow IP address from aggregator to access.

            //$ip = $_SERVER['REMOTE_ADDR'];
            $ip = $request->header('CF-Connecting-IP');

            if ($validator->stopOnFirstFailure()->fails()) {
                return response()->json(['status' => 400, 'error' => 'Validation of request form failed.', 'validation_messages' => $validator->errors(), 'request_ip' => $ip])->setStatusCode(400);
            }

            if(!str_contains(CommonSettings::get('external_api_allowed_ips'), $ip)) {
                 $createArray = array(
                    'result' => array(
                        'balance' => false,
                        'freegames' => 0,
                    ),
                    'status' => 401,
                    'message' => 'IP is not accepted',
                    'id' => $security_signature,
                );
            }

            $betAmount = $request->bet;
            $winAmount = $request->win;
            $currency = $request->currency;
            $game_id = $request->gameid;
            $game_provider = $request->game_provider ?? NULL;
            $player_id = $request->playerid; 
            $roundid = $request->roundid;
            $security_signature = $request->sign;
            $security_timestamp = $request->t;

            $api_secretkey = CommonSettings::get('external_operator_secret');

            //Changed to hmac based signature, aggregation sends "t" (for unix timestamp) and "sign" - signature hmac value is built as follows "$roundid-$timestamp" and with as key the secret key set/given by aggregator, what we now do below is a time-based check this way we can deny old callbacks & basically can sign off any old transactions comfortably
            $verifySign = hash_hmac('md5', $roundid.'-'.$security_timestamp, $api_secretkey);

            $current_timestamp = time();
            $max_age_setting = CommonSettings::get('max_game_tx_age');
            $max_age_accepted = ($current_timestamp - $max_age_setting);

            if($max_age_accepted > $security_timestamp) {
                Log::warning('Game transaction not accepted as age of signature is too old, unix max. timestamp: '.$max_age_accepted.' while game is '.$security_timestamp.', diff. '.$request);
                     $createArray = array(
                        'result' => array(
                            'balance' => false,
                            'freegames' => 0,
                        ),
                        'status' => 403,
                        'message' => 'Signature too old',
                        'id' => $security_signature,
                    );
                    return response()->json($createArray, 403);
            }

            if($verifySign !== $security_signature) {
                    Log::critical('Wrong signature used in callback, check a.s.a.p. if configuration is correct. Request: '.$request);

                    $createArray = array(
                        'result' => array(
                            'balance' => false,
                            'freegames' => 0,
                        ),
                        'status' => 403,
                        'message' => 'Signature does not match up',
                        'id' => $security_signature,
                    );
                    return response()->json($createArray, 403);
            }

            //Searching up the actual player
            $searchUser = User::where('player_id', $player_id)->first();

            if($searchUser) {
                $getBalance = User::balance($player_id, $currency);
                if(!$getBalance) {
                    Log::critical('Error retrieving balance in game callback while player seems to exist');
                    die(400);
                }
                //Changing amount to aggregation format (currency amount basically in cents as integer, 1.00$ becomes 100 to return)
                $playerCurrentBalance = (int) $getBalance * 100;
                if($betAmount > $playerCurrentBalance) {
                    $createArray = array(
                        'result' => array(
                            'balance' => false,
                            'freegames' => 0,
                        ),
                        'status' => 402,
                        'message' => 'Insufficient balance',
                        'id' => $security_signature,
                    );
                    return response()->json($createArray, 402);
                }

                if($betAmount > 0 or $winAmount > 0) {
                    //Changing amount to our number_format & requesting balance modification, make sure number_format is enforced else you will get faulty balance calculations & make sure to check your php_precision settings to support decimals comfortably.
                    if($betAmount > 0) {
                        $debitAmount = floatval(number_format(floatval($betAmount / 100), 7, '.', ''));
                        $changeBalance = User::changeBalance($debitAmount, 'debit', $player_id, $currency);
                    } elseif($winAmount > 0) {
                        $creditAmount = floatval(number_format(floatval($winAmount / 100), 7, '.', ''));
                        $changeBalance = User::changeBalance($creditAmount, 'credit', $player_id, $currency);
                    }
                } 

                //And we request & return the current balance to game aggregation so player can place another bet =}
                $finalBalance = (int) (User::balance($player_id, $currency) * 100);
                /* This signature is used if additional security is required by aggregation middleware
                $passedSignature = hash_hmac('md5', $finalBalance, $api_secretkey);
                */
                $createArray = array(
                    'result' => array(
                        'balance' => $finalBalance,
                        'freegames' => 0,
                    ),
                    'status' => 200,
                    'message' => 'n/a',
                    'id' => $security_signature,
                );
            

            if(env('APP_CALLBACK_LOG') === true) {
                Log::debug('Game Callback:'.$assignRandomId.' Response: '.json_encode($createArray));
            }

            return response()->json($createArray, 200);

            } else {
                Log::warning('Player not found on bet callback, check backoffice - possibly somebody trying to intrude - shazam alert)'.$request);
                die('419');
            };

    }
}
