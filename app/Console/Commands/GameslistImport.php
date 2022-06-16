<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommonSettings;
use Illuminate\Support\Facades\Http;
use App\Models\Gameslist;
use DB;

class GameslistImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:import-games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import games from external aggregation source';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getLinkExternalSource = CommonSettings::get('external_gameslist_link');
        if($getLinkExternalSource === false) {
            return 'Please set the \'external_gameslist_link\' setting_key in database.';
        }

        $get = Http::get($getLinkExternalSource);
        //dd($get);
        $decodedResult = json_decode($get, true);
        $getCurrentList = Gameslist::all();

        foreach($decodedResult as $game) {
           if(!$getCurrentList->where('game_id', $game['game_id'])->first()) {
           DB::table('gameslist')->insert([
                'game_id' => $game['game_id'],
                'game_slug' => $game['game_slug'],
                'game_name' => $game['game_name'],
                'game_provider' => $game['game_provider'],
                'game_desc' => $game['game_desc'],
                'extra_id' => $game['extra_id'],
                'demo_available' => $game['demo_available'],
                'hidden' => $game['hidden'],
                'disabled' => $game['disabled'],
                'index_rating' => $game['index_rating'],
                'api_ext' => $game['api_ext'],
                'type' => $game['type'],
                'parent_id' => $game['parent_id'],
                'game_img' => $game['game_img'],
                'softswiss_id' => $game['game_softswiss_id'],
                'softswiss_full' => $game['game_softswiss_full'],
                'softswiss_s1' => $game['game_img_s1'],
                'softswiss_s2' => $game['game_img_s2'],
                'softswiss_s3' => $game['game_img_s3'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
           }
        }

        return 0;
    }
}
