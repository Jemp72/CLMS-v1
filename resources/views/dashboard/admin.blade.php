@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-primary text-2xl mb-1">Admin Dashboard</h1>
        <p class="text-muted text-sm">Overview of all system activities and resources</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Total Students</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ number_format($totalStudents) }}</p>
                </div>
                <x-icon name="users" class="w-10 h-10 text-primary" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Active Now</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ number_format($activeNow) }}</p>
                </div>
                <x-icon name="activity" class="w-10 h-10 text-success" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Total Equipment</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ number_format($totalEquipment) }}</p>
                </div>
                <x-icon name="package" class="w-10 h-10 text-primary" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Low Stock Items</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ number_format($lowStockCount) }}</p>
                </div>
                <x-icon name="alert-circle" class="w-10 h-10 text-warning" />
            </div>
        </div>
    </div>

    {{-- Two-Column Panel --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Activity Logs --}}
        <div class="bg-white rounded-lg border border-black/10 shadow-sm">
            <div class="p-6 border-b border-black/10">
                <div class="flex items-center gap-2">
                    <x-icon name="activity" class="w-5 h-5 text-primary" />
                    <h3 class="text-primary text-base">Recent Activity Logs</h3>
                </div>
            </div>
            <div class="divide-y divide-black/5">
                @forelse ($activityLogs as $log)
                    <div class="p-4 hover:bg-surface transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($log['status'] === 'success')
                                    <x-icon name="check-circle" class="w-4 h-4 text-success" />
                                @elseif ($log['status'] === 'warning')
                                    <x-icon name="alert-circle" class="w-4 h-4 text-warning" />
                                @else
                                    <x-icon name="clock" class="w-4 h-4 text-muted" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-[#2c2c2c] mb-0.5">{{ $log['action'] }}</p>
                                <p class="text-xs text-muted">{{ $log['user'] }}</p>
                            </div>
                            <span class="text-xs text-muted flex-shrink-0">{{ $log['timestamp'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-icon name="activity" class="w-10 h-10 text-muted mx-auto mb-2 opacity-30" />
                        <p class="text-sm text-muted">No activity yet</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-black/10 bg-surface text-center">
                <a href="{{ route('logbook') }}" class="text-sm text-primary hover:underline">View All Activity Logs</a>
            </div>
        </div>

        {{-- Inventory Alerts --}}
        <div class="bg-white rounded-lg border border-black/10 shadow-sm">
            <div class="p-6 border-b border-black/10">
                <div class="flex items-center gap-2">
                    <x-icon name="package" class="w-5 h-5 text-primary" />
                    <h3 class="text-primary text-base">Inventory Alerts</h3>
                </div>
            </div>
            <div class="divide-y divide-black/5">
                @forelse ($lowStockItems as $item)
                    <div class="p-4 hover:bg-surface transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0 mr-3">
                                <p class="text-sm text-[#2c2c2c] mb-0.5">{{ $item['item'] }}</p>
                                <p class="text-xs text-muted">{{ $item['category'] }}</p>
                            </div>
                            @if ($item['status'] === 'critical')
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium flex-shrink-0">
                                    {{ $item['label'] }}
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 bg-warning/20 text-[#7a5d00] rounded text-xs font-medium flex-shrink-0">
                                    {{ $item['label'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-icon name="check-circle" class="w-10 h-10 text-success mx-auto mb-2 opacity-50" />
                        <p class="text-sm text-muted">All supplies are well-stocked</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-black/10 bg-surface text-center">
                <a href="{{ route('inventory') }}" class="text-sm text-primary hover:underline">View Full Inventory</a>
            </div>
        </div>

    </div>

</div>
@endsection