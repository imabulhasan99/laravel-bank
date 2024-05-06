<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Deposit\CreateRequest;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::paginate(10);
        $balance = auth()->user()->balance;
        return view('transaction.index', compact('transactions', 'balance'));
    }
    public function depositTransction() {
        $depositTransactions = Transaction::where('transaction_type', TransactionType::Deposit->value)->paginate(10);
        return view('transaction.deposit', compact('depositTransactions'));
    }
    public function addDeposit(CreateRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = auth()->user();
            $user->balance += $request->amount;
            $user->save();
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'transaction_type' => TransactionType::Deposit,
                'date' => now(),
            ]);
        });
    
        return redirect()->route('transaction.deposit')->with('status', 'Deposit added successfully.');
    }
   
}
