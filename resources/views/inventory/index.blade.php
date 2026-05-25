@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6"
     x-data='{
         activeTab: "{{ old('supply_name') !== null ? 'supplies' : (old('equipment_no') !== null ? 'equipment' : request('tab', 'equipment')) }}",

         /* ── Equipment ───────────────────────────────────────────── */
         equipment: @json($equipment),
         eqSearch: "",
         eqTypeFilter: "all",
         eqLabFilter: "all",
         eqStatusFilter: "all",
         showAddEquipment: {{ ($errors->has('equipment_no') || $errors->has('equipment_name') || $errors->has('equipment_type') || $errors->has('lab_id') || $errors->has('quantity')) ? 'true' : 'false' }},
         showEditEquipment: false,
         showDeleteEquipment: false,
         showQR: false,
         selectedEquipment: null,

         /* ── Supplies ────────────────────────────────────────────── */
         supplies: @json($supplies),
         supSearch: "",
         supCategoryFilter: "all",
         supStatusFilter: "all",
         showAddSupply: {{ $errors->has('supply_name') || $errors->has('category') ? 'true' : 'false' }},
         showEditSupply: false,
         showDeleteSupply: false,
         showStatusConfirm: false,
         pendingStatusSupply: null,
         pendingStatusValue: null,
         selectedSupply: null,

         /* ── Computed ────────────────────────────────────────────── */
         get filteredEquipment() {
             const q = this.eqSearch.toLowerCase();
             return this.equipment.filter(e => {
                 const matchType   = this.eqTypeFilter   === "all" || e.equipment_type   === this.eqTypeFilter;
                 const matchLab    = this.eqLabFilter    === "all" || String(e.lab_id)   === String(this.eqLabFilter);
                 const matchStatus = this.eqStatusFilter === "all" || e.equipment_status === this.eqStatusFilter;
                 const matchSearch = !q
                     || (e.serial_no      || "").toLowerCase().includes(q)
                     || (e.equipment_no   || "").toLowerCase().includes(q)
                     || (e.equipment_name || "").toLowerCase().includes(q)
                     || (e.brand          || "").toLowerCase().includes(q);
                 return matchType && matchLab && matchStatus && matchSearch;
             });
         },
         get filteredSupplies() {
             const q = this.supSearch.toLowerCase();
             return this.supplies.filter(s => {
                 const matchCat    = this.supCategoryFilter === "all" || s.category === this.supCategoryFilter;
                 const matchSearch = !q || (s.supply_name || "").toLowerCase().includes(q);
                 const matchStatus = this.supStatusFilter  === "all" || s.status   === this.supStatusFilter;
                 return matchCat && matchSearch && matchStatus;
             });
         },

         /* ── Helpers ─────────────────────────────────────────────── */
         typeLabel(t) {
             return { computer_unit: "Computer Unit", peripheral: "Peripheral", component: "Component", miscellaneous: "Miscellaneous" }[t] || t;
         },
         statusClass(s) {
             return { available: "bg-success text-white", "in-use": "bg-primary text-white", maintenance: "bg-warning text-[#2c2c2c]", damaged: "bg-muted text-white" }[s] || "bg-muted text-white";
         },
         statusLabel(s) {
             return { available: "Available", "in-use": "In Use", maintenance: "Maintenance", damaged: "Damaged" }[s] || s;
         },
         supplyStatusClass(s) {
             return { fully_stocked: "bg-success text-white", in_stock: "bg-blue-100 text-blue-800", low_stock: "bg-warning text-[#2c2c2c]", out_of_stock: "bg-primary text-white" }[s] || "bg-muted text-white";
         },
         supplyStatusLabel(s) {
             return { fully_stocked: "Fully Stocked", in_stock: "In Stock", low_stock: "Low Stock", out_of_stock: "Out of Stock" }[s] || s;
         },

         /* ── Actions ─────────────────────────────────────────────── */
         openEditEquipment(item)   { this.selectedEquipment = {...item}; this.showEditEquipment   = true; },
         openDeleteEquipment(item) { this.selectedEquipment = {...item}; this.showDeleteEquipment = true; },
         openQR(item)              { this.selectedEquipment = {...item}; this.showQR              = true; },
         openEditSupply(item)      { this.selectedSupply    = {...item}; this.showEditSupply      = true; },
         openDeleteSupply(item)    { this.selectedSupply    = {...item}; this.showDeleteSupply    = true; },
         confirmStatusChange(item, newStatus) {
             this.pendingStatusSupply = {...item};
             this.pendingStatusValue  = newStatus;
             this.showStatusConfirm   = true;
         },
         submitStatusChange() {
             if (!this.pendingStatusSupply || !this.pendingStatusValue) return;
             var form = document.createElement("form");
             form.method = "POST";
             form.action = "{{ url('/inventory/supplies') }}/" + this.pendingStatusSupply.supply_id;
             var fields = {
                 _token: "{{ csrf_token() }}",
                 _method: "PUT",
                 supply_name: this.pendingStatusSupply.supply_name,
                 category: this.pendingStatusSupply.category,
                 status: this.pendingStatusValue,
                 unit: this.pendingStatusSupply.unit || "",
                 remarks: this.pendingStatusSupply.remarks || ""
             };
             for (var key in fields) {
                 var input = document.createElement("input");
                 input.type = "hidden";
                 input.name = key;
                 input.value = fields[key];
                 form.appendChild(input);
             }
             document.body.appendChild(form);
             form.submit();
         },

         generateQR(id) {
             this.$nextTick(() => {
                 const el = document.getElementById(id);
                 if (!el || !this.selectedEquipment || typeof QRCode === "undefined") return;
                 el.innerHTML = "";

                 /*
                  * davidshimjs/qrcodejs — https://davidshimjs.github.io/qrcodejs/
                  * Payload: URL so scanning opens the status-update page directly.
                  * The URL encodes equipment_id; all other fields are fetched server-side.
                  */
                 const url = `{{ url('/inventory/equipment') }}/${this.selectedEquipment.equipment_id}`;

                 new QRCode(el, {
                     text:         url,
                     width:        180,
                     height:       180,
                     colorDark:    "#000000",
                     colorLight:   "#ffffff",
                     correctLevel: QRCode.CorrectLevel.H
                 });
             });
         },

         downloadQR() {
             if (!this.selectedEquipment) return;
             var canvas = document.querySelector("#qr-canvas canvas");
             if (!canvas) return;
             var link = document.createElement("a");
             link.download = "QR-" + this.selectedEquipment.equipment_no + ".png";
             link.href = canvas.toDataURL("image/png");
             document.body.appendChild(link);
             link.click();
             document.body.removeChild(link);
         }
     }'
     x-init='$watch("showQR", v => { if (v) generateQR("qr-canvas"); })'>



    {{-- ── Flash message ────────────────────────────────────────────────────── --}}
    @if (session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" 
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="flex items-center gap-3 p-4 bg-success/10 border border-success/30 rounded-lg">
        <x-icon name="check-circle" class="w-4 h-4 text-success flex-shrink-0" />
        <p class="text-sm text-success">{{ session('success') }}</p>
    </div>
    @endif

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Inventory Management</h1>
            <p class="text-muted text-sm">Physical count records for equipment and consumable supplies</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="'{{ route('inventory.print') }}?tab=' + activeTab" target="_blank"
               class="flex items-center gap-2 px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-[#2c2c2c]">
                <x-icon name="download" class="w-4 h-4" />
                Print List
            </a>
            @if (session('role') === 'admin')
            <button x-show="activeTab === 'equipment'" @click="showAddEquipment = true"
                    class="flex items-center gap-2 px-5 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
                <x-icon name="plus" class="w-4 h-4" />
                Add Equipment
            </button>
            <button x-show="activeTab === 'supplies'" x-cloak @click="showAddSupply = true"
                    class="flex items-center gap-2 px-5 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium">
                <x-icon name="plus" class="w-4 h-4" />
                Add Supply
            </button>
            @endif
        </div>
    </div>

    {{-- ── Tab Navigation ───────────────────────────────────────────────────── --}}
    <div class="border-b border-black/10">
        <nav class="flex gap-1">
            <button @click="activeTab = 'equipment'"
                    :class="activeTab === 'equipment' ? 'border-b-2 border-primary text-primary' : 'text-muted hover:text-[#2c2c2c]'"
                    class="px-5 py-3 text-sm font-medium transition-colors flex items-center gap-2 -mb-px">
                <x-icon name="monitor" class="w-4 h-4" />
                Equipment / Hardware
                <span class="px-2 py-0.5 bg-surface rounded-full text-xs">{{ $totalEquipment }}</span>
            </button>
            <button @click="activeTab = 'supplies'"
                    :class="activeTab === 'supplies' ? 'border-b-2 border-primary text-primary' : 'text-muted hover:text-[#2c2c2c]'"
                    class="px-5 py-3 text-sm font-medium transition-colors flex items-center gap-2 -mb-px">
                <x-icon name="box" class="w-4 h-4" />
                Consumable Supplies
                <span class="px-2 py-0.5 bg-surface rounded-full text-xs">{{ $totalSupplies }}</span>
            </button>
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         EQUIPMENT TAB — Hardware with QR codes & serial numbers
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'equipment'">
        <div class="bg-white rounded-lg border border-black/10 shadow-sm">

            {{-- Toolbar --}}
            <div class="p-5 border-b border-black/10">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[180px] max-w-sm">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                            <x-icon name="search" class="w-4 h-4" />
                        </span>
                        <input type="text" x-model="eqSearch"
                               placeholder="Search name, serial no., brand..."
                               class="w-full pl-9 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <select x-model="eqTypeFilter"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        <option value="all">All Types</option>
                        <option value="computer_unit">Computer Unit</option>
                        <option value="peripheral">Peripheral</option>
                        <option value="component">Component</option>
                        <option value="miscellaneous">Miscellaneous</option>
                    </select>
                    <select x-model="eqLabFilter"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        <option value="all">All Labs</option>
                        @foreach ($labs as $lab)
                        <option value="{{ $lab->lab_id }}">{{ $lab->lab_name }}</option>
                        @endforeach
                    </select>
                    <select x-model="eqStatusFilter"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        <option value="all">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="in-use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Equip. No.</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Serial No.</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Item Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Brand / Model</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Lab</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">PM</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">CAL</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        <template x-for="item in filteredEquipment" :key="item.equipment_id">
                            <tr class="hover:bg-surface transition-colors">
                                <td class="px-5 py-3 text-sm font-mono text-[#2c2c2c]" x-text="item.equipment_no"></td>
                                <td class="px-5 py-3 text-sm font-mono text-muted" x-text="item.serial_no || '—'"></td>
                                <td class="px-5 py-3 text-sm font-medium text-[#2c2c2c]" x-text="item.equipment_name"></td>
                                <td class="px-5 py-3 text-sm text-muted">
                                    <span x-text="item.brand || '—'"></span>
                                    <span x-show="item.model_number" x-text="' / ' + item.model_number" class="text-xs text-muted/70"></span>
                                </td>
                                <td class="px-5 py-3 text-sm text-[#2c2c2c]" x-text="typeLabel(item.equipment_type)"></td>
                                <td class="px-5 py-3 text-sm text-muted" x-text="item.lab_name"></td>
                                <td class="px-5 py-3">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium"
                                          :class="statusClass(item.equipment_status)"
                                          x-text="statusLabel(item.equipment_status)"></span>
                                </td>
                                <td class="px-5 py-3 text-sm text-[#2c2c2c]" x-text="item.preventive_maintenance_done ? '✔' : '—'"></td>
                                <td class="px-5 py-3 text-sm text-[#2c2c2c]" x-text="item.calibration_done ? '✔' : '—'"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <button @click="openQR(item)" title="View / Print QR Code"
                                                class="p-1.5 hover:bg-white rounded-lg transition-colors">
                                            <x-icon name="qr-code" class="w-4 h-4 text-success" />
                                        </button>
                                        @if (session('role') === 'admin')
                                        <button @click="openEditEquipment(item)" title="Edit"
                                                class="p-1.5 hover:bg-white rounded-lg transition-colors">
                                            <x-icon name="edit" class="w-4 h-4 text-muted" />
                                        </button>
                                        <button @click="openDeleteEquipment(item)" title="Delete"
                                                class="p-1.5 hover:bg-white rounded-lg transition-colors">
                                            <x-icon name="trash-2" class="w-4 h-4 text-muted" />
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredEquipment.length === 0">
                            <tr>
                                <td colspan="10" class="px-5 py-12 text-center text-sm text-muted">
                                    No equipment found matching your filters.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-black/10">
                <p class="text-sm text-muted" x-text="`Showing ${filteredEquipment.length} of ${equipment.length} items`"></p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SUPPLIES TAB — Consumables with low-stock tracking (no QR codes)
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'supplies'" x-cloak>
        <div class="bg-white rounded-lg border border-black/10 shadow-sm">

            {{-- Toolbar --}}
            <div class="p-5 border-b border-black/10">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[180px] max-w-sm">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                            <x-icon name="search" class="w-4 h-4" />
                        </span>
                        <input type="text" x-model="supSearch"
                               placeholder="Search supply name..."
                               class="w-full pl-9 pr-4 py-2 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <select x-model="supCategoryFilter"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        <option value="all">All Categories</option>
                        @foreach (\App\Http\Controllers\SupplyController::CATEGORIES as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select x-model="supStatusFilter"
                            class="px-3 py-2 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        <option value="all">All Statuses</option>
                        <option value="fully_stocked">Fully Stocked</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Supply Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Category</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Unit</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Remarks</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-[#2c2c2c] uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        <template x-for="item in filteredSupplies" :key="item.supply_id">
                            <tr class="hover:bg-surface transition-colors">
                                <td class="px-5 py-3 text-sm font-medium text-[#2c2c2c]" x-text="item.supply_name"></td>
                                <td class="px-5 py-3 text-sm text-muted" x-text="item.category"></td>
                                <td class="px-5 py-3 text-sm text-muted" x-text="item.unit || '—'"></td>
                                <td class="px-5 py-3">
                                    @if (session('role') === 'admin')
                                    <select :value="item.status"
                                            @change="confirmStatusChange(item, $event.target.value); $event.target.value = item.status"
                                            class="px-2 py-1 border border-black/10 rounded text-xs font-medium bg-surface focus:border-primary focus:outline-none cursor-pointer">
                                        <option value="fully_stocked">Fully Stocked</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                    @else
                                    <span class="inline-block px-2 py-1 rounded text-xs font-medium"
                                          :class="supplyStatusClass(item.status)"
                                          x-text="supplyStatusLabel(item.status)"></span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-muted max-w-[160px] truncate" x-text="item.remarks || '—'"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        @if (session('role') === 'admin')
                                        <button @click="openEditSupply(item)" title="Edit"
                                                class="p-1.5 hover:bg-white rounded-lg transition-colors">
                                            <x-icon name="edit" class="w-4 h-4 text-muted" />
                                        </button>
                                        <button @click="openDeleteSupply(item)" title="Delete"
                                                class="p-1.5 hover:bg-white rounded-lg transition-colors">
                                            <x-icon name="trash-2" class="w-4 h-4 text-muted" />
                                        </button>
                                        @else
                                        <span class="text-xs text-muted">View only</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSupplies.length === 0">
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-sm text-muted">No supply items found.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-black/10">
                <p class="text-sm text-muted" x-text="`Showing ${filteredSupplies.length} of ${supplies.length} items`"></p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODALS
    ═══════════════════════════════════════════════════════════════════════ --}}

    {{-- ADD EQUIPMENT ────────────────────────────────────────────────────────── --}}
    <div x-show="showAddEquipment" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showAddEquipment = false"
             class="bg-white rounded-xl w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-black/10 sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-primary text-lg">Add Equipment</h2>
                    <p class="text-xs text-muted mt-0.5">Keyboards, monitors, system units, mice, switches, access points, etc.</p>
                </div>
                <button @click="showAddEquipment = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>
            <form method="POST" action="{{ route('equipment.store') }}" class="p-6 space-y-4">
                @csrf
                {{-- Validation errors --}}
                @if ($errors->any() && old('equipment_no') !== null)
                <div class="p-3 bg-primary/5 border border-primary/20 rounded-lg">
                    <ul class="text-sm text-primary space-y-1">
                        @foreach ($errors->all() as $e)
                        <li class="flex items-start gap-2"><span class="mt-0.5">•</span> {{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Equipment No. <span class="text-primary">*</span></label>
                        <input type="text" name="equipment_no" required placeholder="e.g., EQ-001"
                               value="{{ old('equipment_no') }}"
                               class="w-full px-4 py-2.5 border @error('equipment_no') border-primary @else border-black/10 @enderror rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                        @error('equipment_no')<p class="mt-1 text-xs text-primary">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Serial No.</label>
                        <input type="text" name="serial_no" placeholder="e.g., SN-20240001"
                               value="{{ old('serial_no') }}"
                               class="w-full px-4 py-2.5 border @error('serial_no') border-primary @else border-black/10 @enderror rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                        @error('serial_no')<p class="mt-1 text-xs text-primary">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Item Name <span class="text-primary">*</span></label>
                    <input type="text" name="equipment_name" required placeholder="e.g., Desktop Computer"
                           value="{{ old('equipment_name') }}"
                           class="w-full px-4 py-2.5 border @error('equipment_name') border-primary @else border-black/10 @enderror rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('equipment_name')<p class="mt-1 text-xs text-primary">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Brand</label>
                        <input type="text" name="brand" placeholder="e.g., Dell"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Model</label>
                        <input type="text" name="model_number" placeholder="e.g., OptiPlex 7090"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Type <span class="text-primary">*</span></label>
                        <select name="equipment_type" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="computer_unit">Computer Unit</option>
                            <option value="peripheral">Peripheral</option>
                            <option value="component">Component</option>
                            <option value="miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Status <span class="text-primary">*</span></label>
                        <select name="equipment_status" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="available">Available</option>
                            <option value="in-use">In Use</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Laboratory <span class="text-primary">*</span></label>
                        <select name="lab_id" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="">— Select Lab —</option>
                            @foreach ($labs as $lab)
                            <option value="{{ $lab->lab_id }}">{{ $lab->lab_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Quantity <span class="text-primary">*</span></label>
                        <input type="number" name="quantity" value="1" min="1" required
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="preventive_maintenance_done" value="1" class="w-4 h-4 text-primary focus:ring-primary/20 border-black/20 rounded">
                        <span class="text-sm font-medium text-[#2c2c2c]">PM Done</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="calibration_done" value="1" class="w-4 h-4 text-primary focus:ring-primary/20 border-black/20 rounded">
                        <span class="text-sm font-medium text-[#2c2c2c]">CAL Done</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Remarks</label>
                    <textarea name="remarks" rows="2" placeholder="Optional notes..."
                              class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-black/10">
                    <button type="button" @click="showAddEquipment = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT EQUIPMENT ──────────────────────────────────────────────────────── --}}
    <div x-show="showEditEquipment" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showEditEquipment = false"
             class="bg-white rounded-xl w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-black/10 sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-primary text-lg">Edit Equipment</h2>
                    <p class="text-xs text-muted mt-0.5" x-text="selectedEquipment?.equipment_name"></p>
                </div>
                <button @click="showEditEquipment = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>
            <form method="POST" :action="`{{ url('/inventory/equipment') }}/${selectedEquipment?.equipment_id}`" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Equipment No. <span class="text-primary">*</span></label>
                        <input type="text" name="equipment_no" required :value="selectedEquipment?.equipment_no"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Serial No.</label>
                        <input type="text" name="serial_no" :value="selectedEquipment?.serial_no || ''"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Item Name <span class="text-primary">*</span></label>
                    <input type="text" name="equipment_name" required :value="selectedEquipment?.equipment_name"
                           class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Brand</label>
                        <input type="text" name="brand" :value="selectedEquipment?.brand || ''"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Model</label>
                        <input type="text" name="model_number" :value="selectedEquipment?.model_number || ''"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Type <span class="text-primary">*</span></label>
                        <select name="equipment_type" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="computer_unit" :selected="selectedEquipment?.equipment_type === 'computer_unit'">Computer Unit</option>
                            <option value="peripheral"    :selected="selectedEquipment?.equipment_type === 'peripheral'">Peripheral</option>
                            <option value="component"     :selected="selectedEquipment?.equipment_type === 'component'">Component</option>
                            <option value="miscellaneous" :selected="selectedEquipment?.equipment_type === 'miscellaneous'">Miscellaneous</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Status <span class="text-primary">*</span></label>
                        <select name="equipment_status" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="available"   :selected="selectedEquipment?.equipment_status === 'available'">Available</option>
                            <option value="in-use"      :selected="selectedEquipment?.equipment_status === 'in-use'">In Use</option>
                            <option value="maintenance" :selected="selectedEquipment?.equipment_status === 'maintenance'">Maintenance</option>
                            <option value="damaged"     :selected="selectedEquipment?.equipment_status === 'damaged'">Damaged</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Laboratory <span class="text-primary">*</span></label>
                        <select name="lab_id" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            @foreach ($labs as $lab)
                            <option value="{{ $lab->lab_id }}" :selected="selectedEquipment?.lab_id == {{ $lab->lab_id }}">{{ $lab->lab_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Quantity <span class="text-primary">*</span></label>
                        <input type="number" name="quantity" min="1" required :value="selectedEquipment?.quantity || 1"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="preventive_maintenance_done" value="1" class="w-4 h-4 text-primary focus:ring-primary/20 border-black/20 rounded" :checked="selectedEquipment?.preventive_maintenance_done">
                        <span class="text-sm font-medium text-[#2c2c2c]">PM Done</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="calibration_done" value="1" class="w-4 h-4 text-primary focus:ring-primary/20 border-black/20 rounded" :checked="selectedEquipment?.calibration_done">
                        <span class="text-sm font-medium text-[#2c2c2c]">CAL Done</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Remarks</label>
                    <textarea name="remarks" rows="2" :value="selectedEquipment?.remarks || ''"
                              class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-black/10">
                    <button type="button" @click="showEditEquipment = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Update Equipment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE EQUIPMENT ────────────────────────────────────────────────────── --}}
    <div x-show="showDeleteEquipment" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showDeleteEquipment = false" class="bg-white rounded-xl w-full max-w-md shadow-2xl">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash-2" class="w-6 h-6 text-primary" />
                </div>
                <h3 class="font-heading font-semibold text-[#2c2c2c] text-lg mb-1">Delete Equipment</h3>
                <p class="text-sm text-muted mb-2">You are about to delete:</p>
                <p class="text-sm font-medium text-[#2c2c2c] mb-5"
                   x-text="`${selectedEquipment?.equipment_name} (${selectedEquipment?.equipment_no})`"></p>
                <p class="text-xs text-muted mb-5">This action cannot be undone.</p>
                <form method="POST" :action="`{{ url('/inventory/equipment') }}/${selectedEquipment?.equipment_id}`"
                      class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteEquipment = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- QR CODE MODAL ───────────────────────────────────────────────────────── --}}
    <div x-show="showQR" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showQR = false; selectedEquipment = null" class="bg-white rounded-xl w-full max-w-xs shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-base font-medium">QR Code</h2>
                    <p class="text-xs text-muted mt-0.5" x-text="selectedEquipment?.equipment_name"></p>
                </div>
                <button @click="showQR = false; selectedEquipment = null" class="p-1.5 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-4 h-4 text-muted" />
                </button>
            </div>
            <div class="p-5 flex flex-col items-center gap-4">
                <div id="qr-canvas" class="flex items-center justify-center min-h-[180px]"></div>
                <div class="w-full border border-black/10 rounded-lg p-3 bg-surface space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">ID</span>
                        <span class="text-xs font-mono font-semibold text-[#2c2c2c]" x-text="selectedEquipment?.equipment_id"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">Equipment No.</span>
                        <span class="text-xs font-mono font-semibold text-[#2c2c2c]" x-text="selectedEquipment?.equipment_no"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">Serial No.</span>
                        <span class="text-xs font-mono text-[#2c2c2c]" x-text="selectedEquipment?.serial_no || '—'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">Name</span>
                        <span class="text-xs text-[#2c2c2c] text-right max-w-[160px] truncate" x-text="selectedEquipment?.equipment_name"></span>
                    </div>
                </div>
                <div class="flex gap-2 w-full">
                    <button @click="downloadQR()"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-xs font-medium">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD SUPPLY ──────────────────────────────────────────────────────────── --}}
    <div x-show="showAddSupply" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showAddSupply = false" class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
            <div class="flex items-center justify-between p-6 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-lg">Add Consumable Supply</h2>
                    <p class="text-xs text-muted mt-0.5">Bond paper, ink, cleaning supplies, cables, tools, etc.</p>
                </div>
                <button @click="showAddSupply = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>
            <form method="POST" action="{{ route('supply.store') }}" class="p-6 space-y-4">
                @csrf
                {{-- Validation errors --}}
                @if ($errors->any() && old('supply_name') !== null)
                <div class="p-3 bg-primary/5 border border-primary/20 rounded-lg">
                    <ul class="text-sm text-primary space-y-1">
                        @foreach ($errors->all() as $e)
                        <li class="flex items-start gap-2"><span class="mt-0.5">•</span> {{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Supply Name <span class="text-primary">*</span></label>
                    <input type="text" name="supply_name" required placeholder="e.g., Bond Paper (Short)"
                           value="{{ old('supply_name') }}"
                           class="w-full px-4 py-2.5 border @error('supply_name') border-primary @else border-black/10 @enderror rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    @error('supply_name')<p class="mt-1 text-xs text-primary">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Category <span class="text-primary">*</span></label>
                    <select name="category" required
                            class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        @foreach (\App\Http\Controllers\SupplyController::CATEGORIES as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Status <span class="text-primary">*</span></label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="fully_stocked">Fully Stocked</option>
                            <option value="in_stock" selected>In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Unit</label>
                        <input type="text" name="unit" placeholder="e.g., ream"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Remarks</label>
                    <textarea name="remarks" rows="2" placeholder="Optional notes..."
                              class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-black/10">
                    <button type="button" @click="showAddSupply = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Save Supply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT SUPPLY ─────────────────────────────────────────────────────────── --}}
    <div x-show="showEditSupply" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showEditSupply = false" class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
            <div class="flex items-center justify-between p-6 border-b border-black/10">
                <div>
                    <h2 class="text-primary text-lg">Edit Supply</h2>
                    <p class="text-xs text-muted mt-0.5" x-text="selectedSupply?.supply_name"></p>
                </div>
                <button @click="showEditSupply = false" class="p-2 hover:bg-surface rounded-lg transition-colors">
                    <x-icon name="x" class="w-5 h-5 text-muted" />
                </button>
            </div>
            <form method="POST" :action="`{{ url('/inventory/supplies') }}/${selectedSupply?.supply_id}`" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Supply Name <span class="text-primary">*</span></label>
                    <input type="text" name="supply_name" required :value="selectedSupply?.supply_name"
                           class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Category <span class="text-primary">*</span></label>
                    <select name="category" required
                            class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                        @foreach (\App\Http\Controllers\SupplyController::CATEGORIES as $cat)
                        <option value="{{ $cat }}" :selected="selectedSupply?.category === '{{ $cat }}'">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Status <span class="text-primary">*</span></label>
                        <select name="status" required
                                class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none cursor-pointer">
                            <option value="fully_stocked" :selected="selectedSupply?.status === 'fully_stocked'">Fully Stocked</option>
                            <option value="in_stock"      :selected="selectedSupply?.status === 'in_stock'">In Stock</option>
                            <option value="low_stock"     :selected="selectedSupply?.status === 'low_stock'">Low Stock</option>
                            <option value="out_of_stock"  :selected="selectedSupply?.status === 'out_of_stock'">Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Unit</label>
                        <input type="text" name="unit" :value="selectedSupply?.unit || ''"
                               class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-1.5">Remarks</label>
                    <textarea name="remarks" rows="2" :value="selectedSupply?.remarks || ''"
                              class="w-full px-4 py-2.5 border border-black/10 rounded-lg bg-surface text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-black/10">
                    <button type="button" @click="showEditSupply = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Update Supply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE SUPPLY ───────────────────────────────────────────────────────── --}}
    <div x-show="showDeleteSupply" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showDeleteSupply = false" class="bg-white rounded-xl w-full max-w-md shadow-2xl">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash-2" class="w-6 h-6 text-primary" />
                </div>
                <h3 class="font-heading font-semibold text-[#2c2c2c] text-lg mb-1">Delete Supply Item</h3>
                <p class="text-sm text-muted mb-2">You are about to delete:</p>
                <p class="text-sm font-medium text-[#2c2c2c] mb-5" x-text="selectedSupply?.supply_name"></p>
                <p class="text-xs text-muted mb-5">This action cannot be undone.</p>
                <form method="POST" :action="`{{ url('/inventory/supplies') }}/${selectedSupply?.supply_id}`"
                      class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteSupply = false"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- STATUS CHANGE CONFIRMATION ────────────────────────────────────────── --}}
    <div x-show="showStatusConfirm" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div @click.outside="showStatusConfirm = false; pendingStatusSupply = null; pendingStatusValue = null"
             class="bg-white rounded-xl w-full max-w-md shadow-2xl">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-warning/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="alert-triangle" class="w-6 h-6 text-warning" />
                </div>
                <h3 class="font-heading font-semibold text-[#2c2c2c] text-lg mb-1">Change Supply Status</h3>
                <p class="text-sm text-muted mb-2">You are about to change the status of:</p>
                <p class="text-sm font-medium text-[#2c2c2c] mb-3" x-text="pendingStatusSupply?.supply_name"></p>
                <div class="flex items-center justify-center gap-2 mb-5">
                    <span class="inline-block px-2 py-1 rounded text-xs font-medium"
                          :class="supplyStatusClass(pendingStatusSupply?.status)"
                          x-text="supplyStatusLabel(pendingStatusSupply?.status)"></span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-muted" />
                    <span class="inline-block px-2 py-1 rounded text-xs font-medium"
                          :class="supplyStatusClass(pendingStatusValue)"
                          x-text="supplyStatusLabel(pendingStatusValue)"></span>
                </div>
                <div class="flex justify-center gap-3">
                    <button type="button" @click="showStatusConfirm = false; pendingStatusSupply = null; pendingStatusValue = null"
                            class="px-5 py-2.5 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">Cancel</button>
                    <button type="button" @click="submitStatusChange()"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">Yes, Change Status</button>
                </div>
            </div>
        </div>
    </div>




</div>
@endsection

@push('modals')
{{-- davidshimjs/qrcodejs — https://davidshimjs.github.io/qrcodejs/ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush
