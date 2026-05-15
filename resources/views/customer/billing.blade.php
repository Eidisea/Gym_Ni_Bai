@extends('layouts.customer')

@section('content')
<div
    class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full"
    x-data="{
        selectedPlan: null,
        selectedPlanName: '',
        selectedPlanAmount: '',
        selectedPayment: null,
        openPaymentModal() {
            if (!this.selectedPlan || !this.selectedPayment) return;
            this.$dispatch('open-modal', 'payment-details');
        }
    }"
>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Billing & Memberships</h1>
        <p class="mt-2 text-sm text-slate-600">Manage your membership and view transaction history</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <p class="font-medium mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ================================================================
             LEFT COLUMN — Plan + Method selection (2/3 width)
        ================================================================ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Renew Your Membership</h2>

                {{-- Active subscription notice + cancel --}}
                @if($activeSubscription)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800">Current Plan: {{ $activeSubscription->plan?->plan_name ?? 'Archived Plan' }}</p>
                                    <p class="text-xs text-green-600">Expires on {{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'confirm-cancel-subscription')"
                                class="flex-shrink-0 px-3 py-1.5 border border-red-600 text-red-600 hover:bg-red-50 text-xs font-medium rounded-lg transition">
                                Cancel Subscription
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Plan selection --}}
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Select a Plan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availablePlans as $plan)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="plan_id_display"
                                    value="{{ $plan->plan_id }}"
                                    x-model="selectedPlan"
                                    @change="selectedPlanName = '{{ addslashes($plan->plan_name) }}'; selectedPlanAmount = '₱{{ number_format($plan->base_price, 2) }}'"
                                    class="sr-only">
                                <div
                                    :class="selectedPlan == '{{ $plan->plan_id }}' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300'"
                                    class="border-2 rounded-lg p-4 transition h-full">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-slate-900">{{ $plan->plan_name }}</h4>
                                        <div class="w-5 h-5 flex-shrink-0">
                                            <svg x-show="selectedPlan == '{{ $plan->plan_id }}'" class="w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <svg x-show="selectedPlan != '{{ $plan->plan_id }}'" class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <circle cx="12" cy="12" r="10"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-2">{{ $plan->duration_days }} days</p>
                                    <p class="text-2xl font-bold text-slate-900">₱{{ number_format($plan->base_price, 2) }}</p>
                                    @if($plan->description)
                                        <p class="text-xs text-slate-500 mt-2">{{ $plan->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Payment method selection --}}
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Payment Method</h3>
                    <div class="space-y-3">

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method_display" value="card" x-model="selectedPayment" class="sr-only">
                            <div :class="selectedPayment === 'card' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300'" class="border-2 rounded-lg p-4 flex items-center transition">
                                <div class="w-5 h-5 mr-3 flex-shrink-0">
                                    <svg x-show="selectedPayment === 'card'" class="w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <svg x-show="selectedPayment !== 'card'" class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="h-6 w-8 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-slate-900">Credit / Debit Card</p>
                                        <p class="text-sm text-slate-500">Visa, Mastercard, Amex, Discover</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method_display" value="ewallet" x-model="selectedPayment" class="sr-only">
                            <div :class="selectedPayment === 'ewallet' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:border-slate-300'" class="border-2 rounded-lg p-4 flex items-center transition">
                                <div class="w-5 h-5 mr-3 flex-shrink-0">
                                    <svg x-show="selectedPayment === 'ewallet'" class="w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <svg x-show="selectedPayment !== 'ewallet'" class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-3">
                                    <svg class="h-6 w-6 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-slate-900">E-Wallet</p>
                                        <p class="text-sm text-slate-500">GCash, PayMaya, GrabPay, PayPal</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- CTA --}}
                <button
                    type="button"
                    @click="openPaymentModal()"
                    :disabled="!selectedPlan || !selectedPayment"
                    :class="!selectedPlan || !selectedPayment ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="w-full px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg transition">
                    Proceed to Payment
                </button>
            </div>

            {{-- Cash info banner --}}
            <div class="mt-6 bg-slate-50 border border-slate-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-slate-500 mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Prefer to pay in cash?</p>
                        <p class="text-sm text-slate-600 mt-1">Visit the Gym Ni Bai front desk to purchase or renew your membership in person.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
             RIGHT COLUMN — Transaction history (1/3 width)
        ================================================================ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Transaction History</h2>

                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                            <div class="border-l-4 border-slate-200 pl-4 pb-4">
                                <div class="flex justify-between items-start mb-1">
                                    <div>
                                        <p class="font-medium text-slate-900 text-sm">{{ $transaction->subscription?->plan?->plan_name ?? 'Archived Plan' }}</p>
                                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</p>
                                    </div>
                                    @if($transaction->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                    @elseif($transaction->status === 'refunded')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Refunded</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </div>
                                <p class="text-lg font-bold text-slate-900">₱{{ number_format($transaction->amount, 2) }}</p>

                                {{-- Payment method detail line --}}
                                @if($transaction->payment_method === 'card' && $transaction->cardPayment)
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ ucfirst($transaction->cardPayment->card_type) }} ···· {{ $transaction->cardPayment->card_last_four }}
                                    </p>
                                @elseif($transaction->payment_method === 'ewallet' && $transaction->ewalletPayment)
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ ucfirst($transaction->ewalletPayment->provider) }} · Ref: {{ $transaction->ewalletPayment->reference_number }}
                                    </p>
                                @else
                                    <p class="text-xs text-slate-500 mt-0.5">{{ ucfirst($transaction->payment_method) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-slate-900 mb-2">No Transactions</h3>
                        <p class="text-sm text-slate-600">Your payment history will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ====================================================================
         PAYMENT DETAILS MODAL
         One modal, two inner panels toggled by selectedPayment.
    ==================================================================== --}}
    <x-modal name="payment-details" maxWidth="lg" focusable>
        <form action="{{ route('customer.billing.renew') }}" method="POST">
            @csrf

            {{-- Hidden fields carry the selections made on the page --}}
            <input type="hidden" name="plan_id" :value="selectedPlan">
            <input type="hidden" name="payment_method" :value="selectedPayment">

            <div class="p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Complete Your Payment</h3>
                    <button type="button" @click="$dispatch('close-modal', 'payment-details')" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Order summary --}}
                <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Order Summary</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-900" x-text="selectedPlanName"></span>
                        <span class="text-lg font-bold text-slate-900" x-text="selectedPlanAmount"></span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1" x-text="selectedPayment === 'card' ? 'Credit / Debit Card' : 'E-Wallet'"></p>
                </div>

                {{-- ── CARD FORM ── --}}
                <div x-show="selectedPayment === 'card'" x-cloak>
                    <p class="text-sm font-semibold text-slate-700 mb-4">Card Details</p>

                    <div class="space-y-4">
                        {{-- Card number display (extracts last 4 into hidden field) --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Card Number</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    placeholder="1234 5678 9012 3456"
                                    maxlength="19"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-12"
                                    oninput="
                                        let v = this.value.replace(/\D/g,'').substring(0,16);
                                        this.value = v.replace(/(.{4})/g,'$1 ').trim();
                                        document.getElementById('card_last_four').value = v.slice(-4);
                                    ">
                                <svg class="absolute right-3 top-2.5 h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <input type="hidden" name="card_last_four" id="card_last_four">
                        </div>

                        {{-- Expiry + CVV --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Date</label>
                                <input
                                    type="text"
                                    placeholder="MM / YY"
                                    maxlength="7"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    oninput="
                                        let v = this.value.replace(/\D/g,'').substring(0,4);
                                        this.value = v.length > 2 ? v.slice(0,2) + ' / ' + v.slice(2) : v;
                                    ">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">CVV</label>
                                <input
                                    type="password"
                                    placeholder="•••"
                                    maxlength="4"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    oninput="this.value = this.value.replace(/\D/g,'')">
                            </div>
                        </div>

                        {{-- Cardholder name --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Cardholder Name</label>
                            <input
                                type="text"
                                placeholder="Name as it appears on card"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        {{-- Card network --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Card Network</label>
                            <select name="card_type" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                <option value="">Select network</option>
                                <option value="visa">Visa</option>
                                <option value="mastercard">Mastercard</option>
                                <option value="amex">Amex</option>
                                <option value="discover">Discover</option>
                            </select>
                        </div>

                        {{-- Mock auth fields — auto-generated, not shown to user --}}
                        <input type="hidden" name="authorization_code" value="AUTH-{{ strtoupper(substr(md5(uniqid()), 0, 8)) }}">
                        <input type="hidden" name="processor_reference" value="REF-{{ strtoupper(substr(md5(uniqid()), 0, 8)) }}">
                    </div>

                    <div class="mt-4 flex items-center text-xs text-slate-500 gap-1.5">
                        <svg class="h-4 w-4 text-green-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Your card details are encrypted and processed securely.
                    </div>
                </div>

                {{-- ── E-WALLET FORM ── --}}
                <div x-show="selectedPayment === 'ewallet'" x-cloak>
                    <p class="text-sm font-semibold text-slate-700 mb-4">E-Wallet Details</p>

                    <div class="space-y-4">
                        {{-- Provider picker --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Select Provider</label>
                            <div class="grid grid-cols-2 gap-3" x-data="{ provider: '' }">
                                @foreach(['gcash' => 'GCash', 'paymaya' => 'PayMaya', 'grabpay' => 'GrabPay', 'paypal' => 'PayPal'] as $value => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="provider" value="{{ $value }}" x-model="provider" class="sr-only">
                                        <div
                                            :class="provider === '{{ $value }}' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-700 hover:border-slate-300'"
                                            class="border-2 rounded-lg px-3 py-2.5 text-sm font-medium text-center transition">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Mobile number --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mobile Number / Account</label>
                            <input
                                type="text"
                                name="account_identifier"
                                placeholder="+63 9XX XXX XXXX"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        {{-- Reference number --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Reference / OTP Code</label>
                            <input
                                type="text"
                                name="reference_number"
                                placeholder="Enter the reference number from your app"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800">
                        <p class="font-medium mb-1">How to pay via e-wallet:</p>
                        <ol class="list-decimal list-inside space-y-0.5">
                            <li>Open your e-wallet app and send the exact amount shown above to <strong>Gym Ni Bai</strong>.</li>
                            <li>Copy the reference number from your transaction receipt.</li>
                            <li>Enter it above and click Confirm Payment.</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-lg">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'payment-details')"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Back
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                    Confirm Payment
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ====================================================================
         CANCEL SUBSCRIPTION CONFIRMATION MODAL
    ==================================================================== --}}
    <x-modal name="confirm-cancel-subscription" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 mr-4">
                    <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Cancel Subscription</h3>
            </div>

            <p class="text-sm text-slate-600 mb-1">Are you sure you want to cancel your active subscription?</p>
            <p class="text-sm text-slate-500">Your access to book classes will be revoked immediately. This action cannot be undone.</p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'confirm-cancel-subscription')"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Keep Subscription
                </button>
                <form method="POST" action="{{ route('customer.billing.cancel-subscription') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        Yes, Cancel It
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

</div>
@endsection