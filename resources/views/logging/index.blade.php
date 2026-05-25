@extends('layouts.public')

@section('title', 'Logging Terminal')

@section('content')
<div x-data='{ mode: "{{ old("_mode", "student_in") }}" }'>

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-primary text-3xl mb-2">Laboratory Logging Terminal</h1>
        <p class="text-muted text-sm">
            Students sign in with your student number. Visitors enter your name and purpose.
        </p>
    </div>

    {{-- Success / error flash --}}
    @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-success/10 border border-success/30 text-sm text-success">
            <div class="flex items-center gap-2">
                <x-icon name="calendar" class="w-5 h-5" />
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Action tabs --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm overflow-hidden">

        <div class="flex border-b border-black/10">
            <button type="button"
                    @click="mode = 'student_in'"
                    :class="mode === 'student_in' ? 'bg-primary text-white' : 'bg-white text-muted hover:bg-surface'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors">
                Student Time In
            </button>
            <button type="button"
                    @click="mode = 'guest_in'"
                    :class="mode === 'guest_in' ? 'bg-primary text-white' : 'bg-white text-muted hover:bg-surface'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors border-l border-black/10">
                Visitor Time In
            </button>
            <button type="button"
                    @click="mode = 'time_out'"
                    :class="mode === 'time_out' ? 'bg-primary text-white' : 'bg-white text-muted hover:bg-surface'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors border-l border-black/10">
                Time Out
            </button>
        </div>

        {{-- ─── STUDENT TIME IN ─── --}}
        <div x-show="mode === 'student_in'" class="p-6">
            <form method="POST" action="{{ route('logging.student.time-in') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_mode" value="student_in">

                <div>
                    <label for="si_student_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Student Number <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="si_student_id"
                           name="student_id"
                           value="{{ old('_mode') === 'student_in' ? old('student_id') : '' }}"
                           placeholder="e.g. 2024-00123"
                           required
                           autofocus
                           class="w-full px-4 py-4 border @error('student_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('student_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-muted">
                    The laboratory, course, and instructor will be detected from your active class schedule.
                </p>

                <button type="submit"
                        class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-base font-medium">
                    Sign In
                </button>
            </form>
        </div>

        {{-- ─── GUEST TIME IN ─── --}}
        <div x-show="mode === 'guest_in'" x-cloak class="p-6">
            <form method="POST" action="{{ route('logging.guest.time-in') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_mode" value="guest_in">

                <div>
                    <label for="g_booked_under" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Reservation Booked Under <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="g_booked_under"
                           name="booked_under"
                           value="{{ old('_mode') === 'guest_in' ? old('booked_under') : '' }}"
                           placeholder="Name on the reservation (e.g. your group leader)"
                           required
                           class="w-full px-4 py-4 border @error('booked_under') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('booked_under')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="g_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Your Full Name <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="g_name"
                           name="guest_name"
                           value="{{ old('_mode') === 'guest_in' ? old('guest_name') : '' }}"
                           placeholder="Your own name — for individual attendance"
                           required
                           class="w-full px-4 py-4 border @error('guest_name') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('guest_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-muted">
                    Each attendee signs in with the same reservation name but their own full name.
                    Don't have a reservation?
                    <a href="{{ route('bookings.create') }}" class="text-primary underline">Book one here</a>.
                </p>

                <button type="submit"
                        class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-base font-medium">
                    Sign In as Visitor
                </button>
            </form>
        </div>

        {{-- ─── TIME OUT (unified) ─── --}}
        <div x-show="mode === 'time_out'" x-cloak class="p-6">
            <form method="POST" action="{{ route('logging.time-out') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_mode" value="time_out">

                <div>
                    <label for="to_identifier" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Student Number or Full Name <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="to_identifier"
                           name="identifier"
                           value="{{ old('_mode') === 'time_out' ? old('identifier') : '' }}"
                           placeholder="Students: e.g. 2024-00123    Visitors: your full name"
                           required
                           class="w-full px-4 py-4 border @error('identifier') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('identifier')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-muted">
                    This will end your current laboratory session.
                </p>

                <button type="submit"
                        class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-base font-medium">
                    Sign Out
                </button>
            </form>
        </div>

    </div>

    {{-- Footer hint --}}
    <p class="text-center text-xs text-muted mt-6">
        Need help? Ask a laboratory staff member.
    </p>

</div>
@endsection
