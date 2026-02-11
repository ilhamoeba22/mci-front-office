<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\Queue;
use Carbon\Carbon;

class TransactionPrintController extends Controller
{
    /**
     * Print Setor Tunai Slip (HTML based for precision)
     */
    public function printSetor($token)
    {
        $transaction = Transaction::where('token', $token)->firstOrFail();
        return view('print.setor', [
            'data' => $transaction,
            'token' => $token
        ]);
    }

    /**
     * Print Tarik Tunai Slip (HTML based - Support 2 pages)
     */
    public function printTarik($token)
    {
        $withdrawal = Withdrawal::where('token', $token)->firstOrFail();
        return view('print.tarik', [
            'data' => $withdrawal,
            'token' => $token
        ]);
    }

    /**
     * Print Transfer Slip (HTML based)
     */
    public function printTransfer($token)
    {
        $transfer = Transfer::where('token', $token)->firstOrFail();
        return view('print.transfer', [
            'data' => $transfer,
            'token' => $token
        ]);
    }
}
