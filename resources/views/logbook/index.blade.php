@extends('layouts.app')

@section('title', 'Logbook & Students')

@section('content')
<div class="space-y-6"
     x-data='{
         showUploadModal: false,
         dragActive: false,
         fileName: null
     }'>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">
                Logbook &amp; Student Management
            </h1>

            <p class="text-muted text-sm">
                Track laboratory usage and manage student records
            </p>
        </div>

        <button @click="showUploadModal = true"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">

            <x-icon name="file-up" class="w-5 h-5" />

            Upload Class List
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))

        <div class="p-4 rounded-lg bg-success text-white">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="p-4 rounded-lg bg-danger text-white">
            {{ session('error') }}
        </div>

    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm">

        {{-- Toolbar --}}
        <div class="p-6 border-b border-black/10">

            <div class="flex items-center gap-4">

                {{-- Search --}}
                <form method="GET"
                      class="relative flex-1 max-w-md">

                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                        <x-icon name="search" class="w-5 h-5" />
                    </span>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by Student ID or Name..."
                           class="w-full pl-10 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />

                </form>

                {{-- Filter --}}
                <form method="GET"
                      class="flex items-center gap-2">

                    @if(request('search'))
                        <input type="hidden"
                               name="search"
                               value="{{ request('search') }}">
                    @endif

                    <x-icon name="filter"
                            class="w-5 h-5 text-muted" />

                    <select name="filter"
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none transition-all cursor-pointer">

                        <option value="">All</option>

                        <option value="day"
                            {{ request('filter') == 'day' ? 'selected' : '' }}>
                            Day
                        </option>

                        <option value="week"
                            {{ request('filter') == 'week' ? 'selected' : '' }}>
                            Week
                        </option>

                        <option value="month"
                            {{ request('filter') == 'month' ? 'selected' : '' }}>
                            Month
                        </option>

                    </select>

                </form>

                {{-- Export Button --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-success text-white rounded-lg hover:bg-success-dark transition-colors text-sm font-medium ml-auto">

                    <x-icon name="download" class="w-4 h-4" />

                    Export

                </button>

            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-surface">

                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Student ID
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Name
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Time-in
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Time-out
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Purpose
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">
                            Date
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-black/5">

                    @forelse($entries as $entry)

                        <tr class="hover:bg-surface transition-colors">

                            {{-- Student ID --}}
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]">

                                {{ $entry['studentId'] }}

                            </td>

                            {{-- Name --}}
                            <td class="px-6 py-4 text-sm font-medium text-[#2c2c2c]">

                                {{ $entry['name'] }}

                            </td>

                            {{-- Time In --}}
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]">

                                {{ $entry['timeIn'] }}

                            </td>

                            {{-- Time Out --}}
                            <td class="px-6 py-4 text-sm">

                                @if($entry['timeOut'])

                                    <span class="text-[#2c2c2c]">

                                        {{ $entry['timeOut'] }}

                                    </span>

                                @else

                                    <span class="inline-block px-2 py-1 bg-success text-white rounded text-xs font-medium">

                                        In Session

                                    </span>

                                @endif

                            </td>

                            {{-- Purpose --}}
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]">

                                {{ $entry['purpose'] }}

                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 text-sm text-muted">

                                {{ $entry['date'] }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="px-6 py-12 text-center text-sm text-muted">

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

    {{-- Upload Modal --}}
    <div x-show="showUploadModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-6">

        <div @click.outside="showUploadModal = false; fileName = null"
             class="bg-white rounded-xl w-full max-w-2xl shadow-2xl">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-black/10">

                <div>

                    <h2 class="text-primary text-lg">
                        Upload Class List
                    </h2>

                    <p class="text-sm text-muted mt-0.5">
                        Import student records via CSV file
                    </p>

                </div>

                <button @click="showUploadModal = false; fileName = null"
                        class="p-2 hover:bg-surface rounded-lg transition-colors">

                    <x-icon name="x" class="w-5 h-5 text-muted" />

                </button>

            </div>

            {{-- Modal Body --}}
            <div class="p-8">

                <div class="border-2 border-dashed rounded-xl p-12 text-center transition-colors cursor-pointer"
                     :class="dragActive ? 'border-primary bg-primary/5' : 'border-black/20 bg-surface'"
                     @dragenter.prevent="dragActive = true"
                     @dragleave.prevent="dragActive = false"
                     @dragover.prevent
                     @drop.prevent="dragActive = false; fileName = $event.dataTransfer.files[0]?.name || null">

                    <x-icon name="upload"
                            class="w-16 h-16 mx-auto mb-4 text-primary" />

                    <h3 class="text-primary text-lg mb-2">
                        Upload CSV Class List
                    </h3>

                    <p class="text-muted text-sm mb-4">
                        Drag and drop your CSV file here, or click to browse
                    </p>

                    <template x-if="fileName">

                        <div class="mb-4 p-3 bg-white rounded-lg border border-black/10 inline-flex items-center gap-2">

                            <x-icon name="file-up"
                                    class="w-4 h-4 text-success" />

                            <span class="text-success text-sm"
                                  x-text="fileName"></span>

                        </div>

                    </template>

                    <label class="inline-block px-6 py-3 bg-white text-primary border-2 border-primary rounded-lg
                                  cursor-pointer hover:bg-primary hover:text-white transition-colors text-sm font-medium">

                        Browse Files

                        <input type="file"
                               accept=".csv"
                               class="hidden"
                               @change="fileName = $event.target.files[0]?.name || null" />

                    </label>

                </div>

                <div class="mt-6 p-4 bg-warning/10 border border-warning/30 rounded-lg">

                    <p class="text-sm text-[#2c2c2c]">

                        <strong>CSV Format:</strong>

                        Student ID, Full Name, Email, Program, Year Level

                    </p>

                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-black/10 flex justify-end gap-3">

                <button @click="showUploadModal = false; fileName = null"
                        class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">

                    Cancel

                </button>

                <button :disabled="!fileName"
                        class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium
                               disabled:opacity-50 disabled:cursor-not-allowed">

                    Import Students

                </button>

            </div>

        </div>

    </div>

</div>
@endsection