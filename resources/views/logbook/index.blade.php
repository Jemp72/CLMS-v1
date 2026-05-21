@extends('layouts.app')

@section('title', 'Logbook & Students')

@section('content')
<div class="space-y-6"
     x-data='{
         showUploadModal: false,
         dragActive: false,
         fileName: null,
         search: "",
         filter: "day",
         entries: @json($entries),
         get filtered() {
             const q = this.search.toLowerCase();
             return this.entries.filter(e =>
                 !q ||
                 e.name.toLowerCase().includes(q) ||
                 e.studentId.toLowerCase().includes(q)
             );
         }
     }'>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Logbook &amp; Student Management</h1>
            <p class="text-muted text-sm">Track laboratory usage and manage student records</p>
        </div>
        <button @click="showUploadModal = true"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
            <x-icon name="file-up" class="w-5 h-5" />
            Upload Class List
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm">

        {{-- Toolbar --}}
        <div class="p-6 border-b border-black/10">
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                        <x-icon name="search" class="w-5 h-5" />
                    </span>
                    <input type="text"
                           x-model="search"
                           placeholder="Search by Student ID or Name..."
                           class="w-full pl-10 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>

                <div class="flex items-center gap-2">
                    <x-icon name="filter" class="w-5 h-5 text-muted" />
                    <select x-model="filter"
                            class="px-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none transition-all cursor-pointer">
                        <option value="day">Day</option>
                        <option value="week">Week</option>
                        <option value="month">Month</option>
                    </select>
                </div>

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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Student ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Time-in</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Time-out</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Purpose</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    <template x-for="(entry, idx) in filtered" :key="idx">
                        <tr class="hover:bg-surface transition-colors">
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]" x-text="entry.studentId"></td>
                            <td class="px-6 py-4 text-sm font-medium text-[#2c2c2c]" x-text="entry.name"></td>
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]" x-text="entry.timeIn"></td>
                            <td class="px-6 py-4 text-sm">
                                <template x-if="entry.timeOut">
                                    <span x-text="entry.timeOut" class="text-[#2c2c2c]"></span>
                                </template>
                                <template x-if="!entry.timeOut">
                                    <span class="inline-block px-2 py-1 bg-success text-white rounded text-xs font-medium">In Session</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]" x-text="entry.purpose"></td>
                            <td class="px-6 py-4 text-sm text-muted" x-text="entry.date"></td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-muted">No entries found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-black/10 flex items-center justify-between">
            <p class="text-sm text-muted" x-text="`Showing ${filtered.length} of {{ count($entries) }} entries`"></p>
            <div class="flex gap-2">
                <button class="px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Previous</button>
                <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm">Next</button>
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

            <div class="flex items-center justify-between p-6 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-lg">Upload Class List</h2>
                    <p class="text-sm text-muted mt-0.5">Import student records via CSV file</p>
                </div>
                <button @click="showUploadModal = false; fileName = null"
                        class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>

            <div class="p-8">
                <div class="border-2 border-dashed rounded-xl p-12 text-center transition-colors cursor-pointer"
                     :class="dragActive ? 'border-primary bg-primary/5' : 'border-black/20 bg-surface'"
                     @dragenter.prevent="dragActive = true"
                     @dragleave.prevent="dragActive = false"
                     @dragover.prevent
                     @drop.prevent="dragActive = false; fileName = $event.dataTransfer.files[0]?.name || null">
                    <x-icon name="upload" class="w-16 h-16 mx-auto mb-4 text-primary" />
                    <h3 class="text-primary text-lg mb-2">Upload CSV Class List</h3>
                    <p class="text-muted text-sm mb-4">Drag and drop your CSV file here, or click to browse</p>

                    <template x-if="fileName">
                        <div class="mb-4 p-3 bg-white rounded-lg border border-black/10 inline-flex items-center gap-2">
                            <x-icon name="file-up" class="w-4 h-4 text-success" />
                            <span class="text-success text-sm" x-text="fileName"></span>
                        </div>
                    </template>

                    <label class="inline-block px-6 py-3 bg-white text-primary border-2 border-primary rounded-lg
                                  cursor-pointer hover:bg-primary hover:text-white transition-colors text-sm font-medium">
                        Browse Files
                        <input type="file" accept=".csv"
                               class="hidden"
                               @change="fileName = $event.target.files[0]?.name || null" />
                    </label>
                </div>

                <div class="mt-6 p-4 bg-warning/10 border border-warning/30 rounded-lg">
                    <p class="text-sm text-[#2c2c2c]">
                        <strong>CSV Format:</strong> Student ID, Full Name, Email, Program, Year Level
                    </p>
                </div>
            </div>

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
