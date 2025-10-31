@extends('layouts.admin')
@section('title', 'Tambah Pemesanan')
@section('content')
@php
$seats = $seats ?? [1, 2, null, 'driver', null, 3, 4, 5, 6, null, 7, 8, 9, null, 10, 11, 12, 13, 14, 15];

$kursi_terisi = $kursi_terisi ?? [];
$rutes = $rutes ?? [];
$jadwals = $jadwals ?? [];
$kursis = $kursis ?? [];
$id_rute = $id_rute ?? null;
$tanggal = $tanggal ?? null;
$id_jadwal = $id_jadwal ?? null;
@endphp
@php
$sudahPilih = request()->has('rute') && request()->has('tanggal') && request()->has('jam');
$selected = old('kursi', []);
$kursi_map = [];
if ($sudahPilih) {
foreach ($kursi_terisi as $no_kursi) {
$kursi_map[(string) $no_kursi] = 'terisi';
}
}
@endphp

<div class="py-8 px-6">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl mx-auto">
        <div class="flex flex-row gap-5">
            <form method="GET" action="{{ route('penumpang.showKursi') }}" class="flex flex-col w-6/12  p-2 ">
                <div>
                    <label for="rute" class="block text-sm font-medium text-gray-700 mb-1">Pilih Rute</label>
                    <select name="rute" id="rute"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option selected disabled>Pilih Rute</option>

                        @foreach ($rutes as $rute)
                        <option value="{{ $rute->id_rute }}"
                            {{ request('rute') == $rute->id_rute ? 'selected' : '' }}>
                            {{ $rute->asal }} - {{ $rute->tujuan }}
                            (Rp{{ number_format($rute->harga, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>


                </div>

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}"
                        min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="jam" class="block text-sm font-medium text-gray-700 mb-1">Jam Keberangkatan</label>
                    <select name="jam" id="jam"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                        {{ request('rute') ? '' : 'disabled' }}>
                        <option selected disabled>
                            {{ request('rute') ? 'Pilih Jam' : 'Pilih Rute terlebih dahulu' }}
                        </option>

                        @if (request('rute') && count($jadwals))
                        @foreach ($jadwals as $jadwal)
                        <option value="{{ $jadwal->id_jadwal }}"
                            {{ request('jam') == $jadwal->id_jadwal ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($jadwal->jam_keberangkatan)->format('H:i') }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-span-3">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">
                        Tampilkan Data
                    </button>
                </div>
                @if (!$sudahPilih)
                <div class="mt-10 text-center text-gray-500  mb-4">
                    Silakan pilih Rute, Tanggal, dan Jam terlebih dahulu untuk mengaktifkan pemilihan kursi.

                </div>
                @endif
            </form>



            <form method="POST" action="{{ route('pemesanan.store') }}" class="flex flex-col w-6/12  p-2 ">
                @csrf
                <input type="hidden" name="id_rute" value="{{ request('rute') }}">
                <input type="hidden" name="id_jadwal" value="{{ request('jam') }}">
                <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">


                <div class="mb-4" id="nama-penumpang-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penumpang</label>
                    <!-- Input dinamis akan muncul di sini -->
                </div>


                <div class="flex justify-center mb-6">
                    <div class="grid grid-cols-4 gap-4">
                        @foreach ($seats as $seat)
                        @if ($seat === null)
                        <div></div>
                        @elseif ($seat === 'driver')
                        <div class="flex items-center justify-center text-2xl"><svg
                                xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-steering-wheel">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M12 14l0 7" />
                                <path d="M10 12l-6.75 -2" />
                                <path d="M14 12l6.75 -2" />
                            </svg></div>
                        @else
                        @php
                        $status = $sudahPilih ? $kursi_map[(string) $seat] ?? 'tersedia' : 'belum';
                        // $isSelected = $seat == $selected;
                        $isSelected = in_array($seat, $selected);

                        @endphp

                        <label class="cursor-pointer">
                            {{-- <input type="checkbox" name="kursi[]" value="{{ $seat }}" class="sr-only"
                            {{ $isSelected ? 'checked' : '' }}
                            {{ in_array($status, ['terisi', 'belum']) ? 'disabled' : '' }}> --}}
                            <input type="checkbox" name="kursi[]" value="{{ $seat }}" class="sr-only"
                                {{ $isSelected ? 'checked' : '' }}
                                {{ in_array($status, ['terisi', 'belum']) ? 'disabled' : '' }}>



                            <div
                                class="seat-box text-sm font-semibold text-center px-3 py-2 rounded-md transition-colors duration-300
                                            {{ $status === 'terisi' || $status === 'belum' ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : '' }}
                                            {{ $isSelected ? 'bg-red-600 text-white' : ($status === 'tersedia' ? 'bg-blue-600 text-white hover:bg-blue-700' : '') }}">
                                {{ $seat }}
                            </div>

                        </label>
                        @endif
                        @endforeach
                    </div>
                </div>
                @php
                $hargaRute = 0;
                if ($sudahPilih && request('rute')) {
                $selectedRute = collect($rutes)->firstWhere('id_rute', request('rute'));
                $hargaRute = $selectedRute->harga ?? 0;
                }
                @endphp
                <div class="mt-4 text-center">
                    <span class="font-semibold text-lg">Total Bayar: Rp</span>
                    <span id="total-bayar">0</span>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-sm transition"
                    {{ !$sudahPilih ? 'disabled class=cursor-not-allowed opacity-50' : '' }}>
                    Simpan Pemesanan
                </button>
            </form>
        </div>
    </div>
</div>
<script>
    const hargaRute = {{$hargaRute}};
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="kursi[]"]');
        const totalBayarEl = document.getElementById('total-bayar');
        const namaContainer = document.getElementById('nama-penumpang-container');

        function updateTotalBayar() {
            let total = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) total += hargaRute;
            });
            totalBayarEl.textContent = total.toLocaleString('id-ID');
        }

        function updateNamaInputs() {
            namaContainer.innerHTML =
                '<label class="block text-sm font-medium text-gray-700 mb-1">Nama Penumpang</label>';
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const seatNo = checkbox.value;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = `nama[${seatNo}]`;
                    input.placeholder = `Nama untuk kursi ${seatNo}`;
                    input.className = 'w-full border rounded-md px-4 py-2 mb-2';
                    namaContainer.appendChild(input);
                }
            });
        }

        checkboxes.forEach(function(checkbox) {
            const box = checkbox.closest('label').querySelector('.seat-box');

            // Set warna saat halaman pertama kali load
            if (checkbox.checked) {
                box.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                box.classList.add('bg-red-600');
            }

            // Update warna, total, dan input nama saat checkbox berubah
            checkbox.addEventListener('change', function() {
                if (checkbox.checked) {
                    box.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    box.classList.add('bg-red-600');
                } else {
                    box.classList.remove('bg-red-600');
                    box.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }

                updateTotalBayar();
                updateNamaInputs();
            });
        });

        // Hitung awal jika ada yang sudah tercentang
        updateTotalBayar();
        updateNamaInputs();
    });
</script>



@endsection
