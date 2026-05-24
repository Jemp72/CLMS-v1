@extends('layouts.app')

@section('title', 'Import Class List')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Import Class List</h1>
            <p class="text-muted text-sm">Bulk-enroll students into a class schedule using a CSV file</p>
        </div>
        <a href="{{ route('enrollments.index') }}"
           class="flex items-center gap-2 px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-muted">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back to Class Lists
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-success/10 border border-success/30 text-sm text-success">
            {{ session('success') }}
        </div>

        @if (!empty(session('row_errors')))
            <div class="bg-white rounded-lg border border-warning/40 shadow-sm p-4">
                <p class="text-xs font-semibold text-[#7a5d00] uppercase tracking-wide mb-2">Row warnings</p>
                <ul class="space-y-1 text-sm text-[#2c2c2c]">
                    @foreach (session('row_errors') as $rowError)
                        <li>• {{ $rowError }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- CSV format hint --}}
    <div class="bg-surface border border-black/10 rounded-lg p-4">
        <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-2">CSV format</p>
        <p class="text-sm text-[#2c2c2c] mb-2">
            The first row must be a header. Required columns:
            <code class="px-1 py-0.5 bg-white rounded text-xs">student_id</code>,
            <code class="px-1 py-0.5 bg-white rounded text-xs">first_name</code>,
            <code class="px-1 py-0.5 bg-white rounded text-xs">last_name</code>.
            Optional:
            <code class="px-1 py-0.5 bg-white rounded text-xs">middle_name</code>,
            <code class="px-1 py-0.5 bg-white rounded text-xs">suffix</code>.
        </p>
        <pre class="text-xs bg-white border border-black/5 rounded p-3 overflow-x-auto"><code>student_id,first_name,last_name,middle_name,suffix
2024-00123,Maria,Lopez,Cruz,
2024-00456,Juan,Cruz,,
2024-00789,Pedro,Garcia,Santos,Jr.</code></pre>
    </div>

    {{-- Form card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm p-6">

        @if ($schedules->isEmpty())
            <div class="px-4 py-3 rounded-lg bg-warning/10 border border-warning/30 text-sm text-[#7a5d00]">
                <strong>No class schedules exist yet.</strong>
                Please <a href="{{ route('schedule.create') }}" class="underline text-primary">add a class schedule</a> first.
            </div>
        @else
            <form method="POST" action="{{ route('enrollments.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div x-data="{
                        all: false,
                        toggleAll() {
                            this.all = !this.all;
                            this.$refs.list.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = this.all);
                        }
                     }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-[#2c2c2c]">
                            Class Schedules <span class="text-primary">*</span>
                            <span class="text-xs text-muted font-normal ml-1">(pick one or more)</span>
                        </label>
                        <button type="button" @click="toggleAll()"
                                class="text-xs text-primary hover:underline">
                            <span x-text="all ? 'Deselect all' : 'Select all'"></span>
                        </button>
                    </div>

                    <div x-ref="list"
                         class="space-y-1 max-h-64 overflow-y-auto border @error('class_schedule_ids') border-red-400 @else border-black/10 @enderror rounded-lg p-2 bg-surface">
                        @php $selectedOld = (array) old('class_schedule_ids', []); @endphp
                        @foreach ($schedules as $schedule)
                            @php
                                $course     = optional($schedule->course);
                                $instructor = optional($schedule->instructor);
                                $lab        = optional($schedule->laboratory);
                                $title      = trim(($course->course_code ?? '?') . ' — ' . ($course->course_name ?? 'Untitled'));
                                $meta       = $schedule->day_of_week
                                              . ' · ' . \Carbon\Carbon::parse($schedule->time_start)->format('g:i A')
                                              . '–' . \Carbon\Carbon::parse($schedule->time_end)->format('g:i A')
                                              . ' · ' . ($lab->lab_name ?? '—')
                                              . ' · ' . trim(($instructor->first_name ?? '') . ' ' . ($instructor->last_name ?? '—'));
                                $isChecked = in_array((string) $schedule->class_schedule_id, array_map('strval', $selectedOld), true);
                            @endphp
                            <label class="flex items-start gap-2 cursor-pointer hover:bg-white p-2 rounded transition-colors">
                                <input type="checkbox"
                                       name="class_schedule_ids[]"
                                       value="{{ $schedule->class_schedule_id }}"
                                       @checked($isChecked)
                                       class="mt-1 cursor-pointer accent-primary" />
                                <span class="flex-1 text-sm">
                                    <span class="text-primary font-medium">{{ $title }}</span>
                                    <span class="block text-xs text-muted mt-0.5">{{ $meta }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('class_schedule_ids')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @error('class_schedule_ids.*')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="csv_file" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        CSV File <span class="text-primary">*</span>
                    </label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                           class="w-full px-4 py-3 border @error('csv_file') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                  file:mr-4 file:px-4 file:py-2 file:rounded file:border-0 file:bg-primary file:text-white file:text-sm file:font-medium file:cursor-pointer hover:file:bg-primary-dark
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    @error('csv_file')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-muted mt-1">
                        Max size: 2 MB. Students that don't exist yet will be created automatically.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('enrollments.index') }}"
                       class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                        Import Class List
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>
@endsection
