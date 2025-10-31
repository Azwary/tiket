@extends('layouts.admin')
@section('title', 'Konfirmasi Pembayaran')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Konfirmasi Pembayaran</h1>

        <div class="bg-white shadow-xl rounded-xl p-8 space-y-8 text-gray-700">

            {{-- Informasi Tiket --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Informasi Penumpang</h2>
                    @php
                        $penumpangs = $data->pemesanan?->detail_pemesanan ?? collect();
                    @endphp
                    <p><span class="font-medium">No. Tiket:</span> {{ $data->pemesanan?->id_pemesanan ?? '-' }}</p>
                    @if ($penumpangs->isNotEmpty())
                        @foreach ($penumpangs as $index => $detail)
                            <p>
                                <span class="font-medium">Nama {{ $index + 1 }}:</span>
                                {{ $detail->penumpang?->nama_penumpang ?? '-' }}
                            </p>
                            {{-- <p>
                                <span class="font-medium">No. Tiket {{ $index + 1 }}:</span>
                {{ $detail->id_detail_pemesanan ?? ($data->pemesanan->id_pemesanan ?? '-') }}
                </p> --}}
                        @endforeach
                    @else
                        <p>-</p>
                    @endif
                </div>


                <div>
                    <h2 class="text-lg font-semibold mb-2">Waktu Keberangkatan</h2>
                    <p><span class="font-medium">Tanggal:</span> {{ $data->pemesanan?->tanggal_keberangkatan ?? '-' }}</p>
                    <p><span class="font-medium">Jam:</span> {{ $data->pemesanan?->jadwal?->jam_keberangkatan ?? '-' }}</p>
                </div>
            </div>

            {{-- Informasi Rute --}}
            <div>
                <h2 class="text-lg font-semibold mb-2">Rute Perjalanan</h2>
                <p>
                    {{ $data->pemesanan?->jadwal?->rute?->asal ?? '-' }}
                    <span class="mx-2">→</span>
                    {{ $data->pemesanan?->jadwal?->rute?->tujuan ?? '-' }}
                </p>
            </div>

            {{-- Informasi Kursi --}}
            <div>
                <h2 class="text-lg font-semibold mb-2">Detail Kursi</h2>
                <p>
                    @php
                        $kursis = $data->pemesanan?->detail_pemesanan;
                    @endphp

                    @if ($kursis && $kursis->count())
                        {{ $kursis->pluck('kursi.no_kursi')->filter()->join(', ') }}
                    @else
                        <span class="text-red-500">Data kursi tidak tersedia.</span>
                    @endif
                </p>
            </div>

            {{-- Total Harga --}}
            <div>
                <h2 class="text-lg font-semibold mb-2">Total Pembayaran</h2>
                <p>Rp{{ number_format($data->jumlah_pembayaran ?? 0, 0, ',', '.') }}</p>
            </div>

            {{-- Bukti Pembayaran --}}
            <div>
                <h2 class="text-lg font-semibold mb-2">Bukti Pembayaran</h2>
                @php
                    $buktiPath = $data->upload_bukti ?? null; // gunakan field di tabel pembayaran
                @endphp
                <div class="flex justify-center">
                    @if ($buktiPath && file_exists(public_path($buktiPath)))
                        <img src="{{ asset($buktiPath) }}" class="w-64 h-auto rounded-lg border" alt="Bukti Pembayaran">
                    @else
                        <p class="text-red-500">Belum ada bukti pembayaran.</p>
                    @endif
                </div>
            </div>


            {{-- Tombol Konfirmasi --}}
            <div class="flex justify-center gap-6 pt-4">
                <form action="{{ route('pembayaran.konfirmasi', $data->id_pembayaran) }}" method="POST"
                    class="flex gap-4">
                    @csrf
                    <button type="submit" name="action" value="berhasil"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                        ✅ Konfirmasi
                    </button>
                    <button type="submit" name="action" value="ditolak"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                        ❌ Tolak
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection
