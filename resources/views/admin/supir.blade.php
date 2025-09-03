@extends('layouts.admin')
@section('title', 'Manajemen Supir & Kendaraan')

@section('content')
    <div class="p-6 space-y-6">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold mb-4">Manajemen Supir & Kendaraan</h1>
                <div class="relative">
                    <button id="dropdownButton" type="button"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-20">
                        <a href="{{ route('supir.create') }}?tipe=supir"
                            class="block px-4 py-2 hover:bg-gray-100 text-gray-700">+ Supir</a>
                        <a href="{{ route('supir.create') }}?tipe=kendaraan"
                            class="block px-4 py-2 hover:bg-gray-100 text-gray-700">+ Kendaraan</a>
                    </div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow my-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabel Supir --}}
            <div class="mt-2">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Supir</h2>
                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Supir</th>
                                <th class="px-4 py-2 border">No HP</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supirs as $index => $supir)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $supir->nama_supir }}</td>
                                    <td class="px-4 py-2 border">{{ $supir->no_hp }}</td>
                                    <td class="px-4 py-2 border">{{ $supir->status ?? '-' }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('supir.edit', $supir->id_supir) }}?tipe=supir"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            <form action="{{ route('supir.destroy', $supir->id_supir) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus supir ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data supir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Kendaraan --}}
            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Kendaraan</h2>
                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Plat Nomor</th>
                                <th class="px-4 py-2 border">Supir</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kendaraans as $index => $kendaraan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $kendaraan->plat_nomor }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ $kendaraan->supir->nama_supir ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 border">{{ $kendaraan->status ?? '-' }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('supir.edit', $kendaraan->id_kendaraan) }}?tipe=kendaraan"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            <form
                                                action="{{ route('supir.destroy', $kendaraan->id_kendaraan) }}?tipe=kendaraan"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
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
                                    <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data kendaraan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Tabel Jadwal --}}
            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Jadwal</h2>
                {{-- Filter Jadwal Otomatis --}}
                <div class="mb-4 flex flex-wrap gap-3 items-end">
                    <form id="filterForm" action="{{ route('supir.jadwal') }}" method="GET"
                        class="flex flex-wrap gap-3 items-end">
                        <div>
                            <label for="rute_filter" class="block text-sm font-medium text-gray-700">Rute</label>
                            <select name="rute_id" id="rute_filter"
                                onchange="document.getElementById('filterForm').submit()"
                                class="px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">-- Semua Rute --</option>
                                @foreach ($rutes as $rute)
                                    <option value="{{ $rute->id_rute }}"
                                        {{ request('rute_id') == $rute->id_rute ? 'selected' : '' }}>
                                        {{ $rute->asal }} - {{ $rute->tujuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="jam_filter" class="block text-sm font-medium text-gray-700">Jam
                                Keberangkatan</label>
                            <select name="jam_keberangkatan" id="jam_filter"
                                onchange="document.getElementById('filterForm').submit()"
                                class="px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">-- Semua Jam --</option>
                                @foreach ($jams as $jam)
                                    <option value="{{ $jam }}"
                                        {{ request('jam_keberangkatan') == $jam ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($jam)->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Rute</th>
                                <th class="px-4 py-2 border">Supir</th>
                                <th class="px-4 py-2 border">Kendaraan</th>
                                <th class="px-4 py-2 border">Jam Keberangkatan</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwals as $index => $jadwal)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $jadwal->rute->asal ?? '-' }} -
                                        {{ $jadwal->rute->tujuan ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $jadwal->supir->nama_supir ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $jadwal->kendaraan->plat_nomor ?? '-' }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_keberangkatan)->format('H:i') }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('supir.jadwal.edit', $jadwal->id_jadwal) }}"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            {{-- <form action="{{ route('supir.jadwal.destroy', $jadwal->id_jadwal) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data jadwal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');

            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
