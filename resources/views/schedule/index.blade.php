@extends('layouts.app')

@section('title', 'Schedule & Booking')

@section('content')
<div class="space-y-6"
     x-data='{
         showAddModal: false,
         selectedDay: 15,
         scheduledDays: @json($scheduledDays),
         schedules: @json($schedules),
         get selectedSchedules() {
             return this.schedules.filter(s => s.day === this.selectedDay);
         }
     }'>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Laboratory Schedule &amp; Booking</h1>
            <p class="text-muted text-sm">Manage laboratory reservations and class schedules</p>
        </div>
        <button @click="showAddModal = true"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
            <x-icon name="plus" class="w-5 h-5" />
            Add Reservation
        </button>
    </div>

    {{-- Calendar + Detail --}}
    <div class="flex gap-6">

        {{-- Calendar --}}
        <div class="flex-1 bg-white rounded-lg border border-black/10 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button class="p-2 hover:bg-surface rounded-lg transition-colors">
                        <x-icon name="chevron-left" class="w-5 h-5 text-primary" />
                    </button>
                    <h2 class="text-primary text-lg">May 2026</h2>
                    <button class="p-2 hover:bg-surface rounded-lg transition-colors">
                        <x-icon name="chevron-right" class="w-5 h-5 text-primary" />
                    </button>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-3 h-3 bg-primary rounded-full"></div>
                    <span class="text-muted">Scheduled</span>
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
                        <button @click="selectedDay = {{ $day }}"
                                :class="selectedDay === {{ $day }}
                                    ? 'border-primary bg-primary/10 shadow-sm'
                                    : 'border-black/10 bg-white hover:bg-surface'"
                                class="aspect-square rounded-lg border p-2 text-left transition-all">
                            <span class="text-sm {{ in_array($day, $scheduledDays) ? 'font-semibold text-primary' : 'text-[#2c2c2c]' }}">{{ $day }}</span>
                            @if (in_array($day, $scheduledDays))
                                <div class="mt-1 space-y-0.5">
                                    @for ($i = 0; $i < min(collect($schedules)->where('day', $day)->count(), 2); $i++)
                                        <div class="w-full h-1 bg-primary rounded"></div>
                                    @endfor
                                </div>
                            @endif
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Day Detail --}}
        <div class="w-96 bg-white rounded-lg border border-black/10 shadow-sm p-6">
            <h3 class="text-primary text-base mb-4">
                May <span x-text="selectedDay"></span>, 2026
            </h3>

            <template x-if="selectedSchedules.length > 0">
                <div class="space-y-4">
                    <template x-for="(schedule, idx) in selectedSchedules" :key="idx">
                        <div class="border border-black/10 rounded-lg p-4 bg-surface hover:shadow-sm transition-shadow">
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
            </template>

            <template x-if="selectedSchedules.length === 0">
                <div class="text-center py-12">
                    <x-icon name="calendar" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
                    <p class="text-muted text-sm">No events scheduled for this day</p>
                </div>
            </template>
        </div>

    </div>

    {{-- Add Reservation Modal --}}
    <div x-show="showAddModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-6">

        <div @click.outside="showAddModal = false"
             class="bg-white rounded-xl w-full max-w-2xl shadow-2xl">

            <div class="flex items-center justify-between p-6 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-lg">Add Reservation</h2>
                    <p class="text-sm text-muted mt-0.5">Create a new laboratory booking</p>
                </div>
                <button @click="showAddModal = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>

            <form class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Requestor's Name</label>
                    <input type="text" placeholder="Enter full name"
                           class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Contact Information</label>
                    <input type="text" placeholder="Email or phone number"
                           class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Purpose</label>
                    <textarea rows="3" placeholder="Describe the purpose of reservation"
                              class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                     focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Preferred Date</label>
                        <input type="date"
                               class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Target Laboratory</label>
                        <select class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                            <option>Lab A - Room 301</option>
                            <option>Lab B - Room 302</option>
                            <option>Lab C - Room 303</option>
                            <option>Lab D - Room 304</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Start Time</label>
                        <input type="time"
                               class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-2">End Time</label>
                        <input type="time"
                               class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
            </form>

            <div class="p-6 border-t border-black/10 flex justify-end gap-3">
                <button @click="showAddModal = false"
                        class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">
                    Cancel
                </button>
                <button class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                    Submit Request
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
