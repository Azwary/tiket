@extends('layouts.admin')
@section('title', 'Edit Pemesanan')

@section('content')
    @php
        $seats = $seats ?? [1, 2, null, 'driver', null, 3, 4, 5, 6, null, 7, 8, 9, null, 10, 11, 12, 13, 14, 15];
        $kursi_terisi = $kursi_terisi ?? [];
        $rutes = $rutes ?? [];
        $jadwals = $jadwals ?? [];
        $selected = old('kursi', $pemesanan->detail_pemesanan->pluck('kursi.no_kursi')->toArray() ?? []);
        $id_rute = old('id_rute', $pemesanan->jadwal->rute->id_rute ?? null);
        $tanggal = old('tanggal', $pemesanan->tanggal_keberangkatan);
        $id_jadwal = old('id_jadwal', $pemesanan->id_jadwal);
        $sudahPilih = $id_rute && $tanggal && $id_jadwal;
        $kursi_map = [];

        if ($sudahPilih) {
            foreach ($kursi_terisi as $no_kursi) {
                $kursi_map[(string) $no_kursi] = 'terisi';
            }
        }
    @endphp

    <div class="py-8 px-6">
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl mx-auto">
            <h2 class="text-2xl font-semibold mb-6">Edit Pemesanan</h2>
            <form method="POST" action="{{ route('pemesanan.update', $pemesanan->id_pemesanan) }}">
                @csrf
                @method('PUT')

                {{-- Nama Penumpang --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penumpang</label>
                    <input type="text" name="nama" value="{{ old('nama', $pemesanan->penumpang->nama_penumpang) }}"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm" required />
                </div>

                <!-- Pilih Rute -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Rute</label>
                    <select name="id_rute" class="w-full border rounded-md px-4 py-2 text-sm" id="rute-select">
                        <option value="">-- Pilih Rute --</option>
                        @foreach ($rutes as $rute)
                            <option value="{{ $rute->id_rute }}" {{ $id_rute == $rute->id_rute ? 'selected' : '' }}>
                                {{ $rute->asal }}-{{ $rute->tujuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal Keberangkatan --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Keberangkatan</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                        class="w-full border rounded-md px-4 py-2 text-sm" required>
                </div>

                <!-- Jam Keberangkatan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Keberangkatan</label>
                    <select name="id_jadwal" class="w-full border rounded-md px-4 py-2 text-sm" id="jadwal-select">
                        <option value="">-- Pilih Jam --</option>
                        @foreach ($jadwals as $jadwal)
                            <option value="{{ $jadwal->id_jadwal }}"
                                {{ old('id_jadwal', $pemesanan->id_jadwal ?? '') == $jadwal->id_jadwal ? 'selected' : '' }}>
                                {{ $jadwal->jam_keberangkatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Kursi --}}
                <div class="flex justify-center mb-6">
                    <div class="grid grid-cols-4 gap-4">
                        @foreach ($seats as $seat)
                            @if ($seat === null)
                                <div></div>
                            @elseif ($seat === 'driver')
                                <div class="flex items-center justify-center text-2xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icon-tabler-steering-wheel">
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M12 14l0 7" />
                                        <path d="M10 12l-6.75 -2" />
                                        <path d="M14 12l6.75 -2" />
                                    </svg>
                                </div>
                            @else
                                @php
                                    $status = $sudahPilih ? $kursi_map[(string) $seat] ?? 'tersedia' : 'belum';
                                    $isSelected = in_array($seat, $selected);
                                    $disabled = $status === 'terisi' && !$isSelected;

                                    $classes =
                                        'seat-box text-sm font-semibold text-center px-3 py-2 rounded-md transition ';
                                    $classes .= $disabled
                                        ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                        : ($isSelected
                                            ? 'bg-red-600 text-white'
                                            : 'bg-blue-600 text-white hover:bg-blue-700');
                                @endphp

                                <label class="cursor-pointer">
                                    <input type="checkbox" name="kursi[]" value="{{ $seat }}" class="sr-only"
                                        {{ $isSelected ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                    <div class="{{ $classes }}" data-disabled="{{ $disabled ? '1' : '0' }}">
                                        {{ $seat }}
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">
                    Perbarui Pemesanan
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateSeatColor(checkbox) {
                const box = checkbox.closest('label').querySelector('.seat-box');
                const isChecked = checkbox.checked;
                const isDisabled = box.dataset.disabled === '1';

                box.classList.remove(
                    'bg-blue-600', 'hover:bg-blue-700',
                    'bg-red-600', 'text-white',
                    'bg-gray-300', 'text-gray-500', 'cursor-not-allowed'
                );

                if (isDisabled) {
                    box.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                } else if (isChecked) {
                    box.classList.add('bg-red-600', 'text-white');
                } else {
                    box.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                }
            }

            document.querySelectorAll('input[type="checkbox"][name="kursi[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSeatColor(checkbox);
                });
                updateSeatColor(checkbox);
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#rute-select').on('change', function() {
            var idRute = $(this).val();
            $('#jadwal-select').html('<option value="">Memuat...</option>');

            if (idRute) {
                $.ajax({
                    url: '/get-jadwal-by-rute/' + idRute,
                    type: 'GET',
                    success: function(data) {
                        $('#jadwal-select').empty().append('<option value="">-- Pilih Jam --</option>');
                        data.forEach(function(jadwal) {
                            $('#jadwal-select').append(
                                `<option value="${jadwal.id_jadwal}">${jadwal.jam_keberangkatan}</option>`
                            );
                        });
                    },
                    error: function() {
                        alert('Gagal memuat jadwal!');
                    }
                });
            } else {
                $('#jadwal-select').html('<option value="">-- Pilih Jam --</option>');
            }
        });
    </script>

@endsection
