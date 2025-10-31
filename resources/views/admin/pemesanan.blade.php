@extends('layouts.admin')
@section('title', 'Pemesanan Tiket')
@section('content')
    <div class="py-6 px-6">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <div class="mb-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold mb-4">Daftar Pemesanan</h1>
                <a href="{{ route('pemesanan.create') }}"
                    class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition">
                    + Tambah Pemesanan
                </a>
            </div>

            {{-- Form Filter --}}
            <form method="GET" action="{{ route('pemesanan.index') }}" class="flex flex-row items-end flex-wrap gap-4">
                <!-- Filter Rute -->
                <div class="w-48">
                    <label for="rute" class="block mb-1 text-sm font-medium text-gray-700">Rute</label>
                    <select name="rute" id="rute" onchange="this.form.submit()"
                        class="border border-gray-300 rounded w-full p-2">
                        <option value="">-- Semua Rute --</option>
                        @foreach ($rutes as $rute)
                            <option value="{{ $rute->id_rute }}" {{ request('rute') == $rute->id_rute ? 'selected' : '' }}>
                                {{ $rute->asal }} - {{ $rute->tujuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div class="w-40">
                    <label for="tanggal" class="block mb-1 text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                        class="border border-gray-300 rounded w-full p-2">
                </div>

                <!-- Filter Jam -->
                <div class="w-32">
                    <label for="jam" class="block mb-1 text-sm font-medium text-gray-700">Jam</label>
                    <select name="jam" id="jam" class="border border-gray-300 rounded w-full p-2">
                        <option value="">-- Semua Jam --</option>
                        @foreach ($jams as $jam)
                            <option value="{{ $jam }}" {{ request('jam') == $jam ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($jam)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                {{-- <div class="w-40">
                    <label for="status" class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="border border-gray-300 rounded w-full p-2">
                        <option value="">-- Semua Status --</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
            {{ ucfirst($status) }}
            </option>
            @endforeach
            </select>
    </div> --}}

                <!-- Tombol Filter -->
                <div class="self-end mb-1">
                    <button type="submit"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition">
                        Filter
                    </button>
                </div>
            </form>



            {{-- Tabel Pemesanan --}}
            <div class="overflow-x-auto rounded-xl shadow mt-2">
                <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Nama Penumpang</th>
                            <th class="border px-4 py-2">Asal</th>
                            <th class="border px-4 py-2">Tujuan</th>
                            <th class="border px-4 py-2">No Kursi</th>
                            <th class="border px-4 py-2">Tanggal Pemesanan</th>
                            <th class="border px-4 py-2">Nama Supir</th>
                            <th class="border px-4 py-2">Nomer Kendaraan</th>
                            <th class="border px-4 py-2">Jam Keberangkatan</th>
                            <th class="border px-4 py-2">Tanggal Keberangkatan</th>
                            <th class="border px-4 py-2">Total Pembayaran</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pemesanans as $pemesanan)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2">
                                    {{ $pemesanan->detail_pemesanan->pluck('penumpang.nama_penumpang')->join(', ') ?: '-' }}
                                </td>

                                <td class="border px-4 py-2">{{ $pemesanan->jadwal->rute->asal ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $pemesanan->jadwal->rute->tujuan ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    {{ $pemesanan->detail_pemesanan->pluck('kursi.no_kursi')->join(', ') ?: '-' }}
                                </td>
                                <td class="border px-4 py-2">{{ $pemesanan->tanggal_pemesanan ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $pemesanan->jadwal->supir->nama_supir ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $pemesanan->jadwal->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    {{ substr($pemesanan->jadwal->jam_keberangkatan ?? '-', 0, 5) }}</td>
                                <td class="border px-4 py-2">{{ $pemesanan->tanggal_keberangkatan ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    Rp.{{ $pembayaran = optional($pemesanan->pembayaran)->jumlah_pembayaran ? number_format(optional($pemesanan->pembayaran)->jumlah_pembayaran, 0, ',', '.') : '-' }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('pemesanan.edit', $pemesanan->id_pemesanan) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</a>

                                        <form method="POST"
                                            action="{{ route('pemesanan.destroy', $pemesanan->id_pemesanan) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data pemesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
