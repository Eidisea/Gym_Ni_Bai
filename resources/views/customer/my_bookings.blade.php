@extends('layouts.customer')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full" x-data="{ tab: 'upcoming', cancelAction: '' }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">My Bookings</h1>
        <p class="mt-2 text-sm text-slate-600">View and manage your class reservations</p>
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

    <div class="mb-6 border-b border-slate-200">
        <nav class="flex space-x-8">
            <button @click="tab = 'upcoming'" :class="tab === 'upcoming' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-600 hover:text-slate-800 hover:border-slate-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition">
                Upcoming Classes
                <span class="ml-2 px-2 py-1 text-xs rounded-full" :class="tab === 'upcoming' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-600'">
                    {{ $upcoming->count() }}
                </span>
            </button>
            <button @click="tab = 'past'" :class="tab === 'past' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-600 hover:text-slate-800 hover:border-slate-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition">
                Past & Cancelled
                <span class="ml-2 px-2 py-1 text-xs rounded-full" :class="tab === 'past' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-600'">
                    {{ $past->count() }}
                </span>
            </button>
        </nav>
    </div>

    <div x-show="tab === 'upcoming'">
        @if($upcoming->count() > 0)
            <div class="space-y-4">
                @foreach($upcoming as $booking)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex-1">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-slate-900">
                                            {{ $booking->schedule?->fitnessClass?->class_name ?? 'Archived Class' }}
                                        </h3>
                                        <p class="text-sm text-slate-600 mt-1">
                                            <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $booking->schedule?->trainerProfile?->first_name ?? 'N/A' }} {{ $booking->schedule?->trainerProfile?->last_name ?? '' }}
                                        </p>
                                        <p class="text-sm text-slate-600 mt-1">
                                            <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($booking->schedule?->date)->format('l, M d, Y') }}
                                        </p>
                                        <p class="text-sm text-slate-600 mt-1">
                                            <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($booking->schedule?->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->schedule?->end_time)->format('g:i A') }}
                                        </p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 md:ml-6">
                                <button
                                    type="button"
                                    @click="cancelAction = '{{ route('customer.bookings.cancel', $booking) }}'; $dispatch('open-modal', 'confirm-cancel-booking')"
                                    class="px-4 py-2 border border-red-600 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg transition">
                                    Cancel Booking
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-xl font-semibold text-slate-900 mb-2">No Upcoming Bookings</h3>
                <p class="text-sm text-slate-600 mb-4">You don't have any upcoming class reservations.</p>
                <a href="{{ route('customer.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    Browse Classes
                </a>
            </div>
        @endif
    </div>

    <div x-show="tab === 'past'">
        @if($past->count() > 0)
            <div class="space-y-4">
                @foreach($past as $booking)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 opacity-75">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{ $booking->schedule?->fitnessClass?->class_name ?? 'Archived Class' }}
                                </h3>
                                <p class="text-sm text-slate-600 mt-1">
                                    <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $booking->schedule?->trainerProfile?->first_name ?? 'N/A' }} {{ $booking->schedule?->trainerProfile?->last_name ?? '' }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($booking->schedule?->date)->format('l, M d, Y') }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    <svg class="inline h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($booking->schedule?->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->schedule?->end_time)->format('g:i A') }}
                                </p>
                                @if($booking->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                                        Cancelled
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 mt-2">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-xl font-semibold text-slate-900 mb-2">No History</h3>
                <p class="text-sm text-slate-600">You don't have any past or cancelled bookings.</p>
            </div>
        @endif
    </div>

    {{-- Cancel Booking Confirmation Modal --}}
    <x-modal name="confirm-cancel-booking" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 mr-4">
                    <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Cancel Booking</h3>
            </div>

            <p class="text-sm text-slate-600 mb-1">Are you sure you want to cancel this booking?</p>
            <p class="text-sm text-slate-500">This cannot be undone. Your slot will be released and a confirmation email will be sent.</p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'confirm-cancel-booking')"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Keep Booking
                </button>

                <form method="POST" :action="cancelAction">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        Yes, Cancel It
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

</div>
@endsection