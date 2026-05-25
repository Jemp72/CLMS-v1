@extends('layouts.app')

@section('title', 'Add Class Schedule')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Add Class Schedule</h1>
            <p class="text-muted text-sm">Schedule a recurring weekly class for a laboratory</p>
        </div>
        <a href="{{ route('schedule') }}"
           class="flex items-center gap-2 px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-muted">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back to Calendar
        </a>
    </div>

    {{-- Form card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm p-6">

        <form method="POST" action="{{ route('schedule.store') }}" class="space-y-4">
            @csrf

            {{-- General error (conflict) --}}
            @if ($errors->has('general'))
                <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first('general') }}
                </div>
            @endif

            {{-- Warning if dropdowns are empty --}}
            @if ($courses->isEmpty() || $instructors->isEmpty() || $laboratories->isEmpty() || $terms->isEmpty())
                <div class="px-4 py-3 rounded-lg bg-warning/10 border border-warning/30 text-sm text-[#7a5d00]">
                    <strong>Heads up:</strong> Some dropdowns are empty. Make sure you have at least one
                    @if ($courses->isEmpty()) <span class="font-medium">course</span>, @endif
                    @if ($instructors->isEmpty()) <span class="font-medium">instructor</span>, @endif
                    @if ($laboratories->isEmpty()) <span class="font-medium">laboratory</span>, @endif
                    @if ($terms->isEmpty()) <span class="font-medium">academic term</span> @endif
                    registered in the system before adding a schedule.
                </div>
            @endif

            {{-- Course --}}
            <div>
                <label for="course_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Course <span class="text-primary">*</span>
                </label>
                <select id="course_id" name="course_id" required
                        class="w-full px-4 py-3 border @error('course_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">Select a course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->course_id }}" @selected(old('course_id') == $course->course_id)>
                            {{ $course->course_code }} — {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Instructor --}}
            <div>
                <label for="instructor_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Assigned Instructor <span class="text-primary">*</span>
                </label>
                <select id="instructor_id" name="instructor_id" required
                        class="w-full px-4 py-3 border @error('instructor_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">Select an instructor</option>
                    @foreach ($instructors as $instructor)
                        <option value="{{ $instructor->system_user_id }}" @selected(old('instructor_id') == $instructor->system_user_id)>
                            {{ $instructor->last_name }}, {{ $instructor->first_name }}
                        </option>
                    @endforeach
                </select>
                @error('instructor_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lab + Term --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="lab_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Target Laboratory <span class="text-primary">*</span>
                    </label>
                    <select id="lab_id" name="lab_id" required
                            class="w-full px-4 py-3 border @error('lab_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
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

                <div>
                    <label for="term_id" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Academic Term <span class="text-primary">*</span>
                    </label>
                    <select id="term_id" name="term_id" required
                            class="w-full px-4 py-3 border @error('term_id') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="">Select a term</option>
                        @foreach ($terms as $term)
                            <option value="{{ $term->academic_term_id }}" @selected(old('term_id') == $term->academic_term_id)>
                                {{ $term->academic_year }} — {{ ucfirst($term->semester) }} semester
                            </option>
                        @endforeach
                    </select>
                    @error('term_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Day + Time --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="day_of_week" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Day of Week <span class="text-primary">*</span>
                    </label>
                    <select id="day_of_week" name="day_of_week" required
                            class="w-full px-4 py-3 border @error('day_of_week') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="">Select a day</option>
                        @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <option value="{{ $day }}" @selected(old('day_of_week') == $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_start" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Start Time <span class="text-primary">*</span>
                    </label>
                    <input type="time" id="time_start" name="time_start" value="{{ old('time_start') }}" required
                           class="w-full px-4 py-3 border @error('time_start') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    @error('time_start')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_end" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        End Time <span class="text-primary">*</span>
                    </label>
                    <input type="time" id="time_end" name="time_end" value="{{ old('time_end') }}" required
                           class="w-full px-4 py-3 border @error('time_end') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    @error('time_end')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('schedule') }}"
                   class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                    Add Schedule
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
