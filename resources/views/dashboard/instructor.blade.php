@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')

@php
    $presentCount = collect($students)->where('status', 'logged-in')->count();
    $absentCount = collect($students)->where('status', 'not-logged-in')->count();
    $totalCount = count($students);
    $attendanceRate = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-primary text-2xl mb-1">Instructor Dashboard</h1>
        <p class="text-muted text-sm">Overview of your students and class attendance</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Total Students</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ $totalCount }}</p>
                </div>
                <x-icon name="users" class="w-10 h-10 text-primary" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Present Today</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ $presentCount }}</p>
                </div>
                <x-icon name="user" class="w-10 h-10 text-success" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Absent Today</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ $absentCount }}</p>
                </div>
                <x-icon name="clock" class="w-10 h-10 text-muted" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-black/10 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-muted text-sm mb-1">Attendance Rate</p>
                    <p class="text-3xl font-heading font-semibold text-[#2c2c2c]">{{ $attendanceRate }}%</p>
                </div>
                <x-icon name="trending-up" class="w-10 h-10 text-success" />
            </div>
        </div>
    </div>

    {{-- Student Table --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm">
        <div class="p-6 border-b border-black/10">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-primary text-base mb-0.5">CS 101 - Introduction to Programming</h3>
                    <p class="text-sm text-muted">Student Status Overview</p>
                </div>
                <div class="flex gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-success rounded-full"></div>
                        <span class="text-muted">Present ({{ $presentCount }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                        <span class="text-muted">Not Present ({{ $absentCount }})</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Student ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Program</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($students as $student)
                        <tr class="hover:bg-surface transition-colors">
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]">{{ $student['studentId'] }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-[#2c2c2c]">{{ $student['name'] }}</td>
                            <td class="px-6 py-4 text-sm text-muted">{{ $student['email'] }}</td>
                            <td class="px-6 py-4 text-sm text-muted">{{ $student['program'] }}</td>
                            <td class="px-6 py-4">
                                @if ($student['status'] === 'logged-in')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-success text-white rounded-lg text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                        Logged In
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-muted rounded-lg text-xs font-medium">
                                        <span class="w-1.5 h-1.5 bg-muted rounded-full"></span>
                                        Not Logged In
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-muted">{{ $student['lastActive'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-black/10 flex items-center justify-between">
            <p class="text-sm text-muted">Showing {{ count($students) }} students in your class</p>
            <button class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                Export Attendance Report
            </button>
        </div>
    </div>

</div>
@endsection
