<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Transactions</h3>
                    @if ($balance > 0)
                        <p class="text-md font-semibold mb-4 text-red-500">Balance: {{ $balance }}</p>
                    @endif
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Transaction Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr class="{{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}">
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->id }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->user_id }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->transaction_type }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->amount }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->fee }}</td>
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $transaction->date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
