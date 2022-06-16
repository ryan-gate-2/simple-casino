<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gameslist extends Model
{
    protected $table = 'gameslist';
    protected $timestamp = true;
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'game_id',
        'game_slug',
        'game_name',
        'game_provider',
        'game_desc',
        'extra_id',
        'demo_available',
        'disabled',
        'hidden',
        'index_rating',
        'api_ext',
        'parent_id',
        'softswiss_id',
        'softswiss_full',
        'softswiss_s1',
        'softswiss_s2',
        'softswiss_s3',
    ];

}
