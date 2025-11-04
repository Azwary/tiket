@extends('layouts.admin')
@section('title', 'Pembayaran')
@section('content')
    <div class="p-6">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <h1 class="text-2xl font-bold mb-4">Konfirmasi Pembayaran</h1>

            {{-- Form Filter --}}
            <form method="GET" action="{{ route('pembayaran.index') }}" class="flex flex-row items-end w-7/12 gap-2">
                <div class="mb-4 w-full">
                    <label for="status" class="block mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded w-full p-2">
                        <option value="">-- Semua Status --</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="berhasil" {{ request('status') == 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="mb-4 w-full">
                    <label for="tanggal" class="block mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                        class="border border-gray-300 rounded w-full p-2">
                </div>

                <div class="mb-4">
                    <button type="submit"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition">Filter</button>
                </div>
            </form>

            {{-- Tabel Konfirmasi --}}
            <div class="overflow-x-auto rounded-xl shadow mt-2">
                <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Nama Penumpang</th>
                            <th class="px-4 py-2 border">Tanggal Pemesanan</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @forelse ($pembayaran as $index => $item) --}}
                        @forelse ($pembayaran->sortByDesc('created_at')->values() as $index => $item)
                            <tr class="text-center {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 border text-left">
                                    @php
                                        $penumpangs = $item->pemesanan?->detail_pemesanan ?? collect();
                                    @endphp

                                    @if ($penumpangs->isNotEmpty())
                                        {{ $penumpangs->map(fn($detail) => $detail->penumpang?->nama_penumpang ?? '-')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>


                                <td class="px-4 py-2 border"> {{ optional($item->pemesanan)->tanggal_pemesanan ?? '-' }}
                                </td>
                                @php
                                    $status = optional($item)->status_konfirmasi;
                                    $warna = match ($status) {
                                        'berhasil' => 'text-green-600 font-semibold',
                                        'ditolak' => 'text-red-600 font-semibold',
                                        'menunggu' => 'text-gray-500 italic',
                                        default => 'text-gray-400',
                                    };
                                @endphp
                                <td class="px-4 py-2 border {{ $warna }}">
                                    {{ optional($item)->status_konfirmasi ?? '-' }}</td>
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('pembayaran.show', $item->id_pembayaran) }}"
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Cek Bukti</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
