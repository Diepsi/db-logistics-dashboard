<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengiriman - DB Logistics</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        h2 { font-size: 13px; color: #4b5563; font-weight: normal; margin: 0 0 16px; }
        .brand { color: #059669; font-weight: bold; }
        .kpi-grid { display: flex; gap: 12px; margin-bottom: 20px; }
        .kpi {
            flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px;
        }
        .kpi .label { font-size: 9px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; }
        .kpi .value { font-size: 20px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #111827; color: #fff; text-align: left; padding: 7px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; }
        td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .muted { color: #6b7280; }
        .footer { margin-top: 18px; color: #9ca3af; font-size: 9px; }
    </style>
</head>
<body>
    <h1><span class="brand">DB Logistics</span> — Laporan Operasional Pengiriman</h1>
    <h2>Dicetak: {{ now()->format('d M Y H:i') }} ·resources/views/reports/pdf.blade.php</h2>

    <div class="kpi-grid">
        <div class="kpi"><div class="label">Total Resi</div><div class="value">{{ number_format($kpis['totalShipments']) }}</div></div>
        <div class="kpi"><div class="label">Completed</div><div class="value" style="color:#059669;">{{ number_format($kpis['completed']) }}</div></div>
        <div class="kpi"><div class="label">On Delivery</div><div class="value" style="color:#2563eb;">{{ number_format($kpis['onDelivery']) }}</div></div>
        <div class="kpi"><div class="label">Undelivered</div><div class="value" style="color:#dc2626;">{{ number_format($kpis['undelivered']) }}</div></div>
        <div class="kpi"><div class="label">SLA Achievement</div><div class="value" style="color:#059669;">{{ $kpis['slaAchievementRate'] }}%</div></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Resi</th>
                <th>Manifest</th>
                <th>NPSN</th>
                <th>Sekolah</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Tanggal HO</th>
                <th>Vendor LM</th>
                <th>Status</th>
                <th>SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $shipment)
                <tr>
                    <td>{{ $shipment->waybill_no }}</td>
                    <td>{{ $shipment->manifest_no ?? '-' }}</td>
                    <td>{{ $shipment->npsn ?? '-' }}</td>
                    <td>{{ $shipment->school_name ?? '-' }}</td>
                    <td>{{ $shipment->province ?? '-' }}</td>
                    <td>{{ $shipment->city_regency ?? '-' }}</td>
                    <td>{{ $shipment->ho_date?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $shipment->vendor_lm ?? '-' }}</td>
                    <td>{{ $shipment->final_status }}</td>
                    <td>{{ $shipment->is_within_sla ? 'On Time' : 'Over SLA' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="muted">Tidak ada data untuk filter yang dipilih.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($shipments->count() >= 500)
        <p class="footer">* Daftar pengiriman dibatasi 500 baris terbaru pada ekspor PDF. Gunakan Export Excel untuk data lengkap.</p>
    @endif

    <p class="footer">Dokumen ini dihasilkan otomatis oleh Dashboard Monitoring & Analisis Operasional DB Logistics.</p>
</body>
</html>
