<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Waiting for Approval</h1>
            <p class="text-gray-600">Your transaction has been submitted and is awaiting admin approval.</p>
        </div>

        @if($isRejected)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-red-800">Transaction Rejected</h3>
                        <p class="text-red-700">This transaction was rejected by <span class="font-bold">{{ $rejectedBy ?? 'an administrator' }}</span>.</p>
                        @if($rejectionReason)
                            <p class="text-red-600 mt-1">Reason: {{ $rejectionReason }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Transaction Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Transaction Details</h2>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                    Pending Approval
                </span>
            </div>

            @if($transactionType === 'transaction' && $transaction)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Transaction Type</p>
                        <p class="text-lg text-gray-900">{{ $transaction->transaction_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Amount</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($transaction->amount, 2) }} EGP</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Customer</p>
                        <p class="text-lg text-gray-900">{{ $transaction->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reference Number</p>
                        <p class="text-lg text-gray-900">{{ $transaction->reference_number }}</p>
                    </div>
                    @if($transaction->deduction > 0)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Discount Applied</p>
                            <p class="text-lg text-green-600 font-bold">{{ number_format($transaction->deduction, 2) }} EGP</p>
                        </div>
                    @endif
                </div>
            @elseif($transactionType === 'cash_transaction' && $cashTransaction)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Transaction Type</p>
                        <p class="text-lg text-gray-900">{{ $cashTransaction->transaction_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Amount</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($cashTransaction->amount, 2) }} EGP</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Customer</p>
                        <p class="text-lg text-gray-900">{{ $cashTransaction->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reference Number</p>
                        <p class="text-lg text-gray-900">{{ $cashTransaction->reference_number }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center">
                <button wire:click="checkApprovalStatus" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 mb-4">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Check Approval Status
                </button>

                @if($isApproved)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-medium text-green-800">Transaction Approved!</h3>
                                <p class="text-green-700">Your transaction has been approved and is now complete.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button wire:click="goToReceipt" 
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Print Receipt
                        </button>
                        <button wire:click="goToTransactions" 
                                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Go to Transactions
                        </button>
                    </div>
                @elseif($showContactMessage)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-medium text-yellow-800">Still Pending</h3>
                                <p class="text-yellow-700">Your transaction is still awaiting approval. Please contact an administrator for assistance.</p>
                            </div>
                        </div>
                    </div>
                    <button wire:click="goToTransactions" 
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Go to Transactions
                    </button>
                @endif
            </div>
        </div>

        <!-- Information Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        An administrator will review your transaction and either approve or reject it. 
                        You can check the status using the button above, or contact an administrator directly.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div> 