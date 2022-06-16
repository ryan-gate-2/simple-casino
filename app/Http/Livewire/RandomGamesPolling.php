<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Gameslist;

class RandomGamesPolling extends Component
{
	protected $listeners = ['refreshComponent' => '$refresh'];

	public function render()
	{
			$games = Gameslist::all()->random('15');
	        return view('livewire.random-games-polling')->with(['games' => $games, 'viewType' => 'normal']);
	}
}
