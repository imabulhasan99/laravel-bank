<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Enums\AccountType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Deposit\CreateRequest;
use App\Http\Requests\Withdraw\CreateRequest as DepositRequest;

class TransactionController extends Controller
{
    public function index(TransactionService $transactionService): View
    {
      $data =  $transactionService->getTransactionData();
        return view('transaction.index', 
        ['transactions' => $data['transactions'], 'balance' => $data['balance']]);
    }

    public function depositTransction(TransactionService $transactionService): View
    {
        $data =  $transactionService->getDepositTransactionData();
        return view('transaction.deposit', 
        ['depositTransactions' => $data['depositTransactions'], 'balance' => $data['balance']]);
    }

    public function addDeposit(CreateRequest $request, TransactionService $transactionService)
    {
        if ($transactionService->addDeposit($request)) {
            return redirect()->route('transaction.deposit')->with('status', 'Deposit added successfully.');
        } else {
            return redirect()->route('transaction.deposit')->with('status', 'Failed to add deposit.');
        }
    }

    public function withdrawTransction(TransactionService $transactionService): View
    {
       $data = $transactionService->getWithdrawTransactionData();
        return view('transaction.withdraw', 
        ['withdrawTransactions' => $data['withdrawTransactions'], 'balance' => $data['balance']]);
    }

    public function addWithdraw(DepositRequest $request, TransactionService $transactionService): RedirectResponse
    {
        $data = $transactionService->addWithdraw($request);
        if ($data['success']) {
            return redirect()->route('transaction.withdraw')->with('status', $data['message']);
        } else {
            return redirect()->route('transaction.withdraw')->with('status', $data['message']);
        }
    }
}
