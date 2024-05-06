<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Enums\TransactionType;

class TransactionObserver
{
    public function creating(Transaction $transaction)
    {
        if ($transaction->transaction_type === TransactionType::Deposit) {
            $transaction->fee = 0;
        }
    }
}
