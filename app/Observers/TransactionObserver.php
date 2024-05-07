<?php

namespace App\Observers;

use App\Enums\TransactionType;
use App\Models\Transaction;

class TransactionObserver
{
    public function creating(Transaction $transaction)
    {
        if ($transaction->transaction_type === TransactionType::Deposit) {
            $transaction->fee = 0;
        }
    }
}
