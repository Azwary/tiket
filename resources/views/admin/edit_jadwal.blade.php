@extends('layouts.admin')
@section('title', 'Edit Jadwal')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full md:w-2/3 lg:w-1/2 mx-auto border border-gray-200">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Edit Jadwal
            </h1>

            <form action="{{ route('supir.jadwal.update', $jadwal->id_jadwal) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Rute (readonly) --}}
                <div>
                    <label for="id_rute" class="block mb-1 text-sm font-medium text-gray-700">Rute</label>
                    <select name="id_rute" id="id_rute" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                        @foreach ($rutes as $rute)
                            <option value="{{ $rute->id_rute }}" {{ $jadwal->id_rute == $rute->id_rute ? 'selected' : '' }}>
                                {{ $rute->asal }} - {{ $rute->tujuan }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_rute" value="{{ $jadwal->id_rute }}">
                </div>

                {{-- Supir (otomatis update, readonly) --}}
                <div>
                    <label for="id_supir" class="block mb-1 text-sm font-medium text-gray-700">Supir</label>
                    <select name="id_supir" id="id_supir" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                        @foreach ($supirs as $supir)
                            <option value="{{ $supir->id_supir }}"
                                {{ $jadwal->id_supir == $supir->id_supir ? 'selected' : '' }}>
                                {{ $supir->nama_supir }}
                            </option>
                        @endforeach
                    </select>
                    <!-- hidden input supaya data supir tetap terkirim -->
                    <input type="hidden" name="id_supir" id="hidden_supir" value="{{ $jadwal->id_supir }}">
                </div>


                {{-- Kendaraan (editable) --}}
                <div>
                    <label for="id_kendaraan" class="block mb-1 text-sm font-medium text-gray-700">Kendaraan</label>
                    <select name="id_kendaraan" id="id_kendaraan"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @foreach ($kendaraans as $kendaraan)
                            <option value="{{ $kendaraan->id_kendaraan }}" data-supir="{{ $kendaraan->id_supir }}"
                                {{ $jadwal->id_kendaraan == $kendaraan->id_kendaraan ? 'selected' : '' }}>
                                {{ $kendaraan->plat_nomor }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kendaraan')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jam keberangkatan (readonly) --}}
                <div>
                    <label for="jam_keberangkatan" class="block mb-1 text-sm font-medium text-gray-700">Jam
                        Keberangkatan</label>
                    <input type="time" name="jam_keberangkatan" id="jam_keberangkatan"
                        value="{{ \Carbon\Carbon::parse($jadwal->jam_keberangkatan)->format('H:i') }}" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                    <input type="hidden" name="jam_keberangkatan" value="{{ $jadwal->jam_keberangkatan }}">
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('supir.jadwal') }}"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-400 text-gray-700 hover:bg-gray-100 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-5 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk update supir otomatis saat ganti kendaraan --}}
    <script>
        const kendaraanSelect = document.getElementById('id_kendaraan');
        const supirSelect = document.getElementById('id_supir');
        const hiddenSupir = document.getElementById('hidden_supir');

        // mapping kendaraan -> supir
        const kendaraanToSupir = {};
        @foreach ($kendaraans as $kendaraan)
            kendaraanToSupir["{{ $kendaraan->id_kendaraan }}"] = {
                id: "{{ $kendaraan->id_supir }}",
                nama: "{{ $kendaraan->supir->nama_supir ?? '' }}"
            };
        @endforeach

        kendaraanSelect.addEventListener('change', function() {
            const selectedKendaraan = kendaraanSelect.value;
            const supirData = kendaraanToSupir[selectedKendaraan];

            if (supirData) {
                // update hidden input supaya value dikirim saat submit
                hiddenSupir.value = supirData.id;

                // update dropdown supir agar terlihat berubah
                supirSelect.innerHTML = `<option value="${supirData.id}" selected>${supirData.nama}</option>`;
            }
        });
    </script>
@endsection
