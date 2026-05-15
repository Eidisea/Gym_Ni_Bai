<x-mail::message>
# Class Booking Confirmed! 🎉

Hello {{ $customer->first_name }},

Great news! Your class booking has been confirmed. We're excited to see you at the gym!

## Booking Details

**Class:** {{ $fitnessClass->class_name }}  
**Date:** {{ $schedule->schedule_date->format('l, F j, Y') }}  
**Time:** {{ $schedule->start_time->format('g:i A') }} - {{ $schedule->end_time->format('g:i A') }}  
**Trainer:** {{ $trainer->first_name }} {{ $trainer->last_name }}  
**Location:** {{ $schedule->location }}  

**Booking ID:** #{{ str_pad($booking->booking_id, 6, '0', STR_PAD_LEFT) }}  
**Status:** {{ $booking->status }}

## What to Bring

- Comfortable workout clothes
- Water bottle
- Towel
- Positive attitude!

## Important Notes

- Please arrive 10 minutes early for check-in
- If you need to cancel, please do so at least 2 hours before class time
- Contact us if you have any questions or concerns

<x-mail::button :url="route('customer.bookings.index')">
View My Bookings
</x-mail::button>

We look forward to seeing you in class!

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
