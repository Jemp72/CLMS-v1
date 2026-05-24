<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ $equipment->equipment_name }} — Update Status</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Mobile-first status card */
        body { background: #f7f7f7; min-height: 100dvh; }
        .status-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 18px 20px;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #fff;
            color: #2c2c2c;
            text-align: left;
        }
        .status-btn:active { transform: scale(0.98); }
        .status-btn.selected-available  { border-color: #22c55e; background: #f0fdf4; color: #166534; }
        .status-btn.selected-in-use     { border-color: var(--color-primary, #2563eb); background: #eff6ff; color: #1e40af; }
        .status-btn.selected-maintenance{ border-color: #eab308; background: #fefce8; color: #713f12; }
        .status-btn.selected-damaged    { border-color: #6b7280; background: #f9fafb; color: #374151; }
        .dot {
            width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
        }
        .dot-available   { background: #22c55e; }
        .dot-in-use      { background: #2563eb; }
        .dot-maintenance { background: #eab308; }
        .dot-damaged     { background: #6b7280; }
    </style>
</head>
<body>

<div class="min-h-dvh flex flex-col" style="max-width:480px;margin:0 auto;padding:24px 16px;">

    {{-- Header --}}
    <div class="mb-6">
        <p class="text-xs text-muted uppercase tracking-widest mb-1">QR Scan — Status Update</p>
        <h1 class="text-xl font-heading font-semibold text-[#2c2c2c] leading-tight">{{ $equipment->equipment_name }}</h1>
        <p class="text-sm text-muted mt-0.5">{{ $equipment->equipment_no }}
            @if($equipment->serial_no) · SN: {{ $equipment->serial_no }} @endif
        </p>
        <p class="text-sm text-muted">{{ $equipment->lab_name }}</p>
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

    {{-- Status form --}}
    <form method="POST" action="{{ route('equipment.update.status', $equipment->equipment_id) }}">
        @csrf

        <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Select New Status</p>

        <div class="space-y-3 mb-6" id="status-options">
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
                <div class="status-btn {{ $equipment->equipment_status === $value ? 'selected-' . str_replace('-', '-', $value) : '' }}"
                     id="btn-{{ $value }}">
                    <span class="dot {{ $info['dot'] }}"></span>
                    <div>
                        <div>{{ $info['label'] }}</div>
                        <div style="font-size:12px;font-weight:400;opacity:0.65;margin-top:1px">{{ $info['desc'] }}</div>
                    </div>
                    @if ($equipment->equipment_status === $value)
                    <span style="margin-left:auto;font-size:11px;opacity:0.6">Current</span>
                    @endif
                </div>
            </label>
            @endforeach
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-4 bg-primary text-white rounded-xl font-semibold text-base hover:bg-primary-dark active:scale-[0.98] transition-all">
            Save Status
        </button>
    </form>

    {{-- Back link --}}
    <div class="mt-5 text-center">
        <a href="{{ route('inventory', ['tab' => 'equipment']) }}"
           class="text-sm text-muted hover:text-[#2c2c2c] transition-colors">
            ← Back to Inventory
        </a>
    </div>

</div>

<script>
    function updateSelection(radio) {
        // Clear all selections
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.className = 'status-btn';
        });
        // Apply selected class
        const selected = radio.value.replace('_', '-');
        document.getElementById('btn-' + radio.value).classList.add('status-btn', 'selected-' + radio.value);
    }
</script>

</body>
</html>
