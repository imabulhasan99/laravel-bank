<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Enums\AccountType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Deposit\CreateRequest;
use App\Http\Requests\Withdraw\CreateRequest as DepositRequest;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::where('user_id', auth()->id())->paginate(10);
        $balance = auth()->user()->balance;
        return view('transaction.index', compact('transactions', 'balance'));
    }

    public function depositTransction(): View
    {
        $depositTransactions = Transaction::where('transaction_type', TransactionType::Deposit->value)
            ->where('user_id', auth()->id())
            ->paginate(10);
        $balance = auth()->user()->balance;
        return view('transaction.deposit', compact('depositTransactions', 'balance'));
    }

    public function addDeposit(CreateRequest $request): RedirectResponse
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

    public function withdrawTransction(): View
    {
        $withdrawTransactions = Transaction::where('transaction_type', TransactionType::Withdraw->value)
            ->where('user_id', auth()->id())
            ->paginate(10);
        $balance = auth()->user()->balance;
        return view('transaction.withdraw', compact('withdrawTransactions', 'balance'));
    }

    public function addWithdraw(DepositRequest $request): RedirectResponse
    {
        $user = auth()->user();

        try {
            DB::transaction(function () use ($user, $request) {
                $currentMonthWithdrawals = Transaction::where('user_id', $user->id)
                    ->where('transaction_type', TransactionType::Withdraw)
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->sum('amount');

                $todayWithdrawals = Transaction::where('user_id', $user->id)
                    ->where('transaction_type', TransactionType::Withdraw)
                    ->whereDate('date', today())
                    ->sum('amount');

                $currentMonthLimit = 5000 - $currentMonthWithdrawals;
                $todayLimit = 1000 - $todayWithdrawals;

                if (floatval($request->amount) <= $currentMonthLimit && floatval($request->amount) <= $todayLimit) {
                    $fee = 0;
                } else {
                    $fee = floatval($request->amount) * (0.015 / 100);
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
