@extends('layouts.admin')
@section('title', 'Tambah Kendaraan')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 max-w-xl mx-auto space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Tambah Kendaraan</h1>
                <p class="text-gray-500 mt-1 text-sm">Silakan lengkapi data kendaraan berikut dengan benar.</p>
            </div>

            <form action="{{ route('supir.store', ['tipe' => 'kendaraan']) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="plat_nomor"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('plat_nomor') }}">
                    @error('plat_nomor')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_supir" class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="id_supir" id="id_supir"
                        class="w-full px-4 py-2 border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Supir --</option>
                        @foreach ($supirs as $supir)
                            <option value="{{ $supir->id_supir }}"
                                {{ old('id_supir') == $supir->id_supir ? 'selected' : '' }}>
                                {{ $supir->nama_supir }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_supir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Status --</option>
                        <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="tidak tersedia" {{ old('status') == 'tidak tersedia' ? 'selected' : '' }}>Tidak
                            Tersedia</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('supir.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
