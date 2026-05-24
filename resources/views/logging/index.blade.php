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
                    @click="mode = 'student_out'"
                    :class="mode === 'student_out' ? 'bg-primary text-white' : 'bg-white text-muted hover:bg-surface'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors border-l border-black/10">
                Student Time Out
            </button>
            <button type="button"
                    @click="mode = 'guest_in'"
                    :class="mode === 'guest_in' ? 'bg-primary text-white' : 'bg-white text-muted hover:bg-surface'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors border-l border-black/10">
                Visitor Time In
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

                <div>
                    <label for="si_lab_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Laboratory <span class="text-primary">*</span>
                    </label>
                    <select id="si_lab_id" name="lab_id" required
                            class="w-full px-4 py-4 border @error('lab_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="">Select a laboratory</option>
                        @foreach ($laboratories as $lab)
                            <option value="{{ $lab->lab_id }}"
                                    @selected(old('_mode') === 'student_in' && old('lab_id') == $lab->lab_id)>
                                {{ $lab->lab_name }}{{ $lab->location ? ' — ' . $lab->location : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('lab_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="si_purpose" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Purpose <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="si_purpose"
                           name="purpose"
                           value="{{ old('_mode') === 'student_in' ? old('purpose') : '' }}"
                           placeholder="e.g. CS 101 class, Research, Homework"
                           required
                           class="w-full px-4 py-4 border @error('purpose') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('purpose')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-base font-medium">
                    Sign In
                </button>
            </form>
        </div>

        {{-- ─── STUDENT TIME OUT ─── --}}
        <div x-show="mode === 'student_out'" x-cloak class="p-6">
            <form method="POST" action="{{ route('logging.student.time-out') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_mode" value="student_out">

                <div>
                    <label for="so_student_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Student Number <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="so_student_id"
                           name="student_id"
                           value="{{ old('_mode') === 'student_out' ? old('student_id') : '' }}"
                           placeholder="e.g. 2024-00123"
                           required
                           class="w-full px-4 py-4 border @error('student_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('student_id')
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

        {{-- ─── GUEST TIME IN ─── --}}
        <div x-show="mode === 'guest_in'" x-cloak class="p-6">
            <form method="POST" action="{{ route('logging.guest.time-in') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_mode" value="guest_in">

                <div>
                    <label for="g_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Full Name <span class="text-primary">*</span>
                    </label>
                    <input type="text"
                           id="g_name"
                           name="guest_name"
                           value="{{ old('_mode') === 'guest_in' ? old('guest_name') : '' }}"
                           placeholder="Enter your full name"
                           required
                           class="w-full px-4 py-4 border @error('guest_name') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('guest_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="g_org" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                            Organization
                        </label>
                        <input type="text"
                               id="g_org"
                               name="organization"
                               value="{{ old('_mode') === 'guest_in' ? old('organization') : '' }}"
                               placeholder="Optional"
                               class="w-full px-4 py-4 border border-black/10 rounded-lg bg-surface text-base
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label for="g_contact" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                            Contact Number
                        </label>
                        <input type="text"
                               id="g_contact"
                               name="contact_number"
                               value="{{ old('_mode') === 'guest_in' ? old('contact_number') : '' }}"
                               placeholder="Optional"
                               class="w-full px-4 py-4 border border-black/10 rounded-lg bg-surface text-base
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>

                <div>
                    <label for="g_lab" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Laboratory <span class="text-primary">*</span>
                    </label>
                    <select id="g_lab" name="lab_id" required
                            class="w-full px-4 py-4 border @error('lab_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="">Select a laboratory</option>
                        @foreach ($laboratories as $lab)
                            <option value="{{ $lab->lab_id }}"
                                    @selected(old('_mode') === 'guest_in' && old('lab_id') == $lab->lab_id)>
                                {{ $lab->lab_name }}{{ $lab->location ? ' — ' . $lab->location : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('lab_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="g_purpose" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Purpose of Visit <span class="text-primary">*</span>
                    </label>
                    <textarea id="g_purpose"
                              name="purpose"
                              rows="2"
                              placeholder="Briefly describe your reason for visiting"
                              required
                              class="w-full px-4 py-4 border @error('purpose') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-base
                                     focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none">{{ old('_mode') === 'guest_in' ? old('purpose') : '' }}</textarea>
                    @error('purpose')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full px-6 py-4 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-base font-medium">
                    Sign In as Visitor
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
