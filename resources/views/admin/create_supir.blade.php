@extends('layouts.admin')
@section('title', 'Tambah Supir')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 max-w-xl mx-auto space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Tambah Supir</h1>
                <p class="text-gray-500 mt-1 text-sm">Silakan lengkapi data supir berikut dengan benar.</p>
            </div>

            <form action="{{ route('supir.store', ['tipe' => 'supir']) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="nama_supir" class="block text-sm font-medium text-gray-700 mb-1">Nama Supir</label>
                    <input type="text" name="nama_supir" id="nama_supir"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('nama_supir') }}">
                    @error('nama_supir')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                    <input type="text" name="no_hp" id="no_hp"
                        class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('no_hp') }}">
                    @error('no_hp')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
