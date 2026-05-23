<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Summary — USeP CLMS</title>
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Base ── */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 24px 32px;
        }

        /* ── Header ── */
        .report-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #2c2c2c;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .report-header h1 { font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
        .report-header p  { font-size: 10px; color: #555; }
        .report-meta      { text-align: right; font-size: 10px; color: #555; }
        .report-meta strong { color: #1a1a1a; }

        /* ── Section ── */
        .section        { margin-bottom: 28px; }
        .section-title  { font-size: 13px; font-weight: 700; color: #1a1a1a; text-transform: uppercase; letter-spacing: 0.05em; padding: 6px 0; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
        .group-title    { font-size: 11px; font-weight: 600; color: #555; margin: 10px 0 4px; }

        /* ── Table ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        thead th {
            background: #f5f5f5;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #555;
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
        }
        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
            color: #2c2c2c;
        }
        tbody tr:last-child td { border-bottom: none; }
        .mono { font-family: 'Courier New', monospace; }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-ok         { background: #d1fae5; color: #065f46; }
        .badge-low        { background: #fef9c3; color: #713f12; }
        .badge-out        { background: #fee2e2; color: #991b1b; }
        .badge-available  { background: #d1fae5; color: #065f46; }
        .badge-in-use     { background: #dbeafe; color: #1e40af; }
        .badge-maintenance{ background: #fef9c3; color: #713f12; }
        .badge-damaged    { background: #f3f4f6; color: #374151; }
        .badge-pm-done    { background: #d1fae5; color: #065f46; }
        .badge-pm-pending { background: #f3f4f6; color: #6b7280; }

        /* ── Summary row ── */
        .summary-row { display: flex; gap: 24px; margin-bottom: 16px; }
        .summary-item { text-align: center; padding: 8px 16px; border: 1px solid #e5e5e5; border-radius: 6px; }
        .summary-item .num { font-size: 20px; font-weight: 700; color: #1a1a1a; }
        .summary-item .lbl { font-size: 9px; text-transform: uppercase; color: #888; letter-spacing: 0.05em; }

        /* ── Empty ── */
        .empty { padding: 12px; text-align: center; color: #aaa; font-style: italic; font-size: 10px; }

        /* ── Footer ── */
        .report-footer {
            border-top: 1px solid #ccc;
            margin-top: 24px;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #999;
        }

        /* ── Print button (screen-only) ── */
        .print-btn {
            position: fixed;
            top: 16px;
            right: 16px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            z-index: 100;
        }
        .print-btn:hover { background: #333; }

        @media print {
            .print-btn { display: none; }
            body { padding: 12px 16px; }
        }

        @page { margin: 12mm; }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨 Print</button>

{{-- ── Report Header ── --}}
<div class="report-header">
    <div>
        <h1>Inventory Summary Report</h1>
        <p>University of Southeastern Philippines — Computer Lab Management System</p>
        <p>This document serves as an official record of laboratory equipment and consumable supplies.</p>
    </div>
    <div class="report-meta">
        <div><strong>Generated:</strong> {{ now()->format('F d, Y — h:i A') }}</div>
        <div><strong>Prepared by:</strong> {{ session('name', session('email', 'Admin')) }}</div>
    </div>
</div>

{{-- ── Summary Totals ── --}}
@php
    $totalEq  = collect($equipmentByType)->flatten(1)->count();
    $totalSup = collect($suppliesByCategory)->flatten(1)->count();
    $lowStock = collect($suppliesByCategory)->flatten(1)->filter(fn($s) => $s['quantity'] <= $s['minimum_stock_threshold'])->count();
@endphp
<div class="summary-row">
    <div class="summary-item"><div class="num">{{ $totalEq }}</div><div class="lbl">Equipment Items</div></div>
    <div class="summary-item"><div class="num">{{ $totalSup }}</div><div class="lbl">Supply Items</div></div>
    <div class="summary-item"><div class="num">{{ $lowStock }}</div><div class="lbl">Low / Out of Stock</div></div>
</div>

{{-- ════════════════════════════════════════════════════
     SECTION 1 — EQUIPMENT / HARDWARE
════════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">Section 1 — Equipment / Hardware</div>

    @forelse ($equipmentByType as $typeName => $items)
    <div class="group-title">{{ $typeName }}</div>
    <table>
        <thead>
            <tr>
                <th style="width:80px">Equip. No.</th>
                <th style="width:100px">Serial No.</th>
                <th>Item Name</th>
                <th>Brand / Model</th>
                <th style="width:80px">Laboratory</th>
                <th style="width:70px">Status</th>
                <th style="width:40px">Qty</th>
                <th style="width:55px">PM</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $eq)
            <tr>
                <td class="mono">{{ $eq['equipment_no'] }}</td>
                <td class="mono">{{ $eq['serial_no'] ?: '—' }}</td>
                <td><strong>{{ $eq['equipment_name'] }}</strong></td>
                <td>{{ $eq['brand'] ?: '' }}{{ ($eq['brand'] && $eq['model_number']) ? ' / ' : '' }}{{ $eq['model_number'] ?: '' }}{{ (!$eq['brand'] && !$eq['model_number']) ? '—' : '' }}</td>
                <td>{{ $eq['lab_name'] }}</td>
                <td>
                    <span class="badge badge-{{ str_replace(' ', '-', $eq['equipment_status']) }}">
                        {{ ucfirst(str_replace('-', ' ', $eq['equipment_status'])) }}
                    </span>
                </td>
                <td class="mono" style="text-align:center">{{ $eq['quantity'] }}</td>
                <td>
                    @if ($eq['preventive_maintenance_done'])
                    <span class="badge badge-pm-done">Done</span>
                    @else
                    <span class="badge badge-pm-pending">Pending</span>
                    @endif
                </td>
                <td>{{ $eq['remarks'] ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <div class="empty">No equipment records found.</div>
    @endforelse
</div>

{{-- ════════════════════════════════════════════════════
     SECTION 2 — CONSUMABLE SUPPLIES
════════════════════════════════════════════════════ --}}
<div class="section">
    <div class="section-title">Section 2 — Consumable Supplies</div>

    @forelse ($suppliesByCategory as $category => $items)
    <div class="group-title">{{ $category }}</div>
    <table>
        <thead>
            <tr>
                <th>Supply Name</th>
                <th style="width:60px;text-align:center">Qty on Hand</th>
                <th style="width:75px;text-align:center">Min. Threshold</th>
                <th style="width:50px">Unit</th>
                <th style="width:80px">Stock Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $sup)
            @php
                $isOut  = $sup['quantity'] === 0;
                $isLow  = !$isOut && $sup['quantity'] <= $sup['minimum_stock_threshold'];
                $stockBadge = $isOut ? 'badge-out' : ($isLow ? 'badge-low' : 'badge-ok');
                $stockLabel = $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'OK');
            @endphp
            <tr>
                <td><strong>{{ $sup['supply_name'] }}</strong></td>
                <td class="mono" style="text-align:center;{{ $isLow || $isOut ? 'color:#991b1b;font-weight:700' : '' }}">{{ $sup['quantity'] }}</td>
                <td class="mono" style="text-align:center">{{ $sup['minimum_stock_threshold'] }}</td>
                <td>{{ $sup['unit'] ?: '—' }}</td>
                <td><span class="badge {{ $stockBadge }}">{{ $stockLabel }}</span></td>
                <td>{{ $sup['remarks'] ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <div class="empty">No supply records found.</div>
    @endforelse
</div>

{{-- ── Footer ── --}}
<div class="report-footer">
    <span>USeP — Computer Lab Management System</span>
    <span>Generated: {{ now()->format('Y-m-d H:i:s') }}</span>
    <span>Confidential — For internal use only</span>
</div>

</body>
</html>
