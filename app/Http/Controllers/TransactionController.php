<?php

namespace App\Http\Controllers;

use App\Http\Requests\Deposit\CreateRequest;
use App\Http\Requests\Withdraw\CreateRequest as DepositRequest;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(TransactionService $transactionService): View
    {
        $data = $transactionService->getTransactionData();

        return view('transaction.index',
            ['transactions' => $data['transactions'], 'balance' => $data['balance']]);
    }

    public function depositTransction(TransactionService $transactionService): View
    {
        $data = $transactionService->getDepositTransactionData();

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
