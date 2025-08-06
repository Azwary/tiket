@extends('layouts.admin')
@section('title', 'Edit Pengguna')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full md:w-2/3 lg:w-1/2 mx-auto border border-gray-200">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 17a4 4 0 100-8 4 4 0 000 8zm-7 4a7 7 0 1114 0H4z"/>
                </svg>
                Edit {{ request('tipe') == 'admin' ? 'Admin' : 'Petugas' }}
            </h1>

            <form action="{{ route('users.update', request('tipe') == 'admin' ? $user->id_admin : $user->id_petugas) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                @if(request('tipe') == 'admin')
                    {{-- Form Admin --}}
                    <div>
                        <label for="nama_admin" class="block mb-1 text-sm font-medium text-gray-700">Nama Admin</label>
                        <input type="text" name="nama_admin" id="nama_admin"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                               value="{{ old('nama_admin', $user->nama_admin) }}">
                        @error('nama_admin')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                               value="{{ old('username', $user->username) }}">
                        @error('username')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block mb-1 text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">-- Pilih Role --</option>
                            <option value="padang" {{ old('role', $user->role) == 'padang' ? 'selected' : '' }}>Padang</option>
                            <option value="solok" {{ old('role', $user->role) == 'solok' ? 'selected' : '' }}>Solok</option>
                            <option value="sawah_lunto" {{ old('role', $user->role) == 'sawah_lunto' ? 'selected' : '' }}>Sawah Lunto</option>
                        </select>
                        @error('role')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- Form Petugas --}}
                    <div>
                        <label for="nama_petugas" class="block mb-1 text-sm font-medium text-gray-700">Nama Petugas</label>
                        <input type="text" name="nama_petugas" id="nama_petugas"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                               value="{{ old('nama_petugas', $user->nama_petugas) }}">
                        @error('nama_petugas')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block mb-1 text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                               value="{{ old('username', $user->username) }}">
                        @error('username')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password <span class="text-gray-500 text-sm">(kosongkan jika tidak diganti)</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    @error('password')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 justify-end pt-2">
                    <a href="{{ route('users.index') }}"
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
