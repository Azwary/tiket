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
        <p>LAPORAN PENJUALAN BULANAN </p>
        {{-- PERUSAHAAN: FIFAFEL TRANSPORTASI --}}
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
    <p><strong>Priode:</strong>
        @if ($bulan)
            {{ Carbon::parse($bulan)->translatedFormat('F Y') }}
        @else
            Semua Bulan
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
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $pemesanan)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($pemesanan->tanggal_pemesanan)->format('d-m-Y') }}</td>
                    <td>{{ $pemesanan->penumpang->nama_penumpang ?? '-' }}</td>
                    <td>
                        {{ $pemesanan->jadwal->rute->asal ?? '-' }} -
                        {{ $pemesanan->jadwal->rute->tujuan ?? '-' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($pemesanan->tanggal_keberangkatan)->format('d-m-Y') }}</td>
                    <td>{{ $pemesanan->jadwal->jam_keberangkatan ?? '-' }}</td>
                    <td>{{ $pemesanan->pembayaran->status_konfirmasi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data pemesanan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <div class="signature-box">
            <p>Padang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,</p>
            <!-- <p style="margin-bottom: 60px;">Petugas</p> -->
            <!-- {{-- <img src="{{ public_path('tanda_tangan.png') }}" alt="Tanda Tangan" height="80"> --}} -->
            <br>
            <p>----------------------------</p>
            <p>{{ auth('admin')->user()->nama_admin }} / {{ auth('admin')->user()->role }}</p>
            <!-- <p>{{ ucfirst(auth('admin')->user()->nama_admin) }}</p> -->
        </div>
    </div>

</body>

</html>
