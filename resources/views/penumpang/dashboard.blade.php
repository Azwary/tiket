<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white shadow rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-700">Total Tiket Terjual</h3>
                            <p class="mt-2 text-2xl font-bold text-indigo-600">123</p>
                        </div>
                        <div class="bg-white shadow rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-700">Total Transaksi</h3>
                            <p class="mt-2 text-2xl font-bold text-green-600">Rp. 10.000.000</p>
                        </div>
                        <div class="bg-white shadow rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-gray-700">Menunggu Konfirmasi</h3>
                            <p class="mt-2 text-2xl font-bold text-red-600">7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
