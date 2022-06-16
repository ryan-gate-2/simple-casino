<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active_currency',
        'balance_usd',
        'balance_eur',
        'balance_cad',
        'games_played',
        'player_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function balance($player_id, $currency) {
        $searchUser = User::where('player_id', $player_id)->first();
        if($searchUser) {
            $searchBalance = 'balance_'.strtolower($currency);
            return $searchUser->$searchBalance;

        } else {
            return false;
        }
    }


    public static function changeBalance($amount, $type, $player_id, $currency) {
        Log::notice('Balance change request, amount '.$amount.$currency.' player_id'.$player_id);
        $getBalance = self::balance($player_id, $currency);
        if(!$getBalance) {
            Log::warning('Error retrieving balance for transaction processing.');
            return false;
        }

        $searchUser = User::where('player_id', $player_id)->first();
        if(!$searchUser) {
            Log::warning('Player not found for transaction processing.');
            return false;
        }

        $balanceFormatConcat = 'balance_'.strtolower($currency);

        if($type === 'credit') {
            $newBalance = floatval(number_format(($getBalance + $amount), 7, '.', ''));
            $updateRecord = $searchUser->update([$balanceFormatConcat => number_format($newBalance, 7, '.', '')]);
            $creditAmount = $amount;
            $debitAmount = floatval('0.00');
        } elseif($type === 'debit') {
            $newBalance = floatval(number_format(($getBalance - $amount), 7, '.', ''));
            $updateRecord = $searchUser->update([$balanceFormatConcat => number_format($newBalance, 7, '.', '')]);
            $debitAmount = $amount;
            $creditAmount = floatval('0.00');
        }

        if($updateRecord) {
        //Write transaction to database for archiving & administration purposes
        $writeTransaction = BalanceTransactions::writeTransaction($player_id, $creditAmount, $debitAmount, $getBalance, $newBalance, $currency);
        } else {
            Log::critical('Error on changeBalance() function writing new balance to user in database, check immediate. '.$updateRecord);
            return false;
        }
        
        if($writeTransaction) {
        //Successfully changed balance
        return true;
        } else {
            Log::critical('Error on changeBalance() writing transactional history, it _SEEMS_ balance however was added to user but as no record now exists of this change please check player thoroughly. '.$writeTransaction);
            return false;
        }
    }


}
