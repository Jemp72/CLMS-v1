@extends('layouts.app')

@section('title', 'Logbook')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-primary text-2xl mb-1">Logbook</h1>
        <p class="text-muted text-sm">
            Track laboratory usage and sign-in history
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-lg bg-success/10 border border-success/30 text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm">

        {{-- Toolbar — single unified filter form + export link --}}
        <div class="p-6 border-b border-black/10 flex items-center gap-3 flex-wrap">

            <form method="GET" action="{{ route('logbook') }}"
                  class="flex items-center gap-3 flex-wrap flex-1">

                {{-- Search --}}
                <div class="relative flex-1 min-w-[200px] max-w-md">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by Student ID, name, or purpose..."
                           class="w-full pl-10 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>

                {{-- Time window --}}
                <select name="filter"
                        class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm
                               focus:border-primary focus:outline-none cursor-pointer">
                    <option value="">All time</option>
                    <option value="day"   @selected(request('filter') === 'day')>Today</option>
                    <option value="week"  @selected(request('filter') === 'week')>This week</option>
                    <option value="month" @selected(request('filter') === 'month')>This month</option>
                </select>

                {{-- Lab filter --}}
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

                @if (request()->hasAny(['search', 'filter', 'lab_id']))
                    <a href="{{ route('logbook') }}"
                       class="px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-muted">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Export link (preserves current filters) --}}
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
               class="flex items-center gap-2 px-4 py-2 bg-success text-white rounded-lg hover:bg-success-dark transition-colors text-sm font-medium">
                <x-icon name="download" class="w-4 h-4" />
                Export CSV
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-surface">
                    <tr class="text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Student ID</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Laboratory</th>
                        <th class="px-6 py-4">Purpose</th>
                        <th class="px-6 py-4">Time-in</th>
                        <th class="px-6 py-4">Time-out</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-black/5">

                    @forelse($entries as $entry)
                        <tr class="hover:bg-surface transition-colors">
                            {{-- Date --}}
                            <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">
                                {{ $entry['date'] }}
                            </td>

                            {{-- Student ID --}}
                            <td class="px-6 py-4 text-sm font-mono text-xs text-[#2c2c2c] whitespace-nowrap">
                                {{ $entry['studentId'] }}
                            </td>

                            {{-- Name --}}
                            <td class="px-6 py-4 text-sm font-medium text-[#2c2c2c]">
                                {{ $entry['name'] }}
                            </td>

                            {{-- Lab --}}
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-2 py-0.5 bg-primary text-white rounded text-xs font-medium">
                                    {{ $entry['laboratory'] }}
                                </span>
                            </td>

                            {{-- Purpose --}}
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]">
                                {{ $entry['purpose'] }}
                            </td>

                            {{-- Time In --}}
                            <td class="px-6 py-4 text-sm text-[#2c2c2c] whitespace-nowrap">
                                {{ $entry['timeIn'] }}
                            </td>

                            {{-- Time Out --}}
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @if($entry['timeOut'])
                                    <span class="text-[#2c2c2c]">{{ $entry['timeOut'] }}</span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-success text-white rounded text-xs font-medium">
                                        In Session
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-muted">
                                No entries found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-black/10 flex items-center justify-between">

            <p class="text-sm text-muted">

                Showing {{ $logs->count() }}
                of {{ $logs->total() }} entries

            </p>

            <div>

                {{ $logs->links() }}

            </div>

        </div>

    </div>

</div>
@endsection
