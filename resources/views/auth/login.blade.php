<x-guest-layout>

    <div class="w-full max-w-md mx-auto my-10 rounded-2xl  px-8 py-10 ">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <img src="{{ asset('logo/logo.png') }}" alt="Logo" class="w-3/5 h-auto drop-shadow-md">
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="mb-4 p-3  bg-red-100 border border-red-400 text-black rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form method="POST" action="{{ route('login.admin') }}">
            @csrf

            <!-- Username -->
            <div class="mt-4">
                <x-input-label for="username" :value="__('Username')" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="username" name="username" type="text"
                        class="pl-10 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Username" value="{{ old('username') }}" required autofocus>
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative">
                    <!-- Ikon gembok kiri -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <i class="fas fa-lock"></i>
                    </span>

                    <!-- Input password -->
                    <input id="password" name="password" type="password"
                        class="pl-10 pr-10 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Password" required autocomplete="current-password">

                    <!-- Ikon mata kanan -->
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                        onclick="togglePasswordVisibility()">
                        <i id="togglePasswordIcon" class="fas fa-eye"></i>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>


            <!-- Tombol Login -->
            <div class="mt-6">
                <x-primary-button
                    class="w-full justify-center py-2 text-sm font-semibold tracking-wide rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Login') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
