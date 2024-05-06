<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\AccountType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Deposit\CreateRequest;
use App\Http\Requests\Withdraw\CreateRequest as DepositRequest;

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

    public function withdrawTransction() {
        $withTransactions = Transaction::where('transaction_type', TransactionType::Withdraw->value)->paginate(10);
        return view('transaction.withdraw', compact('withTransactions'));
    }


    public function addWithdraw(DepositRequest $request)
    {
        $user = auth()->user();
    
        try {
            DB::transaction(function () use ($user, $request) {
                $currentMonthWithdrawals = Transaction::where('user_id', $user->id)
                    ->where('transaction_type', TransactionType::Withdraw)
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount');
                $remainingMonthlyFreeAmount = 5000 - $currentMonthWithdrawals;
    
                $remainingTransactionFreeAmount = 1000 - $request->amount;
                $remainingFreeAmount = min($remainingMonthlyFreeAmount, $remainingTransactionFreeAmount);
    
                if ($user->account_type === AccountType::Individual) {
                    if (Carbon::now()->dayOfWeek === Carbon::FRIDAY || $request->amount <= $remainingFreeAmount) {
                        $fee = 0;
                    } else {
                        $fee = ($request->amount - $remainingFreeAmount) * 0.015 / 100;
                    }
                } else {
                    $totalWithdrawal = Transaction::where('user_id', $user->id)
                        ->where('transaction_type', TransactionType::Withdraw)
                        ->sum('amount');
        
                    $fee = ($totalWithdrawal > 50000) ? ($request->amount * 0.015 / 100) : ($request->amount * 0.025 / 100);
                }
                if ($user->balance < $request->amount + $fee) {
                    throw new \Exception('Insufficient balance.');
                }
                $user->balance -= $request->amount + $fee;
                $user->save();
    
                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_type' => TransactionType::Withdraw,
                    'amount' => $request->amount,
                    'fee' => $fee,
                    'date' => now(),
                ]);
            });
    
            return redirect()->route('transaction.withdraw')->with('status', 'Withdrawal added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('transaction.withdraw')->with('status', $e->getMessage());
        }
    }
    


}


