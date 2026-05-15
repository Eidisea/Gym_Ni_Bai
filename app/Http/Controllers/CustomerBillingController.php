<?php

namespace App\Http\Controllers;

use App\Models\MembershipSubscription;
use App\Models\MembershipPlan;
use App\Models\PaymentTransaction;
use App\Models\CardPayment;
use App\Models\EwalletPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // ADDED for sending emails

class CustomerBillingController extends Controller
{
    public function index()
    {
        $customerId = Auth::user()->customerProfile->customer_id;

        $activeSubscription = MembershipSubscription::with(['plan' => fn($q) => $q->withTrashed()])
            ->where('customer_id', $customerId)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('end_date', 'desc')
            ->first();

        $availablePlans = MembershipPlan::whereNull('archived_at')
            ->orderBy('base_price', 'asc')
            ->get();

        $transactions = PaymentTransaction::with([
            'subscription.plan' => fn($q) => $q->withTrashed(),
            'cardPayment',
            'ewalletPayment',
        ])
            ->whereHas('subscription', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('customer.billing', compact('activeSubscription', 'availablePlans', 'transactions'));
    }

    public function processRenewal(Request $request)
    {
        $validated = $request->validate([
            'plan_id'           => ['required', 'exists:membership_plans,plan_id'],
            'payment_method'    => ['required', 'in:card,ewallet'],

            // Card fields — required when payment_method is card
            'card_last_four'      => ['required_if:payment_method,card', 'nullable', 'digits:4'],
            'card_type'           => ['required_if:payment_method,card', 'nullable', 'in:visa,mastercard,amex,discover'],
            'authorization_code'  => ['required_if:payment_method,card', 'nullable', 'string', 'max:50'],
            'processor_reference' => ['nullable', 'string', 'max:100'],

            // E-wallet fields — required when payment_method is ewallet
            'provider'           => ['required_if:payment_method,ewallet', 'nullable', 'in:gcash,paymaya,grabpay,paypal'],
            'reference_number'   => ['required_if:payment_method,ewallet', 'nullable', 'string', 'max:100'],
            'account_identifier' => ['nullable', 'string', 'max:100'],
        ]);

        $customerId = Auth::user()->customerProfile->customer_id;
        $plan       = MembershipPlan::findOrFail($validated['plan_id']);

        DB::transaction(function () use ($validated, $customerId, $plan) {
            // Create the subscription
            $subscription = MembershipSubscription::create([
                'customer_id' => $customerId,
                'plan_id'     => $plan->plan_id,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays($plan->duration_days)->toDateString(),
                'status'      => 'active',
            ]);

            // Create the parent payment transaction
            $transaction = PaymentTransaction::create([
                'customer_id'      => $customerId,
                'subscription_id'  => $subscription->subscription_id,
                'amount'           => $plan->base_price,
                'payment_method'   => $validated['payment_method'],
                'status'           => 'completed',
                'transaction_date' => now(),
            ]);

            // Create the payment-method detail record
            if ($validated['payment_method'] === 'card') {
                CardPayment::create([
                    'transaction_id'      => $transaction->transaction_id,
                    'card_last_four'      => $validated['card_last_four'],
                    'card_type'           => $validated['card_type'],
                    'authorization_code'  => $validated['authorization_code'],
                    'processor_reference' => $validated['processor_reference'] ?? null,
                ]);
            } elseif ($validated['payment_method'] === 'ewallet') {
                EwalletPayment::create([
                    'transaction_id'     => $transaction->transaction_id,
                    'provider'           => $validated['provider'],
                    'reference_number'   => $validated['reference_number'],
                    'account_identifier' => $validated['account_identifier'] ?? null,
                ]);
            }

            // Send membership activation email
            $subscription->load(['customerProfile', 'plan']);
            Mail::to(Auth::user()->email)->send(new \App\Mail\MembershipActivated($subscription));
        });

        return redirect()->back()->with('success', 'Payment successful! Your membership is now active.');
    }

    public function cancelSubscription()
    {
        $customerId = Auth::user()->customerProfile->customer_id;

        $activeSubscription = MembershipSubscription::where('customer_id', $customerId)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->first();

        if (!$activeSubscription) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $activeSubscription->update([
            'status'   => 'cancelled',
            'end_date' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Subscription successfully cancelled.');
    }
}