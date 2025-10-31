@extends('layouts.admin')
@section('title', 'Laporan Bulanan')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <h1 class="text-2xl font-bold mb-6">Laporan Bulanan</h1>

            {{-- Filter --}}
            <form id="filterForm" method="GET" action="{{ route('laporan.index') }}"
                class="flex flex-wrap gap-4 items-center mb-6">

                <select name="rute" class="border border-gray-300 rounded px-3 py-2"
                    onchange="document.getElementById('filterForm').submit()">
                    <option value="">Pilih Rute</option>

                    @if (auth('admin')->user()->role === 'umum')
                        <option value="semua" {{ request('rute') == 'semua' ? 'selected' : '' }}>Semua Rute</option>
                    @endif

                    @foreach ($rutes as $rute)
                        <option value="{{ $rute->id_rute }}" {{ request('rute') == $rute->id_rute ? 'selected' : '' }}>
                            {{ $rute->asal }} - {{ $rute->tujuan }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="dari_tanggal" class="border border-gray-300 rounded px-3 py-2"
                    value="{{ request('dari_tanggal') }}" onchange="document.getElementById('filterForm').submit()">

                <input type="date" name="sampai_tanggal" class="border border-gray-300 rounded px-3 py-2"
                    value="{{ request('sampai_tanggal') }}" onchange="document.getElementById('filterForm').submit()">

                <a href="{{ request('rute') ? route('laporan.unduh', request()->all()) : '#' }}"
                    class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition {{ !request('rute') ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                    <i data-lucide="file-down" class="w-5 h-5"></i>
                    Unduh PDF
                </a>
            </form>

            {{-- Tabel --}}
            {{-- Tabel hanya tampil jika filter sudah diisi --}}
            @if (request()->filled('rute') || (request()->filled('dari_tanggal') && request()->filled('sampai_tanggal')))
                <div class="overflow-x-auto rounded-xl shadow mt-2">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Penumpang</th>
                                <th class="px-4 py-2 border">Asal</th>
                                <th class="px-4 py-2 border">Tujuan</th>
                                <th class="px-4 py-2 border">Tanggal Keberangkatan</th>
                                <th class="px-4 py-2 border">Jam Keberangkatan</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalKeseluruhan = 0; @endphp
                            @forelse ($pemesanans as $no => $p)
                                @php
                                    $jumlah = optional($p->pembayaran)->jumlah_pembayaran ?? 0;
                                    $totalKeseluruhan += $jumlah;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $no + 1 }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ $p->detail_pemesanan->pluck('penumpang.nama_penumpang')->join(', ') ?: '-' }}
                                    </td>
                                    <td class="px-4 py-2 border">{{ $p->jadwal->rute->asal ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $p->jadwal->rute->tujuan ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $p->tanggal_keberangkatan ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ substr($p->jadwal->jam_keberangkatan ?? '-', 0, 5) }}
                                    </td>
                                    <td class="px-4 py-2 border">{{ $p->pembayaran->status_konfirmasi ?? '-' }}</td>
                                    <td class="px-4 py-2 border">Rp.{{ number_format($jumlah, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-4 text-center text-gray-500">Tidak ada data untuk
                                        ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($pemesanans->count())
                            <tfoot>
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="7" class="px-4 py-2 text-right border">Total Keseluruhan:</td>
                                    <td class="px-4 py-2 border">Rp.{{ number_format($totalKeseluruhan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif

        </div>
    </div>
@endsection
