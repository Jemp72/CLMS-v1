@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-primary text-2xl mb-1">Activity Logs</h1>
        <p class="text-muted text-sm">Sign-in and sign-out events across all laboratories</p>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm">

        {{-- Filter Toolbar --}}
        <div class="p-5 border-b border-black/10">
            <form method="GET" action="{{ route('activity-logs') }}"
                  class="flex flex-wrap items-center gap-3">

                {{-- Specific date --}}
                <input type="date" name="date"
                       value="{{ request('date') }}"
                       class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer" />

                {{-- Time window --}}
                <select name="filter"
                        class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none cursor-pointer">
                    <option value="">All time</option>
                    <option value="day"   @selected(request('filter') === 'day')>Today</option>
                    <option value="week"  @selected(request('filter') === 'week')>This week</option>
                    <option value="month" @selected(request('filter') === 'month')>This month</option>
                </select>

                {{-- Lab --}}
                <select name="lab_id"
                        class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none cursor-pointer">
                    <option value="">All labs</option>
                    @foreach ($laboratories as $lab)
                        <option value="{{ $lab->lab_id }}" @selected((int) request('lab_id') === $lab->lab_id)>
                            {{ $lab->lab_name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                    Apply
                </button>

                @if (request()->hasAny(['date', 'filter', 'lab_id']))
                    <a href="{{ route('activity-logs') }}"
                       class="px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-muted">
                        Clear
                    </a>
                @endif

            </form>
        </div>

        {{-- Feed --}}
        <div class="divide-y divide-black/5">
            @forelse ($entries as $entry)
                <div class="px-5 py-4 hover:bg-surface transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            @if ($entry['status'] === 'success')
                                <x-icon name="check-circle" class="w-4 h-4 text-success" />
                            @else
                                <x-icon name="clock" class="w-4 h-4 text-muted" />
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-[#2c2c2c] mb-0.5">{{ $entry['action'] }}</p>
                            <p class="text-xs text-muted">{{ $entry['user'] }}</p>
                        </div>
                        <span class="text-xs text-muted flex-shrink-0 whitespace-nowrap">{{ $entry['timestamp'] }}</span>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <x-icon name="activity" class="w-10 h-10 text-muted mx-auto mb-3 opacity-30" />
                    <p class="text-sm text-muted">No activity found</p>
                    @if (request()->hasAny(['date', 'filter', 'lab_id']))
                        <a href="{{ route('activity-logs') }}" class="inline-block mt-2 text-xs text-primary hover:underline">
                            Clear filters
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-black/10 flex items-center justify-between">
            <p class="text-sm text-muted">
                Showing {{ $logs->count() }} of {{ $logs->total() }} entries
            </p>
            <div>
                {{ $logs->links() }}
            </div>
        </div>

    </div>

</div>
@endsection
