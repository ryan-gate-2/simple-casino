<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonSettings extends Model
{
    protected $table = 'common_settings';
    protected $timestamp = true;
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_desc',
    ];

    public static function get($key) {
    	$db = self::where('setting_key', $key)->first();

    	if($db) {
    		return $db->setting_value;
    	} else {
    		return false;
    	}
    }

}
