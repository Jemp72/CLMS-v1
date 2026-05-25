@extends('layouts.public')

@section('title', 'Request a Reservation')

@section('content')

{{-- Success message after successful submission --}}
@if (session('success'))
    <div class="mb-6 bg-white border border-success/30 rounded-lg shadow-sm p-6">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center flex-shrink-0">
                <x-icon name="calendar" class="w-5 h-5 text-success" />
            </div>
            <div class="flex-1">
                <h2 class="text-primary text-base font-semibold mb-1">Reservation request submitted</h2>
                <p class="text-sm text-muted">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-primary text-2xl mb-1">Request a Laboratory Reservation</h1>
    <p class="text-muted text-sm">
        Fill out the form below to request a laboratory booking.
        Your request will be reviewed by the administrator.
    </p>
</div>

{{-- Form card --}}
<div class="bg-white rounded-lg border border-black/10 shadow-sm p-6">

    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
        @csrf

        {{-- Requestor's Name --}}
        <div>
            <label for="requestor_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                Full Name <span class="text-primary">*</span>
            </label>
            <input type="text"
                   id="requestor_name"
                   name="requestor_name"
                   value="{{ old('requestor_name') }}"
                   placeholder="Enter your full name"
                   required
                   class="w-full px-4 py-3 border @error('requestor_name') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('requestor_name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contact Information --}}
        <div>
            <label for="contact_number" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                Contact Information
            </label>
            <input type="text"
                   id="contact_number"
                   name="contact_number"
                   value="{{ old('contact_number') }}"
                   placeholder="Email or phone number"
                   class="w-full px-4 py-3 border @error('contact_number') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('contact_number')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Purpose --}}
        <div>
            <label for="purpose" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                Purpose <span class="text-primary">*</span>
            </label>
            <textarea id="purpose"
                      name="purpose"
                      rows="3"
                      placeholder="Briefly describe the purpose of your reservation"
                      required
                      class="w-full px-4 py-3 border @error('purpose') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                             focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none">{{ old('purpose') }}</textarea>
            @error('purpose')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date + Lab --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="booking_date" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Preferred Date <span class="text-primary">*</span>
                </label>
                <input type="date"
                       id="booking_date"
                       name="booking_date"
                       value="{{ old('booking_date') }}"
                       min="{{ date('Y-m-d') }}"
                       required
                       class="w-full px-4 py-3 border @error('booking_date') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                @error('booking_date')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lab_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Target Laboratory <span class="text-primary">*</span>
                </label>
                <select id="lab_id"
                        name="lab_id"
                        required
                        class="w-full px-4 py-3 border @error('lab_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                    <option value="">Select a laboratory</option>
                    @foreach ($laboratories as $lab)
                        <option value="{{ $lab->lab_id }}" @selected(old('lab_id') == $lab->lab_id)>
                            {{ $lab->lab_name }}{{ $lab->location ? ' — ' . $lab->location : '' }}
                        </option>
                    @endforeach
                </select>
                @error('lab_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Time slot --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="start_time" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Start Time <span class="text-primary">*</span>
                </label>
                <input type="time"
                       id="start_time"
                       name="start_time"
                       value="{{ old('start_time') }}"
                       required
                       class="w-full px-4 py-3 border @error('start_time') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                @error('start_time')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_time" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    End Time <span class="text-primary">*</span>
                </label>
                <input type="time"
                       id="end_time"
                       name="end_time"
                       value="{{ old('end_time') }}"
                       required
                       class="w-full px-4 py-3 border @error('end_time') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                @error('end_time')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- General error (e.g. time conflict) --}}
        @if ($errors->has('general'))
            <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                {{ $errors->first('general') }}
            </div>
        @endif

        {{-- Submit --}}
        <div class="pt-2">
            <button type="submit"
                    class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                Submit Reservation Request
            </button>
            <p class="text-xs text-muted text-center mt-3">
                <span class="text-primary">*</span> Required fields
            </p>
        </div>

    </form>
</div>

@endsection
