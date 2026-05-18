@extends('layouts.management')

@section('title', 'Edit Class Schedule')
@section('subtitle', 'Update class schedule details')

@section('content')
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="{{ route('management.dashboard') }}" class="text-gray-400 hover:text-gray-100 text-sm">Dashboard</a>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('management.class-schedules.index') }}" class="text-gray-400 hover:text-gray-100 text-sm">Class Schedules</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-100 text-sm font-medium">Edit</span>
            </div>
        </li>
    </ol>
</nav>

<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-100">Edit Class Schedule</h1>
</div>

<div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
    <form method="POST" action="{{ route('management.class-schedules.update', $classSchedule) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Fitness Class <span class="text-red-400">*</span>
                </label>
                <select name="class_id" id="class_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select a fitness class</option>
                    @foreach($fitnessClasses as $class)
                        <option value="{{ $class->class_id }}" {{ old('class_id', $classSchedule->class_id) == $class->class_id ? 'selected' : '' }}>
                            {{ $class->class_name }} (Max: {{ $class->max_participants }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="trainer_id" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Trainer <span class="text-red-400">*</span>
                </label>
                <select name="trainer_id" id="trainer_id"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select a trainer</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->trainer_id }}" {{ old('trainer_id', $classSchedule->trainer_id) == $trainer->trainer_id ? 'selected' : '' }}>
                            {{ $trainer->full_name }} - {{ $trainer->specialization }}
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_time" class="block text-sm font-medium text-gray-300 mb-1.5">
                    Start Time <span class="text-red-400">*</span>
                </label>
                <input type="datetime-local" name="start_time" id="start_time" 
                       value="{{ old('start_time', $classSchedule->start_time->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('start_time')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_time" class="block text-sm font-medium text-gray-300 mb-1.5">
                    End Time <span class="text-red-400">*</span>
                </label>
                <input type="datetime-local" name="end_time" id="end_time" 
                       value="{{ old('end_time', $classSchedule->end_time->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('end_time')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">
                    Capacity
                </label>
                <div class="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-sm text-gray-300 cursor-not-allowed">
                    {{ $classSchedule->available_slots }} total slots (set by the fitness class)
                </div>
                <p class="mt-1 text-xs text-gray-400">
                    {{ $classSchedule->booked_slots }} booked · {{ $classSchedule->remaining_slots }} remaining.
                    Capacity is controlled by the fitness class's max participants setting.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-slate-600">
            <a href="{{ route('management.class-schedules.show', $classSchedule) }}" 
               class="px-3 py-1.5 text-sm text-gray-400 hover:text-gray-100">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                Update Schedule
            </button>
        </div>
    </form>
</div>
@endsection
