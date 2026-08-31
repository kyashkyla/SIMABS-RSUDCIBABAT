<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
        }

        .header p {
            font-size: 11px;
            color: #64748b;
            margin: 4px 0 0;
        }

        .summary {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }

        .summary td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .summary .label {
            font-size: 9px;
            color: #64748b;
            display: block;
        }

        .summary .value {
            font-size: 15px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }

        table.data th {
            background: #f1f5f9;
            font-size: 10px;
            text-transform: uppercase;
        }

        .status-hadir { color: #059669; font-weight: bold; }
        .status-terlambat { color: #d97706; font-weight: bold; }
        .status-tidak-hadir { color: #dc2626; font-weight: bold; }
        .status-alternatif { color: #2563eb; font-weight: bold; }

        .footer {
            margin-top: 24px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Absensi Pegawai</h1>
        <p>RSUD Cibabat &mdash; Periode {{ $start }} s/d {{ $end }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Hadir</span>
                <span class="value">{{ $summary['hadir'] }}</span>
            </td>
            <td>
                <span class="label">Terlambat</span>
                <span class="value">{{ $summary['terlambat'] }}</span>
            </td>
            <td>
                <span class="label">Tidak Hadir</span>
                <span class="value">{{ $summary['tidak_hadir'] }}</span>
            </td>
            <td>
                <span class="label">Total Data</span>
                <span class="value">{{ $summary['total'] }}</span>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Departemen</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Metode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $r)
                <tr>
                    <td>{{ $r['date'] }}</td>
                    <td>{{ $r['nip'] }}</td>
                    <td>{{ $r['name'] }}</td>
                    <td>{{ $r['department'] }}</td>
                    <td>{{ $r['checkIn'] }}</td>
                    <td>{{ $r['checkOut'] }}</td>
                    <td>{{ $r['method'] }}</td>
                    <td class="status-{{ \Illuminate\Support\Str::slug($r['status']) }}">
                        {{ $r['status'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94a3b8;">
                        Tidak ada data absensi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB &mdash; SIMABS RSUD Cibabat
    </p>

</body>
</html>
