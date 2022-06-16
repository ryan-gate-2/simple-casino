<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Gameslist;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use App\Http\Controllers\ExtGameController;
use Illuminate\Support\Facades\Response;

class LauncherWire extends Component
{
    public $loadedGame = false;
 
 
    public function render(Request $request)
    {
		if($request->slug !== NULL) {
    	 	$selectGame = Gameslist::all()->where('game_id', $request->slug)->first();
    	 	if(!$selectGame) {
    	 		Filament::notify('danger', 'Oops..');

	      		return view('livewire.launcher-wire')->with(['viewType' => 'error', 'text' => 'Game not found, select another game or retry.']);
    	 	}

    	 	if($selectGame) {
    	 		$prepareData = json_encode([
    	 			'user' => auth()->user()->id,
                    'player_id' => auth()->user()->player_id,
    	 			'game' => $selectGame->game_id,
    	 			'currency' => auth()->user()->active_currency,
    	 			'mode' => 'real'
    	 		]);
    	 		$requestNewGame = ExtGameController::createSession($prepareData);
                $status = $requestNewGame->status();
                if($status === 200) {
                    $array = json_decode($requestNewGame->content(), true);
                    $url = $array['url'];
                } else {
                    Filament::notify('danger', 'Oops.. error '.$status);
                    return view('livewire.launcher-wire')->with(['viewType' => 'error', 'text' => $requestNewGame]);
                }
    	 	}

      		return view('livewire.launcher-wire')->with(['viewType' => 'gamelaunch', 'slug' => $selectGame->game_id, 'game_name' => $selectGame->game_name, 'url' => $url, 'text' => 'Goodluck!']);
		}

    	if($request->slug === NULL) {
    	 	$games = Gameslist::all()->random('25');
            return view('livewire.random-games-polling')->with(['games' => $games, 'viewType' => 'no-game-selected', 'text' => 'You first need to a select a game to start playing.']);

    	}

    }

}
