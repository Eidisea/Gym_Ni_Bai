@extends('layouts.management')

@section('title', 'Process Cash Payment')
@section('subtitle', 'Quick cash payment processing for staff')

@section('content')
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('payment-transactions.index') }}" class="text-gray-400 hover:text-gray-100 text-sm transition-colors">
                    Payments
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Process Cash</span>
            </div>
        </li>
    </ol>
</nav>

<div class="max-w-2xl mx-auto mt-10" x-data="cashPOS()">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-100 mb-6 text-center">Cash Payment Processing</h2>
        
        <form method="POST" action="{{ route('payment-transactions.cash-process.store') }}" @submit.prevent="submitPayment">
            @csrf
            
            <input type="hidden" name="customer_id" x-model="customerId">
            <input type="hidden" name="subscription_id" x-model="subscriptionId">
            <input type="hidden" name="amount" x-model="amountDue">
            <input type="hidden" name="amount_received" x-model="amountTendered">
            <input type="hidden" name="change_given" x-model="changeAmount">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                    <div class="relative">
                        <input type="text" 
                               x-model.debounce.300ms="searchQuery"
                               @input="searchCustomers()"
                               @focus="showDropdown = true"
                               placeholder="Search customer by name..."
                               class="w-full px-4 py-3 text-sm bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        
                        <div x-show="showDropdown && searchResults.length > 0"
                             @click.away="showDropdown = false"
                             class="absolute z-10 w-full mt-1 bg-slate-700 border border-slate-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="customer in searchResults" :key="customer.customer_id">
                                <button type="button"
                                        @click="selectCustomer(customer)"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-100 hover:bg-slate-600 transition-colors border-b border-slate-600 last:border-b-0">
                                    <div x-text="customer.full_name" class="font-medium"></div>
                                    <div x-text="customer.email" class="text-xs text-gray-400"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Membership Plan</label>
                    <div class="relative">
                        <input type="text" 
                               :value="planName"
                               readonly
                               class="w-full px-4 py-3 text-sm bg-slate-600 border border-slate-600 rounded-lg text-gray-300 cursor-not-allowed">
                        <div x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-500"></div>
                        </div>
                    </div>
                    <div x-show="!customerId && !loading" class="mt-1 text-xs text-gray-400">
                        Select a customer to view their active plan
                    </div>
                    <div x-show="customerId && !subscriptionId && !loading" class="mt-2">
                        <a href="{{ route('membership-subscriptions.create') }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded-lg transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Membership Plan
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Amount Due</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-lg text-gray-400">₱</span>
                        <input type="text" 
                               :value="amountDue.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"
                               readonly
                               class="w-full pl-10 pr-4 py-3 text-lg font-semibold bg-slate-600 border border-slate-600 rounded-lg text-gray-100 cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-700">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Amount Tendered</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-lg text-gray-400">₱</span>
                        <input type="number" 
                               x-model.number="amountTendered"
                               step="0.01" 
                               min="0"
                               placeholder="0.00"
                               class="w-full pl-10 pr-4 py-3 text-lg bg-slate-700 border border-slate-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="p-4 bg-slate-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-300">Change</span>
                        <span class="text-2xl font-bold" 
                              :class="getChange() >= 0 ? 'text-green-400' : 'text-red-400'"
                              x-text="'₱' + Math.abs(getChange()).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                    <div x-show="amountTendered && getChange() < 0" class="mt-2 text-xs text-red-400">
                        Insufficient amount tendered
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-slate-700">
                <a href="{{ route('payment-transactions.index') }}" 
                   class="px-4 py-2 text-sm bg-slate-700 hover:bg-slate-600 text-gray-100 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        :disabled="!canSubmit()"
                        :class="canSubmit() ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-600 cursor-not-allowed opacity-50'"
                        class="px-6 py-2 text-sm text-white rounded-lg transition-colors">
                    Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cashPOS() {
    return {
        searchQuery: '',
        searchResults: [],
        showDropdown: false,
        customerId: '',
        subscriptionId: '',
        planName: 'Waiting for selection...',
        amountDue: 0,
        amountTendered: null,
        changeAmount: 0,
        loading: false,

        async searchCustomers() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            try {
                const response = await fetch(`/api/customers/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.searchResults = data.customers || [];
                this.showDropdown = true;
            } catch (error) {
                console.error('Error searching customers:', error);
                this.searchResults = [];
            }
        },

        async selectCustomer(customer) {
            this.customerId = customer.customer_id;
            this.searchQuery = customer.full_name;
            this.searchResults = [];
            this.showDropdown = false;
            
            await this.fetchActiveSubscription();
        },

        async fetchActiveSubscription() {
            this.loading = true;
            this.subscriptionId = '';
            this.planName = 'Loading...';
            this.amountDue = 0;
            
            try {
                const response = await fetch(`/api/customers/${this.customerId}/active-subscription`);
                const data = await response.json();
                
                if (data.subscription) {
                    this.subscriptionId = data.subscription.subscription_id;
                    this.planName = data.subscription.plan_name;
                    this.amountDue = parseFloat(data.subscription.price);
                } else {
                    this.planName = 'No active subscription';
                    this.amountDue = 0;
                }
            } catch (error) {
                console.error('Error fetching subscription:', error);
                this.planName = 'Error loading subscription';
                this.amountDue = 0;
            } finally {
                this.loading = false;
            }
        },

        getChange() {
            const tendered = parseFloat(this.amountTendered) || 0;
            const due = parseFloat(this.amountDue) || 0;
            this.changeAmount = Math.max(0, tendered - due);
            return tendered - due;
        },

        canSubmit() {
            return this.customerId && 
                   this.subscriptionId && 
                   this.amountDue > 0 && 
                   this.amountTendered && 
                   this.amountTendered >= this.amountDue;
        },

        submitPayment(event) {
            if (this.canSubmit()) {
                event.target.submit();
            }
        }
    }
}
</script>
@endsection
