<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $tab === 'equipment' ? 'Equipment' : 'Supplies' }} Masterlist — Print</title>
    <style>
        @page { size: landscape; margin: 0.5in; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        /* Header Table */
        .doc-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .doc-header td {
            border: 1px solid #000;
            vertical-align: middle;
        }
        .logo-cell {
            width: 120px;
            text-align: center;
            padding: 10px;
        }
        .logo-cell img {
            max-width: 90px;
            height: auto;
        }
        .univ-info-cell {
            text-align: center;
            padding: 10px;
            line-height: 1.3;
        }
        .univ-name {
            font-family: "Times New Roman", Times, serif;
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .meta-cell {
            width: 250px;
            padding: 0;
            vertical-align: top;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 4px 8px;
            font-size: 10px;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }
        .meta-table td:first-child {
            border-right: 1px solid #000;
            width: 40%;
        }

        .doc-title-row td {
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        .updated-date {
            font-weight: normal;
            font-size: 11px;
            display: block;
            margin-top: 4px;
        }

        /* Masterlist Table */
        .masterlist-table {
            width: 100%;
            border-collapse: collapse;
        }
        .masterlist-table th, .masterlist-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        .masterlist-table th {
            text-transform: uppercase;
            font-weight: normal;
            font-size: 10px;
            background-color: #fff;
        }
        .masterlist-table td {
            font-size: 11px;
        }
        .text-left { text-align: left !important; }
        .empty-row td { height: 22px; }

        /* No-print toolbar */
        .no-print {
            margin-bottom: 20px;
            padding: 10px;
            background: #f3f4f6;
            text-align: right;
            border-radius: 4px;
        }
        .no-print button {
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 13px;
        }
        .btn-print { background: #800000; color: white; border: none; }
        .btn-close { background: #fff; border: 1px solid #ccc; margin-left: 8px; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Now</button>
        <button class="btn-close" onclick="window.close()">Close</button>
    </div>

    <!-- Document Header -->
    <table class="doc-header">
        <tr>
            <td class="logo-cell">
                <img src="{{ asset('images/usep-logo.png') }}" alt="USeP Logo">
            </td>
            <td class="univ-info-cell">
                <div>Republic of the Philippines</div>
                <div class="univ-name">University of Southeastern Philippines</div>
                <div>Iñigo St., Bo. Obrero, Davao City 8000</div>
                <div>Telephone: (082) 227-8192</div>
                <div>Website: www.usep.edu.ph</div>
                <div>Email: president@usep.edu.ph</div>
            </td>
            <td class="meta-cell">
                <table class="meta-table">
                    <tr><td>Form No.</td><td>PM-USeP-PMS-01a</td></tr>
                    <tr><td>Issue Status</td><td>02</td></tr>
                    <tr><td>Revision No.</td><td>01</td></tr>
                    <tr><td>Date Effective</td><td>01 March 2018</td></tr>
                    <tr><td>Approved by</td><td>President</td></tr>
                </table>
            </td>
        </tr>
        <tr class="doc-title-row">
            <td colspan="3">
                MASTERLIST OF {{ $tab === 'equipment' ? 'EQUIPMENT' : 'SUPPLIES' }}
                <span class="updated-date">Updated as of: {{ date('F d, Y') }}</span>
            </td>
        </tr>
    </table>

    <!-- Main List Table -->
    @if ($tab === 'equipment')
    <table class="masterlist-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">NO.</th>
                <th rowspan="2" style="width: 100px;">EQUIPMENT NO.</th>
                <th rowspan="2" class="text-left">EQUIPMENT'S NAME</th>
                <th rowspan="2" class="text-left" style="width: 150px;">MODEL NAME/NUMBER</th>
                <th rowspan="2" style="width: 120px;">AREA ASSIGNMENT</th>
                <th colspan="2">NEEDS</th>
                <th rowspan="2" style="width: 100px;">STATUS</th>
            </tr>
            <tr>
                <th style="width: 40px;">PM</th>
                <th style="width: 40px;">CAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipmentRows as $i => $eq)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $eq['equipment_no'] }}</td>
                <td class="text-left">{{ $eq['equipment_name'] }}</td>
                <td class="text-left">{{ $eq['brand'] ? $eq['brand'] . ' / ' : '' }}{{ $eq['model_number'] ?: '' }}{{ (!$eq['brand'] && !$eq['model_number']) ? '—' : '' }}</td>
                <td>{{ $eq['lab_name'] }}</td>
                <td>{{ $eq['preventive_maintenance_done'] ? '✔' : '' }}</td>
                <td>{{ ($eq['calibration_done'] ?? false) ? '✔' : '' }}</td>
                <td>{{ ucfirst(str_replace('-', ' ', $eq['equipment_status'])) }}</td>
            </tr>
            @empty
            {{-- Show empty table structure even with no data --}}
            @endforelse
            @for ($i = 0; $i < max(5, 10 - count($equipmentRows)); $i++)
            <tr class="empty-row">
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    @else
    <table class="masterlist-table">
        <thead>
            <tr>
                <th style="width: 40px;">NO.</th>
                <th class="text-left">SUPPLY NAME</th>
                <th style="width: 120px;">CATEGORY</th>
                <th style="width: 80px;">UNIT</th>
                <th style="width: 120px;">STATUS</th>
                <th class="text-left" style="width: 200px;">REMARKS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusLabels = [
                    'fully_stocked' => 'Fully Stocked',
                    'in_stock'      => 'In Stock',
                    'low_stock'     => 'Low Stock',
                    'out_of_stock'  => 'Out of Stock',
                ];
            @endphp
            @forelse ($suppliesRows as $i => $sup)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="text-left">{{ $sup['supply_name'] }}</td>
                <td>{{ $sup['category'] }}</td>
                <td>{{ $sup['unit'] ?: '—' }}</td>
                <td>{{ $statusLabels[$sup['status']] ?? $sup['status'] }}</td>
                <td class="text-left">{{ $sup['remarks'] ?: '—' }}</td>
            </tr>
            @empty
            {{-- Show empty table structure even with no data --}}
            @endforelse
            @for ($i = 0; $i < max(5, 10 - count($suppliesRows)); $i++)
            <tr class="empty-row">
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>
    @endif

</body>
</html>
