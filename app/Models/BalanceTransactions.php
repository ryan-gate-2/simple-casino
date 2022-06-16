<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BalanceTransactions extends Model
{
    protected $table = 'balance_transactions';
    protected $timestamp = true;
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tx_id',
        'player_id',
        'credit',
        'debit',
        'old_balance',
        'new_balance',
        'currency',
        'extra_data',
    ];


    public static function writeTransaction($player_id, $credit, $debit, $old_balance, $new_balance, $currency, $extra_data = NULL) {
        $uuid = Str::uuid();

        $writeTransaction = BalanceTransactions::insert([
            'tx_id' => $uuid,
            'player_id' => $player_id,
            'credit' => $credit,
            'debit' => $debit,
            'old_balance' => $old_balance,
            'new_balance' => $new_balance,
            'currency' => $currency,
            'extra_data' => $extra_data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if($writeTransaction) {
            return true;
        } else {
            Log::critical('Critical error writing balance transaction history, please check. '.$writeTransaction);
            return false;
        }
    }

}
