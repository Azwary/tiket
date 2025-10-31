@extends('layouts.admin')
@section('title', 'Tambah Pengguna')

@section('content')
    <div class="p-6">
        <div class="bg-white shadow-xl rounded-2xl p-8 max-w-xl mx-auto space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">
                    Tambah {{ request('tipe') == 'admin' ? 'Admin' : 'Petugas' }}
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Silakan lengkapi data pengguna berikut dengan benar.
                </p>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                @csrf

                @if (request('tipe') == 'admin')
                    {{-- Admin --}}
                    <div>
                        <label for="nama_admin" class="block text-sm font-medium text-gray-700 mb-1">Nama Admin</label>
                        <input type="text" name="nama_admin" id="nama_admin"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('nama_admin') }}">
                        @error('nama_admin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="username"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('username') }}">
                        @error('username')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role"
                            class="w-full px-4 py-2 border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Role --</option>
                            <option value="padang" {{ old('role') == 'padang' ? 'selected' : '' }}>Padang</option>
                            <option value="solok" {{ old('role') == 'solok' ? 'selected' : '' }}>Solok</option>
                            <option value="sawah_lunto" {{ old('role') == 'sawah_lunto' ? 'selected' : '' }}>Sawah Lunto
                            </option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- Petugas --}}
                    <div>
                        <label for="nama_petugas" class="block text-sm font-medium text-gray-700 mb-1">Nama Petugas</label>
                        <input type="text" name="nama_petugas" id="nama_petugas"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('nama_petugas') }}">
                        @error('nama_petugas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="username"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('username') }}">
                        @error('username')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"
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
