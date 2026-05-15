@extends('layouts.customer')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Find a Class</h1>
        <p class="mt-2 text-sm text-slate-500">Browse upcoming sessions and reserve your spot</p>
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

    {{-- No membership warning --}}
    @if(!$hasActiveMembership)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start">
            <svg class="h-5 w-5 text-amber-600 mr-3 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800">No active membership</p>
                <p class="text-sm text-amber-700 mt-1">You need an active membership plan to book classes. <a href="{{ route('customer.billing.index') }}" class="font-semibold underline hover:text-amber-900">Get a plan →</a></p>
            </div>
        </div>
    @endif

    @if($schedules->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schedules as $schedule)
                @php
                    $isBooked   = in_array($schedule->schedule_id, $userBookings);
                    $isFull     = $schedule->available_slots <= 0;
                    $start      = \Carbon\Carbon::parse($schedule->start_time);
                    $end        = \Carbon\Carbon::parse($schedule->end_time);
                    $duration   = $start->diffInMinutes($end);
                    $isToday    = $start->isToday();
                    $isTomorrow = $start->isTomorrow();
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Coloured top strip --}}
                    <div class="h-1.5 {{ $isBooked ? 'bg-green-500' : ($isFull ? 'bg-red-400' : 'bg-indigo-500') }}"></div>

                    <div class="p-5 flex flex-col flex-1">

                        {{-- Class name + day badge --}}
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                {{ $schedule->fitnessClass?->class_name ?? 'Archived Class' }}
                            </h3>
                            @if($isToday)
                                <span class="flex-shrink-0 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">Today</span>
                            @elseif($isTomorrow)
                                <span class="flex-shrink-0 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Tomorrow</span>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($schedule->fitnessClass?->description)
                            <p class="text-xs text-slate-500 mb-3 leading-relaxed line-clamp-2">
                                {{ $schedule->fitnessClass->description }}
                            </p>
                        @endif

                        {{-- Meta info --}}
                        <div class="space-y-1.5 text-sm text-slate-600 mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $schedule->trainerProfile?->first_name ?? 'N/A' }} {{ $schedule->trainerProfile?->last_name ?? '' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $start->format('l, M j, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}
                                    <span class="text-slate-400">({{ $duration }} min)</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex-1"></div>

                        {{-- Slots + Book button --}}
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            @if($isFull)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Full</span>
                            @elseif($isBooked)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Booked</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    {{ $schedule->available_slots }} slot{{ $schedule->available_slots === 1 ? '' : 's' }} left
                                </span>
                            @endif

                            <form method="POST" action="{{ route('customer.classes.book', $schedule) }}">
                                @csrf
                                @if($isBooked)
                                    <button type="button" disabled class="px-4 py-1.5 bg-slate-200 text-slate-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                        Already Booked
                                    </button>
                                @elseif($isFull)
                                    <button type="button" disabled class="px-4 py-1.5 bg-slate-200 text-slate-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                        Class Full
                                    </button>
                                @elseif(!$hasActiveMembership)
                                    <button type="button" disabled class="px-4 py-1.5 bg-slate-200 text-slate-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                        Membership Required
                                    </button>
                                @else
                                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                        Book Class
                                    </button>
                                @endif
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-slate-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">No Upcoming Classes</h3>
            <p class="text-sm text-slate-500">Check back soon — new sessions are added regularly.</p>
        </div>
    @endif

</div>
@endsection