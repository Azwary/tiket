@extends('layouts.admin')
@section('title', 'Edit Data')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full md:w-2/3 lg:w-1/2 mx-auto border border-gray-200">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 17a4 4 0 100-8 4 4 0 000 8zm-7 4a7 7 0 1114 0H4z" />
                </svg>
                Edit {{ $tipe === 'supir' ? 'Supir' : 'Kendaraan' }}
            </h1>

            <form
                action="{{ route('supir.update', $tipe === 'supir' ? $data->id_supir : $data->id_kendaraan) }}?tipe={{ $tipe }}"
                method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                @if ($tipe === 'supir')
                    {{-- Form Supir --}}
                    <div>
                        <label for="nama_supir" class="block mb-1 text-sm font-medium text-gray-700">Nama Supir</label>
                        <input type="text" name="nama_supir" id="nama_supir"
                            value="{{ old('nama_supir', $data->nama_supir) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @error('nama_supir')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_hp" class="block mb-1 text-sm font-medium text-gray-700">No HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $data->no_hp) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @error('no_hp')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                        @php $cur = old('status', $data->status); @endphp
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            @if ($cur && !in_array($cur, ['aktif', 'nonaktif']))
                                <option value="{{ $cur }}" selected>{{ $cur }} (tersimpan)</option>
                            @endif
                            <option value="aktif" {{ $cur === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $cur === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- Form Kendaraan --}}
                    <div>
                        <label for="plat_nomor" class="block mb-1 text-sm font-medium text-gray-700">Plat Nomor</label>
                        <input type="text" name="plat_nomor" id="plat_nomor"
                            value="{{ old('plat_nomor', $data->plat_nomor) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        @error('plat_nomor')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_supir" class="block mb-1 text-sm font-medium text-gray-700">Supir</label>
                        <select name="id_supir" id="id_supir"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">-- Pilih Supir --</option>
                            @foreach ($supirs as $supir)
                                <option value="{{ $supir->id_supir }}"
                                    {{ (string) old('id_supir', $data->id_supir) === (string) $supir->id_supir ? 'selected' : '' }}>
                                    {{ $supir->nama_supir }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_supir')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                        @php $cur = old('status', $data->status); @endphp
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            @if ($cur && !in_array($cur, ['Tersedia', 'Tidak Tersedia']))
                                <option value="{{ $cur }}" selected>{{ $cur }} (tersimpan)</option>
                            @endif
                            <option value="Tersedia" {{ $cur === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Tidak Tersedia" {{ $cur === 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia
                            </option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('supir.index') }}"
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
@endsection
