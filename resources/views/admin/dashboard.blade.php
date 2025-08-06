@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="py-6 px-6">
    {{-- Kartu Statistik --}}
    <div class="flex gap-4 flex-wrap">
        <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-sm">
            <h5 class="text-lg font-semibold text-gray-700 mb-2">Total Tiket Terjual</h5>
            <p class="text-2xl font-bold text-blue-600">{{ $totalTiketTerjual }}</p>
        </div>
        <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-sm">
            <h5 class="text-lg font-semibold text-gray-700 mb-2">Total Penjualan</h5>
            <p class="text-2xl font-bold text-green-600">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
            {{-- <p class="text-2xl font-bold text-green-600">{{ $totalTransaksi }}</p> --}}
        </div>
        <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-sm">
            <h5 class="text-lg font-semibold text-gray-700 mb-2">Menunggu Konfirmasi</h5>
            <p class="text-2xl font-bold text-yellow-600">{{ $menungguKonfirmasi }}</p>
        </div>
    </div>

    {{-- Diagram Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div class="bg-white p-6 rounded-2xl shadow-md">
            <h5 class="text-lg font-semibold text-gray-700 mb-4">Tiket Terjual per Rute</h5>
            <div class="relative h-80">
                <canvas id="barChart" class="w-full h-full"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-md">
            <h5 class="text-lg font-semibold text-gray-700 mb-4">Status Transaksi</h5>
            <div class="flex justify-center">
                <canvas id="doughnutChart" class="max-w-[300px] max-h-[300px]"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Chart Scripts --}}
<script>
    const barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelsRute) !!},
            datasets: [{
                label: 'Jumlah Tiket Terjual',
                data: {!! json_encode($jumlahTiketRute) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const doughnutChart = new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Berhasil', 'Menunggu', 'Ditolak'],
            datasets: [{
                label: 'Status Transaksi',
                data: {!! json_encode([$jumlahBerhasil, $jumlahMenunggu, $jumlahDitolak]) !!},
                backgroundColor: [
                    'rgba(34,197,94,0.8)',   // Hijau
                    'rgba(234,179,8,0.8)',   // Kuning
                    'rgba(239,68,68,0.8)'    // Merah
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%', // bikin donat agak tipis
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 20,
                        padding: 15
                    }
                }
            }
        }
    });
</script>
@endsection
