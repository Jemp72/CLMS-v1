<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ $equipment->equipment_name }} — Equipment Details</title>
    @vite(['resources/css/app.css'])
    <style>
        body { background: #f7f7f7; min-height: 100dvh; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-size: 13px; color: #6b7280; }
        .detail-value { font-size: 13px; font-weight: 600; color: #2c2c2c; text-align: right; max-width: 60%; }

        /* Status badge */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .badge-available   { background: #d1fae5; color: #065f46; }
        .badge-in-use      { background: #dbeafe; color: #1e40af; }
        .badge-maintenance { background: #fef9c3; color: #713f12; }
        .badge-damaged     { background: #f3f4f6; color: #374151; }

        /* Status radio buttons (admin only) */
        .status-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 14px 16px;
            border: 2px solid transparent;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #fff;
            color: #2c2c2c;
            text-align: left;
        }
        .status-btn:active { transform: scale(0.98); }
        .status-btn.selected-available  { border-color: #22c55e; background: #f0fdf4; color: #166534; }
        .status-btn.selected-in-use     { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
        .status-btn.selected-maintenance{ border-color: #eab308; background: #fefce8; color: #713f12; }
        .status-btn.selected-damaged    { border-color: #6b7280; background: #f9fafb; color: #374151; }
        .dot {
            width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        }
        .dot-available   { background: #22c55e; }
        .dot-in-use      { background: #2563eb; }
        .dot-maintenance { background: #eab308; }
        .dot-damaged     { background: #6b7280; }

        /* Toggle switch */
        .toggle-track {
            width: 40px; height: 22px;
            background: #d1d5db; border-radius: 11px;
            position: relative; cursor: pointer; transition: background 0.2s;
            flex-shrink: 0;
        }
        .toggle-track.active { background: #22c55e; }
        .toggle-knob {
            width: 18px; height: 18px;
            background: #fff; border-radius: 50%;
            position: absolute; top: 2px; left: 2px;
            transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-track.active .toggle-knob { transform: translateX(18px); }
    </style>
</head>
<body>

<div class="min-h-dvh flex flex-col" style="max-width:480px;margin:0 auto;padding:24px 16px;">

    {{-- Header --}}
    <div class="mb-5">
        <p class="text-xs text-muted uppercase tracking-widest mb-1">USeP CLMS — Equipment Details</p>
        <h1 class="text-xl font-heading font-semibold text-[#2c2c2c] leading-tight">{{ $equipment->equipment_name }}</h1>
        <p class="text-sm text-muted mt-0.5">
            {{ $equipment->equipment_no }}
            @if($equipment->serial_no) · SN: {{ $equipment->serial_no }} @endif
        </p>
    </div>

    {{-- Success flash --}}
    @if (session('scan_success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium text-green-700">{{ session('scan_success') }}</p>
    </div>
    @endif

    {{-- Equipment Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-black/5 p-5 mb-5">
        <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Equipment Information</p>

        <div class="detail-row">
            <span class="detail-label">Equipment No.</span>
            <span class="detail-value" style="font-family:monospace">{{ $equipment->equipment_no }}</span>
        </div>
        @if ($equipment->serial_no)
        <div class="detail-row">
            <span class="detail-label">Serial No.</span>
            <span class="detail-value" style="font-family:monospace">{{ $equipment->serial_no }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Type</span>
            <span class="detail-value">{{ $typeLabels[$equipment->equipment_type] ?? $equipment->equipment_type }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Brand / Model</span>
            <span class="detail-value">
                {{ $equipment->brand ?: '' }}{{ ($equipment->brand && $equipment->model_number) ? ' / ' : '' }}{{ $equipment->model_number ?: '' }}{{ (!$equipment->brand && !$equipment->model_number) ? '—' : '' }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Laboratory</span>
            <span class="detail-value">{{ $equipment->lab_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Quantity</span>
            <span class="detail-value">{{ $equipment->quantity }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status</span>
            @php
                $statusClasses = [
                    'available'   => 'badge-available',
                    'in-use'      => 'badge-in-use',
                    'maintenance' => 'badge-maintenance',
                    'damaged'     => 'badge-damaged',
                ];
            @endphp
            <span class="badge {{ $statusClasses[$equipment->equipment_status] ?? '' }}">
                {{ ucfirst(str_replace('-', ' ', $equipment->equipment_status)) }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Preventive Maintenance (PM)</span>
            <span class="detail-value">{{ $equipment->preventive_maintenance_done ? '✔ Done' : '— Pending' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Calibration (CAL)</span>
            <span class="detail-value">{{ ($equipment->calibration_done ?? false) ? '✔ Done' : '— Pending' }}</span>
        </div>
        @if ($equipment->remarks)
        <div class="detail-row">
            <span class="detail-label">Remarks</span>
            <span class="detail-value">{{ $equipment->remarks }}</span>
        </div>
        @endif
    </div>

    {{-- Admin-only: Edit Status, PM & CAL --}}
    <div class="bg-white rounded-xl shadow-sm border border-black/5 p-5 mb-5">
        <form method="POST" action="{{ route('equipment.update.status', $equipment->equipment_id) }}">
            @csrf

            <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Update Status & Needs</p>

            <div class="space-y-2 mb-5" id="status-options">
                @php
                    $statuses = [
                        'available'   => ['label' => 'Available',    'desc' => 'Equipment is ready to use',          'dot' => 'dot-available'],
                        'in-use'      => ['label' => 'In Use',       'desc' => 'Currently being used',               'dot' => 'dot-in-use'],
                        'maintenance' => ['label' => 'Maintenance',  'desc' => 'Under repair or preventive check',   'dot' => 'dot-maintenance'],
                        'damaged'     => ['label' => 'Damaged',      'desc' => 'Equipment is broken or defective',   'dot' => 'dot-damaged'],
                    ];
                @endphp

                @foreach ($statuses as $value => $info)
                <label class="block cursor-pointer">
                    <input type="radio" name="equipment_status" value="{{ $value }}"
                           class="sr-only"
                           {{ $equipment->equipment_status === $value ? 'checked' : '' }}
                           onchange="updateSelection(this)">
                    <div class="status-btn {{ $equipment->equipment_status === $value ? 'selected-' . $value : '' }}"
                         id="btn-{{ $value }}">
                        <span class="dot {{ $info['dot'] }}"></span>
                        <div>
                            <div>{{ $info['label'] }}</div>
                            <div style="font-size:11px;font-weight:400;opacity:0.65;margin-top:1px">{{ $info['desc'] }}</div>
                        </div>
                        @if ($equipment->equipment_status === $value)
                        <span style="margin-left:auto;font-size:11px;opacity:0.6">Current</span>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            {{-- PM & CAL toggles --}}
            <div class="border-t border-black/5 pt-4 mb-5 space-y-3">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm text-[#2c2c2c] font-medium">Preventive Maintenance (PM)</span>
                    <input type="checkbox" name="preventive_maintenance_done" value="1"
                           class="sr-only" id="pm-toggle"
                           {{ $equipment->preventive_maintenance_done ? 'checked' : '' }}
                           onchange="toggleTrack(this, 'pm-track')">
                    <div class="toggle-track {{ $equipment->preventive_maintenance_done ? 'active' : '' }}" id="pm-track" onclick="document.getElementById('pm-toggle').click()">
                        <div class="toggle-knob"></div>
                    </div>
                </label>
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm text-[#2c2c2c] font-medium">Calibration (CAL)</span>
                    <input type="checkbox" name="calibration_done" value="1"
                           class="sr-only" id="cal-toggle"
                           {{ ($equipment->calibration_done ?? false) ? 'checked' : '' }}
                           onchange="toggleTrack(this, 'cal-track')">
                    <div class="toggle-track {{ ($equipment->calibration_done ?? false) ? 'active' : '' }}" id="cal-track" onclick="document.getElementById('cal-toggle').click()">
                        <div class="toggle-knob"></div>
                    </div>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-primary text-white rounded-xl font-semibold text-base hover:bg-primary-dark active:scale-[0.98] transition-all">
                Save Changes
            </button>
        </form>
    </div>


    {{-- Back link --}}
    <div class="mt-2 text-center">
        <a href="{{ route('inventory', ['tab' => 'equipment']) }}"
           class="text-sm text-muted hover:text-[#2c2c2c] transition-colors">
            ← Back to Inventory
        </a>
    </div>

</div>

<script>
    function updateSelection(radio) {
        document.querySelectorAll('.status-btn').forEach(function(btn) {
            btn.className = 'status-btn';
        });
        document.getElementById('btn-' + radio.value).classList.add('selected-' + radio.value);
    }
    function toggleTrack(checkbox, trackId) {
        var track = document.getElementById(trackId);
        if (checkbox.checked) {
            track.classList.add('active');
        } else {
            track.classList.remove('active');
        }
    }
</script>

</body>
</html>
