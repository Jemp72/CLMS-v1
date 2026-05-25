@extends('layouts.app')

@section('title', 'Class Lists')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-baseline gap-3">
                <h1 class="text-primary text-2xl">Class Lists</h1>
                <span class="text-sm text-muted">
                    <span class="font-semibold text-primary">{{ $schedules->count() }}</span>
                    {{ Str::plural('class', $schedules->count()) }}
                </span>
            </div>
            <p class="text-muted text-sm mt-1">View enrolled students for every class schedule</p>
        </div>
        <a href="{{ route('enrollments.import') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-white border border-black/10 text-[#2c2c2c] rounded-lg hover:bg-surface transition-colors text-sm font-medium">
            <x-icon name="plus" class="w-4 h-4" />
            Import Class List
        </a>
    </div>

    {{-- Class list cards --}}
    @if ($schedules->isEmpty())
        <div class="bg-white rounded-lg border border-black/10 shadow-sm py-16 text-center">
            <x-icon name="calendar" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
            <p class="text-muted text-sm">No class schedules yet</p>
            <a href="{{ route('schedule.create') }}" class="inline-block mt-2 text-xs text-primary hover:underline">
                Add a class schedule →
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($schedules as $schedule)
                @php
                    $course     = optional($schedule->course);
                    $instructor = optional($schedule->instructor);
                    $lab        = optional($schedule->laboratory);
                    $term       = optional($schedule->term);
                    $enrollments = $schedule->enrollments;
                @endphp

                <div x-data="{ expanded: false }"
                     class="bg-white rounded-lg border border-black/10 shadow-sm overflow-hidden">

                    {{-- Header row (clickable) --}}
                    <button type="button" @click="expanded = !expanded"
                            class="w-full text-left p-5 hover:bg-surface transition-colors flex items-center gap-4">
                        <div class="flex-1">
                            <h3 class="text-primary text-base font-semibold">
                                {{ $course->course_code ?? '?' }} — {{ $course->course_name ?? 'Untitled' }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted mt-1">
                                <span class="flex items-center gap-1">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    {{ $schedule->day_of_week }}
                                    · {{ \Carbon\Carbon::parse($schedule->time_start)->format('g:i A') }}
                                    – {{ \Carbon\Carbon::parse($schedule->time_end)->format('g:i A') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5" />
                                    {{ $lab->lab_name ?? '—' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                    {{ trim(($instructor->first_name ?? '') . ' ' . ($instructor->last_name ?? '')) ?: '—' }}
                                </span>
                                @if ($term)
                                    <span class="flex items-center gap-1 text-[#7a5d00]">
                                        {{ $term->academic_year }} · {{ ucfirst($term->semester) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold">
                                {{ $enrollments->count() }} {{ Str::plural('student', $enrollments->count()) }}
                            </span>
                            <x-icon name="chevron-down" class="w-5 h-5 text-muted transition-transform"
                                    x-bind:class="{ 'rotate-180': expanded }" />
                        </div>
                    </button>

                    {{-- Expanded list --}}
                    <div x-show="expanded" x-cloak class="border-t border-black/10 bg-surface">
                        @if ($enrollments->isEmpty())
                            <div class="p-6 text-center">
                                <p class="text-sm text-muted">No students enrolled yet.</p>
                                <a href="{{ route('enrollments.import') }}"
                                   class="inline-block mt-2 text-xs text-primary hover:underline">
                                    Import a CSV class list →
                                </a>
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="bg-white border-b border-black/10">
                                    <tr class="text-left text-xs font-semibold text-muted uppercase tracking-wide">
                                        <th class="px-5 py-2 w-40">Student No.</th>
                                        <th class="px-5 py-2">Name</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5">
                                    @foreach ($enrollments->sortBy(fn($e) => optional($e->student)->last_name) as $enrollment)
                                        @php $s = $enrollment->student; @endphp
                                        <tr class="hover:bg-white transition-colors">
                                            <td class="px-5 py-2 font-mono text-xs text-[#2c2c2c]">
                                                {{ $s->student_id ?? '—' }}
                                            </td>
                                            <td class="px-5 py-2 text-[#2c2c2c]">
                                                {{ trim(($s->last_name ?? '') . ', ' . ($s->first_name ?? '') . ' ' . ($s->middle_name ?? '') . ' ' . ($s->suffix ?? '')) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
