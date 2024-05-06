<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Http\Requests\Deposit\CreateRequest;
use App\Http\Requests\Withdraw\CreateRequest as WithdrawRequest;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function getTransactionData(): array
    {
        return Cache::remember('transaction_data_'.auth()->id(), Carbon::now()->addMinutes(5), function () {
            $transactions = Transaction::where('user_id', auth()->id())->paginate(10);
            $balance = auth()->user()->balance;

            return compact('transactions', 'balance');
        });
    }

    public function getDepositTransactionData(): array
    {
        return Cache::remember('deposit_transaction_data_'.auth()->id(), Carbon::now()->addMinutes(5), function () {
            $depositTransactions = Transaction::where('transaction_type', TransactionType::Deposit->value)
                ->where('user_id', auth()->id())
                ->paginate(10);
            $balance = auth()->user()->balance;

            return compact('depositTransactions', 'balance');
        });
    }

    public function addDeposit(CreateRequest $request): bool
    {
        try {
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

            Cache::forget('transaction_data_'.auth()->id());
            Cache::forget('deposit_transaction_data_'.auth()->id());

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getWithdrawTransactionData(): array
    {
        return Cache::remember('withdraw_transaction_data_'.auth()->id(), Carbon::now()->addMinutes(5), function () {
            $withdrawTransactions = Transaction::where('transaction_type', TransactionType::Withdraw->value)
                ->where('user_id', auth()->id())
                ->paginate(10);
            $balance = auth()->user()->balance;

            return compact('withdrawTransactions', 'balance');
        });
    }

    public function addWithdraw(WithdrawRequest $request): array
    {
        $user = auth()->user();

        try {
            DB::transaction(function () use ($user, $request, &$status) {
                $fee = $this->calculateWithdrawalFee($user, $request->amount);
                $this->checkSufficientBalance($user, $request->amount, $fee);

                $user->balance -= $request->amount + $fee;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_type' => TransactionType::Withdraw,
                    'amount' => $request->amount,
                    'fee' => $fee,
                    'date' => now(),
                ]);

                $status = ['success' => true, 'message' => 'Withdrawal added successfully.'];
            });

            Cache::forget('transaction_data_'.auth()->id());
            Cache::forget('withdraw_transaction_data_'.auth()->id());

            return $status ?? ['success' => false, 'message' => 'An error occurred.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function calculateWithdrawalFee($user, $amount): float
    {
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

        return ($amount <= $currentMonthLimit && $amount <= $todayLimit) ? 0 : $amount * 0.015 ;
    }

    private function checkSufficientBalance($user, $amount, $fee): void
    {
        if ($user->balance < $amount + $fee) {
            throw new \Exception('Insufficient balance.');
        }
    }
}
