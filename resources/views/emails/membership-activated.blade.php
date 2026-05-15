<x-mail::message>
# Membership Activated! 🎊

Hello {{ $customer->first_name }},

Congratulations! Your membership has been successfully activated. Welcome to the Gym Ni Bai family!

## Membership Details

**Plan:** {{ $plan->plan_name }}  
**Start Date:** {{ $subscription->start_date->format('F j, Y') }}  
**End Date:** {{ $subscription->end_date->format('F j, Y') }}  
**Status:** {{ $subscription->status }}

**Subscription ID:** #{{ str_pad($subscription->subscription_id, 6, '0', STR_PAD_LEFT) }}

## What You Can Do Now

✅ Book fitness classes  
✅ Access all gym facilities  
✅ Participate in group activities  
✅ Get personalized training sessions  

## Getting Started

Ready to begin your fitness journey? Browse our available classes and book your first session!

<x-mail::button :url="route('customer.classes.index')">
Book Your First Class
</x-mail::button>

If you have any questions about your membership or need help getting started, our team is here to help.

Welcome aboard!

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
