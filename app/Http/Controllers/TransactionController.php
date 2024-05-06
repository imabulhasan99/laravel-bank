<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::paginate(10);
        $balance = auth()->user()->balance;
        return view('transaction.index', compact('transactions', 'balance'));
    }
}
