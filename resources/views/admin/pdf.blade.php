<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan Perjalanan Travel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signature {
            width: 100%;
            margin-top: 60px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            width: 250px;
            text-align: center;
        }

        .signature-box p {
            margin: 4px 0;
        }
    </style>
</head>

<body>

    <div class="header-title">
        <p>PT. FIFAFEL TRANS</p>
        <p>LAPORAN PENJUALAN BULANAN</p>
    </div>

    @php
        use Carbon\Carbon;
    @endphp

    <p><strong>Rute:</strong>
        @if ($rute_terpilih)
            {{ $rute_terpilih->asal ?? '-' }} - {{ $rute_terpilih->tujuan ?? '-' }}
        @else
            Semua rute
        @endif
    </p>
    <p><strong>Periode:</strong>
        @if ($dari_tanggal && $sampai_tanggal)
            {{ Carbon::parse($dari_tanggal)->format('d-m-Y') }} s/d
            {{ Carbon::parse($sampai_tanggal)->format('d-m-Y') }}
        @else
            Semua Periode
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pemesanan</th>
                <th>Nama Penumpang</th>
                <th>Rute Perjalanan</th>
                <th>Tanggal Keberangkatan</th>
                <th>Jam Keberangkatan</th>
                <th>Status Pembayaran</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @forelse ($pemesanans as $i => $pemesanan)
                @php
                    $jumlah = optional($pemesanan->pembayaran)->jumlah_pembayaran ?? 0;
                    $totalKeseluruhan += $jumlah;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ Carbon::parse($pemesanan->tanggal_pemesanan)->format('d-m-Y') }}</td>
                    <td>{{ $pemesanan->penumpang->nama_penumpang ?? '-' }}</td>
                    <td>
                        {{ $pemesanan->jadwal->rute->asal ?? '-' }} -
                        {{ $pemesanan->jadwal->rute->tujuan ?? '-' }}
                    </td>
                    <td>{{ Carbon::parse($pemesanan->tanggal_keberangkatan)->format('d-m-Y') }}</td>
                    <td>{{ substr($pemesanan->jadwal->jam_keberangkatan ?? '-', 0, 5) }}</td>
                    <td>{{ $pemesanan->pembayaran->status_konfirmasi ?? '-' }}</td>
                    <td>Rp.{{ number_format($jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data pemesanan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">Total Keseluruhan</th>
                <th>Rp.{{ number_format($totalKeseluruhan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div class="signature-box">
            <p>Padang, {{ Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,</p>
            <br>
            <p>----------------------------</p>
            <p>{{ auth('admin')->user()->nama_admin }} / {{ auth('admin')->user()->role }}</p>
        </div>
    </div>

</body>

</html>
