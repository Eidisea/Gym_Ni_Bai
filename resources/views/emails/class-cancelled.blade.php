<x-mail::message>
# Class Booking Cancelled

Hello {{ $customer->first_name }},

Your class booking has been cancelled as requested.

## Cancelled Booking Details

**Class:** {{ $fitnessClass->class_name }}  
**Date:** {{ $schedule->schedule_date->format('l, F j, Y') }}  
**Time:** {{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}  
**Trainer:** {{ $trainer->first_name }} {{ $trainer->last_name }}  

**Booking ID:** #{{ str_pad($booking->booking_id, 6, '0', STR_PAD_LEFT) }}  
**Status:** {{ $booking->status }}

## What's Next?

Don't worry - you can book another class anytime! Browse our available classes and find one that fits your schedule.

<x-mail::button :url="route('customer.classes.index')">
Browse Classes
</x-mail::button>

If you have any questions about this cancellation, please don't hesitate to contact us.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
