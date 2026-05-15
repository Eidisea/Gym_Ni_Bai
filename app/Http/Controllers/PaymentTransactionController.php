<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\CustomerProfile;
use App\Models\MembershipSubscription;
use App\Models\ClassBooking;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // ADDED: For silent error logging
use Illuminate\Support\Facades\Auth;

class PaymentTransactionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PaymentTransaction::class);

        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'newest');

        // Eager-load relationships with withTrashed() for archive-aware display
        $query = PaymentTransaction::with([
            'customerProfile' => fn($q) => $q->withTrashed(),
            'subscription' => fn($q) => $q->with(['plan' => fn($pq) => $pq->withTrashed()]),
            'booking' => fn($q) => $q->with([
                'schedule' => fn($sq) => $sq->with([
                    'fitnessClass' => fn($fq) => $fq->withTrashed(),
                    'trainerProfile' => fn($tq) => $tq->withTrashed()
                ])
            ]),
            'cashPayment' => fn($q) => $q->with(['staffProfile' => fn($sq) => $sq->withTrashed()]),
            'cardPayment',
            'ewalletPayment'
        ]);

        // Search functionality
        $query->when($search, function($q) use ($search) {
            $q->where(function($subQuery) use ($search) {
                $subQuery->where('transaction_id', 'like', "%{$search}%")
                         ->orWhereHas('customerProfile', function($customerQuery) use ($search) {
                             $customerQuery->where('first_name', 'like', "%{$search}%")
                                           ->orWhere('last_name', 'like', "%{$search}%");
                         })
                         ->orWhereHas('cardPayment', function($cardQuery) use ($search) {
                             $cardQuery->where('authorization_code', 'like', "%{$search}%");
                         })
                         ->orWhereHas('ewalletPayment', function($ewalletQuery) use ($search) {
                             $ewalletQuery->where('reference_number', 'like', "%{$search}%");
                         });
            });
        });

        // Filter by status
        $query->when($filter === 'completed', function($q) {
            $q->where('status', 'completed');
        });

        $query->when($filter === 'pending', function($q) {
            $q->where('status', 'pending');
        });

        $query->when($filter === 'failed', function($q) {
            $q->where('status', 'failed');
        });

        $query->when($filter === 'refunded', function($q) {
            $q->where('status', 'refunded');
        });

        $query->when($filter === 'voided', function($q) {
            $q->where('status', 'voided');
        });

        // Filter by payment method
        $query->when($filter === 'cash', function($q) {
            $q->where('payment_method', 'cash');
        });

        $query->when($filter === 'card', function($q) {
            $q->where('payment_method', 'card');
        });

        $query->when($filter === 'ewallet', function($q) {
            $q->where('payment_method', 'ewallet');
        });

        // Sorting
        $query->when($sort === 'newest', function($q) {
            $q->orderBy('transaction_date', 'desc');
        });

        $query->when($sort === 'oldest', function($q) {
            $q->orderBy('transaction_date', 'asc');
        });

        $query->when($sort === 'amount_high', function($q) {
            $q->orderBy('amount', 'desc');
        });

        $query->when($sort === 'amount_low', function($q) {
            $q->orderBy('amount', 'asc');
        });

        $query->when($sort === 'customer_name', function($q) {
            $q->join('customer_profiles', 'payment_transactions.customer_id', '=', 'customer_profiles.customer_id')
              ->orderBy('customer_profiles.last_name', 'asc')
              ->orderBy('customer_profiles.first_name', 'asc')
              ->select('payment_transactions.*');
        });

        $transactions = $query->paginate(20)->withQueryString();

        return view('payment_transactions.index', compact('transactions', 'search', 'filter', 'sort'));
    }

    public function create()
    {
        Gate::authorize('create', PaymentTransaction::class);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $subscriptions = MembershipSubscription::with(['customerProfile', 'plan'])->get();
        $bookings = ClassBooking::with(['customerProfile', 'schedule.fitnessClass'])->get();
        $staff = StaffProfile::orderBy('last_name')->get();

        return view('payment_transactions.create', compact('customers', 'subscriptions', 'bookings', 'staff'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', PaymentTransaction::class);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'subscription_id' => ['nullable', 'exists:membership_subscriptions,subscription_id'],
            'booking_id' => ['nullable', 'exists:class_bookings,booking_id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,ewallet'],
            'status' => ['required', 'in:pending,completed,failed,refunded,voided'],
            'notes' => ['nullable', 'string', 'max:1000'],
            
            // Cash payment fields
            'staff_id' => ['required_if:payment_method,cash', 'exists:staff_profiles,staff_id'],
            'amount_received' => ['required_if:payment_method,cash', 'numeric', 'min:0'],
            'change_given' => ['nullable', 'numeric', 'min:0'],
            
            // Card payment fields
            'card_last_four' => ['required_if:payment_method,card', 'string', 'size:4'],
            'card_type' => ['required_if:payment_method,card', 'in:visa,mastercard,amex,discover'],
            'authorization_code' => ['required_if:payment_method,card', 'string', 'max:50'],
            'processor_reference' => ['nullable', 'string', 'max:100'],
            
            // E-wallet payment fields
            'provider' => ['required_if:payment_method,ewallet', 'in:gcash,paymaya,grabpay,paypal'],
            'reference_number' => ['required_if:payment_method,ewallet', 'string', 'max:100'],
            'account_identifier' => ['nullable', 'string', 'max:100'],
        ]); 

        try {
            DB::transaction(function () use ($validated) {
                // Create main transaction
                $validated['transaction_date'] = now();
                $transaction = PaymentTransaction::create($validated);

                // Create payment method specific record
                switch ($validated['payment_method']) {
                    case 'cash':
                        $transaction->cashPayment()->create([
                            'staff_id' => $validated['staff_id'],
                            'amount_received' => $validated['amount_received'],
                            'change_given' => $validated['change_given'] ?? 0,
                        ]);
                        break;

                    case 'card':
                        $transaction->cardPayment()->create([
                            'card_last_four' => $validated['card_last_four'],
                            'card_type' => $validated['card_type'],
                            'authorization_code' => $validated['authorization_code'],
                            'processor_reference' => $validated['processor_reference'] ?? null,
                        ]);
                        break;

                    case 'ewallet':
                        $transaction->ewalletPayment()->create([
                            'provider' => $validated['provider'],
                            'reference_number' => $validated['reference_number'],
                            'account_identifier' => $validated['account_identifier'] ?? null,
                        ]);
                        break;
                }
            });

            return redirect()->route('payment-transactions.index')
                ->with('success', 'Payment transaction created successfully.');

        } catch (\Exception $e) {
            // FIX: If anything fails, log it and return the user safely instead of crashing
            Log::error('Payment Transaction Failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment failed to process due to a system error. Please try again.');
        }
    }

    public function show(PaymentTransaction $paymentTransaction)
    {
        Gate::authorize('view', $paymentTransaction);

        // Eager-load all related data with withTrashed() for archive-aware display
        $paymentTransaction->load([
            'customerProfile' => fn($q) => $q->withTrashed()->with('user'),
            'subscription' => fn($q) => $q->with(['plan' => fn($pq) => $pq->withTrashed()]),
            'booking' => fn($q) => $q->with([
                'schedule' => fn($sq) => $sq->with([
                    'fitnessClass' => fn($fq) => $fq->withTrashed(),
                    'trainerProfile' => fn($tq) => $tq->withTrashed()
                ])
            ]),
            'cashPayment' => fn($q) => $q->with(['staffProfile' => fn($sq) => $sq->withTrashed()]),
            'cardPayment',
            'ewalletPayment'
        ]);

        return view('payment_transactions.show', compact('paymentTransaction'));
    }

    public function edit(PaymentTransaction $paymentTransaction)
    {
        Gate::authorize('update', $paymentTransaction);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $subscriptions = MembershipSubscription::with(['customerProfile', 'plan'])->get();
        $bookings = ClassBooking::with(['customerProfile', 'schedule.fitnessClass'])->get();
        $staff = StaffProfile::orderBy('last_name')->get();

        return view('payment_transactions.edit', compact('paymentTransaction', 'customers', 'subscriptions', 'bookings', 'staff'));
    }

    public function update(Request $request, PaymentTransaction $paymentTransaction)
    {
        Gate::authorize('update', $paymentTransaction);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,completed,failed,refunded,voided'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $paymentTransaction->update($validated);

        return redirect()->route('payment-transactions.show', $paymentTransaction)
            ->with('success', 'Payment transaction updated successfully.');
    }

    public function destroy(PaymentTransaction $paymentTransaction)
    {
        Gate::authorize('delete', $paymentTransaction);

        // Void the transaction instead of deleting it
        $paymentTransaction->update([
            'status' => 'voided',
            'notes' => ($paymentTransaction->notes ? $paymentTransaction->notes . ' | ' : '') . 'Voided on ' . now()->format('Y-m-d H:i:s') . ' by ' . Auth::user()->email,
        ]);

        return redirect()->route('payment-transactions.index')
            ->with('success', 'Payment transaction voided successfully.');
    }

    public function cashProcess()
    {
        Gate::authorize('create', PaymentTransaction::class);

        $customers = CustomerProfile::orderBy('last_name')->get();
        $subscriptions = MembershipSubscription::with([
            'customerProfile' => fn($q) => $q->withTrashed(),
            'plan' => fn($q) => $q->withTrashed()
        ])
            ->where('status', 'active')
            ->get();

        return view('payment_transactions.cash_process', compact('customers', 'subscriptions'));
    }

    public function cashProcessStore(Request $request)
    {
        Gate::authorize('create', PaymentTransaction::class);

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customer_profiles,customer_id'],
            'subscription_id' => ['required', 'exists:membership_subscriptions,subscription_id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'amount_received' => ['required', 'numeric', 'min:0'],
            'change_given' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Use the Auth facade to satisfy static analyzers and avoid undefined method errors
        $staffProfile = Auth::user()?->staffProfile;
        
        if (!$staffProfile) {
            return redirect()->back()
                ->with('error', 'Staff profile not found. Please contact administrator.')
                ->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $staffProfile) {
                $transaction = PaymentTransaction::create([
                    'customer_id' => $validated['customer_id'],
                    'subscription_id' => $validated['subscription_id'],
                    'booking_id' => null,
                    'amount' => $validated['amount'],
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'transaction_date' => now(),
                ]);

                $transaction->cashPayment()->create([
                    'staff_id' => $staffProfile->staff_id,
                    'amount_received' => $validated['amount_received'],
                    'change_given' => $validated['change_given'] ?? 0,
                ]);
            });

            return redirect()->route('payment-transactions.index')
                ->with('success', 'Cash payment processed successfully.');

        } catch (\Exception $e) {
            // FIX: Prevent crashes on rapid/failed cash processing
            Log::error('Cash Processing Failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cash payment failed to process due to a system error. Please try again.');
        }
    }
}