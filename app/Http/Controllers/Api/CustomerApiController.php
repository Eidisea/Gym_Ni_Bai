<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\MembershipSubscription;

class CustomerApiController extends Controller
{
    public function search()
    {
        $query = request('q');
        
        if (strlen($query) < 2) {
            return response()->json(['customers' => []]);
        }

        $customers = CustomerProfile::with('user')
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('email', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function($customer) {
                return [
                    'customer_id' => $customer->customer_id,
                    'full_name' => $customer->full_name,
                    'email' => $customer->user?->email ?? 'N/A',
                ];
            });

        return response()->json(['customers' => $customers]);
    }

    public function getActiveSubscription($customerId)
    {
        $subscription = MembershipSubscription::with(['plan' => fn($q) => $q->withTrashed()])
            ->where('customer_id', $customerId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['subscription' => null]);
        }

        return response()->json([
            'subscription' => [
                'subscription_id' => $subscription->subscription_id,
                'plan_name' => $subscription->plan?->plan_name ?? 'Archived Plan',
                'price' => $subscription->plan?->base_price ?? 0,
                'end_date' => $subscription->end_date->format('M j, Y'),
            ]
        ]);
    }
}
