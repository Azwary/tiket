@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="p-6 space-y-6">
        <div class="bg-white shadow-md rounded-2xl p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold mb-4">Manajemen Pengguna</h1>
                <div class="relative">
                    <button id="dropdownButton" type="button"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-20">
                        <a href="{{ route('users.create') }}?tipe=petugas"
                            class="block px-4 py-2 hover:bg-gray-100 text-gray-700">+ Petugas</a>
                        <a href="{{ route('users.create') }}?tipe=admin"
                            class="block px-4 py-2 hover:bg-gray-100 text-gray-700">+ Admin</a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabel Admin --}}
            <div class="mt-2">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Admin</h2>
                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Username</th>
                                <th class="px-4 py-2 border">Wilayah</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $index => $admin)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $admin->nama_admin }}</td>
                                    <td class="px-4 py-2 border">{{ $admin->username }}</td>
                                    <td class="px-4 py-2 border capitalize">{{ $admin->role }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('users.edit', $admin->id_admin) }}?tipe=admin"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            <form action="{{ route('users.destroy', $admin->id_admin) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tipe" value="admin">
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data admin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Petugas --}}
            <div class="mt-2">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Petugas</h2>
                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Username</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($petugas as $index => $p)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $p->nama_petugas }}</td>
                                    <td class="px-4 py-2 border">{{ $p->username }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('users.edit', $p->id_petugas) }}?tipe=petugas"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            <form action="{{ route('users.destroy', $p->id_petugas) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus petugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tipe" value="petugas">
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data petugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Tabel Penumpang --}}
            <div class="mt-2">
                <h2 class="text-xl font-semibold mb-3 text-gray-700">Data Penumpang</h2>
                <div class="overflow-x-auto rounded-xl shadow">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Username</th>
                                <th class="px-4 py-2 border">No telfon</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penumpang as $index => $penumpangs)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $penumpangs->nama_penumpang }}</td>
                                    <td class="px-4 py-2 border">{{ $penumpangs->username }}</td>
                                    <td class="px-4 py-2 border">{{ $penumpangs->no_telepon }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <!-- <a href="{{ route('users.edit', $p->id_petugas) }}?tipe=petugas"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </a> -->
                                            <form action="{{ route('users.destroy', $penumpangs->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus penumpang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tipe" value="penumpang">
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    Hapus
                                                </button>
                                            </form> 
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data petugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');

            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
