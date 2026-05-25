@extends('layouts.app')

@section('title', 'Schedule & Booking')

@section('content')
<div class="space-y-6"
     x-data='{
         selectedDay: {{ $todayDay }},
         selectedLabId: "all",
         panelView: "day",
         schedules: @json($schedules),
         bookings: @json($bookings),
         get filteredSchedules() {
             if (this.selectedLabId === "all") return this.schedules;
             const id = parseInt(this.selectedLabId);
             return this.schedules.filter(s => s.lab_id === id);
         },
         get filteredBookings() {
             if (this.selectedLabId === "all") return this.bookings;
             const id = parseInt(this.selectedLabId);
             return this.bookings.filter(b => b.lab_id === id);
         },
         get scheduledDays() {
             return [...new Set(this.filteredSchedules.map(s => s.day))];
         },
         get pendingDays() {
             return [...new Set(this.filteredBookings.filter(b => b.status === "pending").map(b => b.day))];
         },
         get approvedDays() {
             return [...new Set(this.filteredBookings.filter(b => b.status === "approved").map(b => b.day))];
         },
         get selectedSchedules() {
             return this.filteredSchedules.filter(s => s.day === this.selectedDay);
         },
         get selectedBookings() {
             return this.filteredBookings.filter(b => b.day === this.selectedDay && b.status !== "rejected");
         },
         get pendingBookings() {
             return this.filteredBookings.filter(b => b.status === "pending");
         },
         get rejectedBookings() {
             return this.filteredBookings.filter(b => b.status === "rejected");
         },
         showDay(day) {
             this.selectedDay = day;
             this.panelView = "day";
         },
         dayTooltip(day) {
             const classes  = this.filteredSchedules.filter(s => s.day === day).length;
             const pending  = this.filteredBookings.filter(b => b.day === day && b.status === "pending").length;
             const approved = this.filteredBookings.filter(b => b.day === day && b.status === "approved").length;
             const parts = [];
             if (classes)  parts.push(classes  + " class"   + (classes  > 1 ? "es" : ""));
             if (pending)  parts.push(pending  + " pending");
             if (approved) parts.push(approved + " approved");
             return parts.length ? parts.join(" · ") : "";
         }
     }'>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-primary text-2xl mb-1">Laboratory Schedule &amp; Booking</h1>
            <p class="text-muted text-sm">Manage laboratory reservations and class schedules</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('schedule.create') }}"
               class="flex items-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-white border border-black/10 text-[#2c2c2c] rounded-lg hover:bg-surface transition-colors shadow-sm text-sm font-medium">
                <x-icon name="plus" class="w-5 h-5" />
                Add Class Schedule
            </a>
            <a href="{{ route('bookings.create') }}" target="_blank"
               class="flex items-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
                <x-icon name="plus" class="w-5 h-5" />
                Add Reservation
            </a>
        </div>
    </div>

    {{-- Calendar + Detail --}}
    <div class="flex flex-col xl:flex-row gap-6">

        {{-- Calendar --}}
        <div class="flex-1 bg-white rounded-lg border border-black/10 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <a href="{{ route('schedule', ['month' => $prevMonth]) }}"
                       class="p-2 hover:bg-surface rounded-lg transition-colors block"
                       title="Previous month">
                        <x-icon name="chevron-left" class="w-5 h-5 text-primary" />
                    </a>
                    <h2 class="text-primary text-lg">{{ $monthLabel }}</h2>
                    <a href="{{ route('schedule', ['month' => $nextMonth]) }}"
                       class="p-2 hover:bg-surface rounded-lg transition-colors block"
                       title="Next month">
                        <x-icon name="chevron-right" class="w-5 h-5 text-primary" />
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Lab filter --}}
                    <select x-model="selectedLabId"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="all">All Laboratories</option>
                        @foreach ($laboratories as $lab)
                            <option value="{{ $lab->lab_id }}">{{ $lab->lab_name }}</option>
                        @endforeach
                    </select>

                    {{-- Legend --}}
                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 bg-primary rounded-sm"></div>
                            <span class="text-muted">Class</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 bg-accent rounded-sm"></div>
                            <span class="text-muted">Pending</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 bg-success rounded-sm"></div>
                            <span class="text-muted">Approved</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Day names --}}
            <div class="grid grid-cols-7 gap-2 mb-2">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="text-center text-xs text-muted py-2 font-medium">{{ $dayName }}</div>
                @endforeach
            </div>

            {{-- Day cells --}}
            <div class="grid grid-cols-7 gap-2">
                @foreach ($calendarDays as $day)
                    @if ($day === null)
                        <div></div>
                    @else
                        <button type="button"
                                @click="selectedDay = {{ $day }}"
                                :title="dayTooltip({{ $day }})"
                                :class="selectedDay === {{ $day }}
                                    ? 'border-primary bg-primary/10 shadow-sm'
                                    : 'border-black/10 bg-white hover:bg-surface'"
                                class="aspect-square rounded-lg border p-2 text-left transition-all">

                            <span class="text-sm"
                                  :class="(scheduledDays.includes({{ $day }}) || pendingDays.includes({{ $day }}) || approvedDays.includes({{ $day }}))
                                      ? 'font-semibold text-primary'
                                      : 'text-[#2c2c2c]'">
                                {{ $day }}
                            </span>

                            {{-- Indicator dots: maroon = class, amber = pending, green = approved --}}
                            <div class="mt-1 flex items-center gap-1">
                                <template x-if="scheduledDays.includes({{ $day }})">
                                    <div class="w-1.5 h-1.5 bg-primary rounded-full"></div>
                                </template>
                                <template x-if="pendingDays.includes({{ $day }})">
                                    <div class="w-1.5 h-1.5 bg-accent rounded-full"></div>
                                </template>
                                <template x-if="approvedDays.includes({{ $day }})">
                                    <div class="w-1.5 h-1.5 bg-success rounded-full"></div>
                                </template>
                            </div>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Right Panel: Day View / Pending Requests --}}
        <div class="w-full xl:w-96 bg-white rounded-lg border border-black/10 shadow-sm p-6 flex flex-col">

            {{-- Tabs --}}
            <div class="flex gap-2 mb-4 border-b border-black/10 -mx-6 px-6 pb-3">
                <button type="button"
                        @click="panelView = 'day'"
                        :class="panelView === 'day' ? 'bg-primary text-white' : 'bg-surface text-muted hover:text-primary'"
                        class="px-3 py-1.5 rounded text-xs font-medium transition-colors">
                    Day View
                </button>
                <button type="button"
                        @click="panelView = 'pending'"
                        :class="panelView === 'pending' ? 'bg-primary text-white' : 'bg-surface text-muted hover:text-primary'"
                        class="px-3 py-1.5 rounded text-xs font-medium transition-colors flex items-center gap-1.5">
                    Pending
                    <span x-show="pendingBookings.length > 0"
                          class="w-2 h-2 rounded-full bg-accent"></span>
                </button>
                <button type="button"
                        @click="panelView = 'rejected'"
                        :class="panelView === 'rejected' ? 'bg-primary text-white' : 'bg-surface text-muted hover:text-primary'"
                        class="px-3 py-1.5 rounded text-xs font-medium transition-colors">
                    Rejected
                </button>
            </div>

            {{-- Flash message after approve/reject --}}
            @if (session('success'))
                <div class="mb-3 px-3 py-2 rounded bg-success/10 border border-success/30 text-xs text-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ──────── DAY VIEW ──────── --}}
            <template x-if="panelView === 'day'">
                <div class="overflow-y-auto">
                    <h3 class="text-primary text-base mb-4">
                        {{ $monthShort }} <span x-text="selectedDay"></span>, {{ $displayYear }}
                    </h3>

                    {{-- Class Schedules --}}
                    <template x-if="selectedSchedules.length > 0">
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Class Schedules</p>
                            <div class="space-y-3">
                                <template x-for="(schedule, idx) in selectedSchedules" :key="'s-' + idx">
                                    <div class="border border-black/10 rounded-lg p-4 bg-surface">
                                        <h4 class="text-primary text-sm font-semibold mb-3" x-text="schedule.title"></h4>
                                        <div class="space-y-2">
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="clock" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-[#2c2c2c]" x-text="schedule.time"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="map-pin" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="inline-block px-2 py-0.5 bg-success text-white rounded text-xs font-medium" x-text="schedule.lab"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="user" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-[#2c2c2c]" x-text="schedule.instructor"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="phone" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-muted" x-text="schedule.contact"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Reservations for the selected day --}}
                    <template x-if="selectedBookings.length > 0">
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Reservations</p>
                            <div class="space-y-3">
                                <template x-for="booking in selectedBookings" :key="'b-' + booking.id">
                                    <div class="rounded-lg p-4 border"
                                         :class="booking.status === 'approved'
                                             ? 'border-success/40 bg-success/5'
                                             : 'border-accent/40 bg-accent/5'">
                                        <div class="flex items-start justify-between gap-2 mb-3">
                                            <h4 class="text-primary text-sm font-semibold" x-text="booking.title"></h4>
                                            <span class="text-xs px-2 py-0.5 rounded font-medium capitalize whitespace-nowrap"
                                                  :class="booking.status === 'approved'
                                                      ? 'bg-success text-white'
                                                      : 'bg-accent text-[#2c2c2c]'"
                                                  x-text="booking.status"></span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="clock" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-[#2c2c2c]" x-text="booking.time"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="map-pin" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="inline-block px-2 py-0.5 bg-primary text-white rounded text-xs font-medium" x-text="booking.lab"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="user" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-[#2c2c2c]" x-text="booking.requestor"></span>
                                            </div>
                                            <div class="flex items-start gap-2 text-sm">
                                                <x-icon name="phone" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                                <span class="text-muted" x-text="booking.contact"></span>
                                            </div>
                                        </div>

                                        {{-- Approve/Reject only for pending --}}
                                        <template x-if="booking.status === 'pending'">
                                            <div class="flex gap-2 mt-3 pt-3 border-t border-accent/30">
                                                <form method="POST" :action="`/bookings/${booking.id}/status`" class="flex-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="booking_status" value="approved">
                                                    <button type="submit"
                                                            class="w-full px-3 py-1.5 bg-success text-white rounded text-xs font-medium hover:bg-success-dark transition-colors">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" :action="`/bookings/${booking.id}/status`" class="flex-1"
                                                      onsubmit="return confirm('Reject this reservation?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="booking_status" value="rejected">
                                                    <button type="submit"
                                                            class="w-full px-3 py-1.5 bg-white border border-black/10 text-[#2c2c2c] rounded text-xs font-medium hover:bg-surface transition-colors">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="selectedSchedules.length === 0 && selectedBookings.length === 0">
                        <div class="text-center py-12">
                            <x-icon name="calendar" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
                            <p class="text-muted text-sm">No events scheduled for this day</p>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ──────── PENDING VIEW ──────── --}}
            <template x-if="panelView === 'pending'">
                <div class="overflow-y-auto">
                    <h3 class="text-primary text-base mb-4">Pending Requests</h3>

                    <template x-if="pendingBookings.length === 0">
                        <div class="text-center py-12">
                            <x-icon name="calendar" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
                            <p class="text-muted text-sm">No pending requests</p>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="booking in pendingBookings" :key="'p-' + booking.id">
                            <div class="border border-accent/40 rounded-lg p-4 bg-accent/5">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="text-primary text-sm font-semibold" x-text="booking.title"></h4>
                                    <button type="button"
                                            @click="showDay(booking.day)"
                                            class="text-xs text-primary hover:underline whitespace-nowrap">
                                        Day <span x-text="booking.day"></span>
                                    </button>
                                </div>
                                <div class="space-y-1.5 text-sm">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="clock" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="text-[#2c2c2c]" x-text="booking.time"></span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon name="map-pin" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="inline-block px-2 py-0.5 bg-primary text-white rounded text-xs font-medium" x-text="booking.lab"></span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon name="user" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="text-[#2c2c2c]" x-text="booking.requestor"></span>
                                    </div>
                                </div>

                                <div class="flex gap-2 mt-3 pt-3 border-t border-accent/30">
                                    <form method="POST" :action="`/bookings/${booking.id}/status`" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="booking_status" value="approved">
                                        <button type="submit"
                                                class="w-full px-3 py-1.5 bg-success text-white rounded text-xs font-medium hover:bg-success-dark transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" :action="`/bookings/${booking.id}/status`" class="flex-1"
                                          onsubmit="return confirm('Reject this reservation?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="booking_status" value="rejected">
                                        <button type="submit"
                                                class="w-full px-3 py-1.5 bg-white border border-black/10 text-[#2c2c2c] rounded text-xs font-medium hover:bg-surface transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- ──────── REJECTED VIEW ──────── --}}
            <template x-if="panelView === 'rejected'">
                <div class="overflow-y-auto">
                    <h3 class="text-primary text-base mb-4">Rejected Reservations</h3>

                    <template x-if="rejectedBookings.length === 0">
                        <div class="text-center py-12">
                            <x-icon name="calendar" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
                            <p class="text-muted text-sm">No rejected reservations</p>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="booking in rejectedBookings" :key="'r-' + booking.id">
                            <div class="border border-black/10 rounded-lg p-4 bg-surface opacity-75">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="text-[#2c2c2c] text-sm font-semibold line-through" x-text="booking.title"></h4>
                                    <span class="text-xs px-2 py-0.5 rounded font-medium bg-red-100 text-red-700 whitespace-nowrap">
                                        Rejected
                                    </span>
                                </div>
                                <div class="space-y-1.5 text-sm">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="clock" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="text-muted" x-text="`Day ${booking.day} — ${booking.time}`"></span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon name="map-pin" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="inline-block px-2 py-0.5 bg-black/10 text-[#2c2c2c] rounded text-xs font-medium" x-text="booking.lab"></span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon name="user" class="w-4 h-4 text-muted mt-0.5 flex-shrink-0" />
                                        <span class="text-muted" x-text="booking.requestor"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>

    </div>

</div>
@endsection
