<x-guest-layout>
    <div class="w-full max-w-md mx-auto my-10 p-8">
        <div class="text-center mb-6">
            <img src="{{ asset('logo/logo.png') }}" alt="Logo" class="w-24 h-auto mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Reset Password Admin</h2>
            <p class="text-gray-500 mt-1 text-sm">Masukkan username dan tanggal lahir untuk verifikasi</p>
        </div>

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.request.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                        placeholder="Masukkan username">
                </div>
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" name="tanggal_lahir"
                        class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm">
                </div>
                @error('tanggal_lahir')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg shadow-md hover:from-purple-600 hover:to-indigo-500 transition-all">
                Verifikasi
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login.admin') }}" class="text-sm text-indigo-600 hover:underline">
                Kembali ke Login
            </a>
        </div>
    </div>
</x-guest-layout>
