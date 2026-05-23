@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6"
     x-data='{
         showAddModal: false,
         search: "",
         categoryFilter: "all",
         equipment: @json($equipment),
         get filtered() {
             const q = this.search.toLowerCase();
             return this.equipment.filter(item => {
                 const matchCat = this.categoryFilter === "all" || item.category === this.categoryFilter;
                 const matchSearch = !q ||
                     item.serialNumber.toLowerCase().includes(q) ||
                     item.itemName.toLowerCase().includes(q) ||
                     item.brand.toLowerCase().includes(q);
                 return matchCat && matchSearch;
             });
         }
     }'>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Inventory Management</h1>
            <p class="text-muted text-sm">Track and manage laboratory equipment and assets</p>
        </div>
        <button @click="showAddModal = true"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
            <x-icon name="plus" class="w-5 h-5" />
            Add Equipment
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
                           placeholder="Search by Serial Number or Item Name..."
                           class="w-full pl-10 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>

                <div class="flex items-center gap-2">
                    <x-icon name="filter" class="w-5 h-5 text-muted" />
                    <select x-model="categoryFilter"
                            class="px-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                   focus:border-primary focus:outline-none transition-all cursor-pointer">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat === 'all' ? 'All Categories' : $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Serial Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Item Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Brand</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Model</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    <template x-for="(item, idx) in filtered" :key="idx">
                        <tr class="hover:bg-surface transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-[#2c2c2c]" x-text="item.serialNumber"></td>
                            <td class="px-6 py-4 text-sm font-medium text-[#2c2c2c]" x-text="item.itemName"></td>
                            <td class="px-6 py-4 text-sm text-muted" x-text="item.brand"></td>
                            <td class="px-6 py-4 text-sm text-muted" x-text="item.model"></td>
                            <td class="px-6 py-4 text-sm text-[#2c2c2c]" x-text="item.category"></td>
                            <td class="px-6 py-4">
                                <template x-if="item.status === 'available'">
                                    <span class="inline-block px-2 py-1 bg-success text-white rounded text-xs font-medium">Available</span>
                                </template>
                                <template x-if="item.status === 'in-use'">
                                    <span class="inline-block px-2 py-1 bg-primary text-white rounded text-xs font-medium">In Use</span>
                                </template>
                                <template x-if="item.status === 'maintenance'">
                                    <span class="inline-block px-2 py-1 bg-warning text-[#2c2c2c] rounded text-xs font-medium">Maintenance</span>
                                </template>
                                <template x-if="item.status === 'damaged'">
                                    <span class="inline-block px-2 py-1 bg-muted text-white rounded text-xs font-medium">Damaged</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button class="p-2 hover:bg-white rounded-lg transition-colors" title="Edit">
                                        <x-icon name="edit" class="w-4 h-4 text-muted" />
                                    </button>
                                    <button class="p-2 hover:bg-white rounded-lg transition-colors" title="Delete">
                                        <x-icon name="trash-2" class="w-4 h-4 text-muted" />
                                    </button>
                                    <button class="p-2 hover:bg-white rounded-lg transition-colors" title="QR Code">
                                        <x-icon name="qr-code" class="w-4 h-4 text-success" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-muted">No equipment found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-black/10 flex items-center justify-between">
            <p class="text-sm text-muted" x-text="`Showing ${filtered.length} of {{ count($equipment) }} items`"></p>
            <div class="flex gap-2">
                <button class="px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Previous</button>
                <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm">Next</button>
            </div>
        </div>
    </div>

    {{-- Add Equipment Modal --}}
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
             class="bg-white rounded-xl w-full max-w-3xl shadow-2xl">

            <div class="flex items-center justify-between p-6 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-lg">Add New Equipment</h2>
                    <p class="text-sm text-muted mt-0.5">Register a new laboratory asset</p>
                </div>
                <button @click="showAddModal = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-3 gap-6">

                    {{-- Form Fields --}}
                    <div class="col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Serial Number</label>
                            <input type="text" placeholder="e.g., PC-2024-001"
                                   class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Item Name</label>
                            <input type="text" placeholder="e.g., Desktop Computer"
                                   class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Brand</label>
                                <input type="text" placeholder="e.g., Dell"
                                       class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Model</label>
                                <input type="text" placeholder="e.g., OptiPlex 7090"
                                       class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Category</label>
                            <select class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                           focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <option>Computer</option>
                                <option>Monitor</option>
                                <option>Peripheral</option>
                                <option>Network</option>
                                <option>Printer</option>
                                <option>Furniture</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    {{-- QR Code Panel --}}
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-black/20 rounded-lg p-6 bg-surface
                                    flex flex-col items-center justify-center text-center min-h-[280px]">
                            <x-icon name="qr-code" class="w-16 h-16 text-muted mb-3" />
                            <p class="text-sm text-muted mb-4">QR Code Preview</p>
                            <button class="flex items-center gap-2 px-4 py-2 bg-success text-white rounded-lg
                                           hover:bg-success-dark transition-colors text-sm font-medium">
                                <x-icon name="qr-code" class="w-4 h-4" />
                                Generate QR Code
                            </button>
                        </div>
                        <div class="p-4 bg-warning/10 border border-warning/30 rounded-lg">
                            <p class="text-xs text-[#2c2c2c]">
                                <strong>Tip:</strong> QR codes help track equipment quickly during inventory checks
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="p-6 border-t border-black/10 flex justify-end gap-3">
                <button @click="showAddModal = false"
                        class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">
                    Cancel
                </button>
                <button class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                    Save Equipment
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
