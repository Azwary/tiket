<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $user = Auth::guard('admin')->user();
    $role = $user->role ?? null;

    function getRuteByRole($role)
    {
        return match ($role) {
            'padang' => [1, 2],
            'solok' => [4],
            'sawah_lunto' => [3],
            'umum' => null,
            default => [],
        };
    }

    $ruteDiizinkan = getRuteByRole($role);

    $notifQuery = \App\Models\Pembayaran::with('pemesanan.penumpang', 'pemesanan.jadwal.rute')->where(
        'status_konfirmasi',
        'menunggu',
    );

    if ($ruteDiizinkan !== null) {
        $notifQuery->whereHas('pemesanan.jadwal', function ($q) use ($ruteDiizinkan) {
            $q->whereIn('id_rute', $ruteDiizinkan);
        });
    }

    $notifs = $notifQuery->latest()->take(5)->get();
    $notifCount = $notifs->count();
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-800">
    <div class="min-h-screen flex">
        <!-- Sidebar Desktop -->
        <aside class="bg-white text-black w-64 p-4 min-h-screen hidden md:block">
            <div class="flex justify-center">
                <img src="{{ asset('logo/logo.png') }}" alt="Logo" class="w-3/5 h-auto">
            </div>

            <ul class="space-y-2 mt-8">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded w-full
                             {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('pemesanan.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded w-full
                                 {{ request()->routeIs('pemesanan.*') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                        <i data-lucide="ticket" class="w-5 h-5"></i>
                        Data penjualan tiket
                    </a>
                </li>

                <li>
                    <a href="{{ route('pembayaran.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded w-full
                                {{ request()->routeIs('pembayaran.*') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                        <i data-lucide="badge-check" class="w-5 h-5"></i>
                        Transaksi Online
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded w-full
                            {{ request()->routeIs('laporan.*') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5"></i>
                        Laporan
                    </a>
                </li>
                @if (Auth::guard('admin')->user()->role == 'umum')
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded w-full
                            {{ request()->routeIs('Users.*') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            Users
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('supir.index') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded w-full
                            {{ request()->routeIs('Users.*') ? 'bg-gray-200 font-semibold text-indigo-600' : 'hover:bg-gray-200' }}">
                            <i data-lucide="car-taxi-front" class="w-5 h-5"></i>
                            Supirs
                        </a>
                    </li>
                @endif
            </ul>
        </aside>


        <!-- Sidebar Mobile -->
        <div id="mobileSidebar"
            class="fixed top-0 left-0 h-full w-64 bg-gray-800 text-white z-50 p-4 transform -translate-x-full transition-transform duration-300 md:hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Menu</h2>
                <button onclick="toggleMobileSidebar()" class="text-white text-2xl">&times;</button>
            </div>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('pemesanan.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full">
                        <i data-lucide="ticket" class="w-5 h-5"></i>
                        Rekap pemesanan tiket/ data penjualan tiket
                    </a>
                </li>
                <li>
                    <a href="{{ route('pembayaran.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full">
                        <i data-lucide="badge-check" class="w-5 h-5"></i>
                        Konfirmasi pembayarn online/ Transaksi Online
                    </a>
                </li>
                <li>
                    <a href="{{ route('laporan.index') }}"
                        class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5"></i>
                        Laporan
                    </a>
                </li>
                @if (Auth::guard('admin')->user()->role == 'umum')
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded hover:bg-gray-700 w-full">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            Users
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Overlay for mobile -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"
            onclick="toggleMobileSidebar()"></div>

        <!-- Main Panel -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navbar -->
            <header class="bg-white shadow w-full">
                <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                    <!-- Hamburger (Mobile) -->
                    <button onclick="toggleMobileSidebar()" class="block md:hidden text-2xl text-gray-700">
                        &#9776;
                    </button>

                    <!-- Judul Halaman -->
                    <div class="text-xl font-semibold text-gray-800">
                        <span class="hidden md:block">@yield('title')</span>
                    </div>

                    @php
                        $notifCount = $notifs->count();
                    @endphp


                    <!-- Area Kanan: Notifikasi & Akun -->
                    <div class="flex items-center space-x-8">
                        <!-- Notifikasi -->
                        <div class="relative">
                            <button onclick="toggleNotifDropdown()" class="relative focus:outline-none group">
                                <!-- Icon Lonceng -->
                                <svg class="w-6 h-6 text-gray-700 group-hover:text-indigo-600 transition" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                                            C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if ($notifCount > 0)
                                    <span
                                        class="absolute top-0 right-0 inline-block w-4 h-4 text-[10px] text-white bg-red-600
                                            rounded-full text-center leading-4 font-bold animate-pulse">
                                        {{ $notifCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Dropdown Notifikasi -->
                            <div id="notifDropdown"
                                class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="px-4 py-2 text-sm font-semibold text-gray-700 border-b">
                                    Notifikasi
                                </div>
                                @if ($notifCount > 0)
                                    @foreach ($notifs as $notif)
                                        @php
                                            $pemesanan = $notif->pemesanan;
                                            $penumpang = $pemesanan->penumpang->nama_penumpang ?? '-';
                                            $rute_a = $pemesanan->jadwal->rute->asal ?? '-';
                                            $rute_b = $pemesanan->jadwal->rute->tujuan ?? '-';
                                            $tanggal = Carbon::parse($pemesanan->tanggal)->format('d M Y');
                                            $waktu = $pemesanan->jadwal->jam_keberangkatan ?? '-';
                                        @endphp
                                        <a href="{{ route('pembayaran.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            {{ $penumpang }} – ( {{ $rute_a }} – {{ $rute_b }} ) -
                                            {{ $tanggal }} – {{ $waktu }}
                                        </a>
                                    @endforeach
                                @else
                                    <div class="px-4 py-2 text-sm text-gray-500 italic">
                                        Tidak ada notifikasi.
                                    </div>
                                @endif
                            </div>
                        </div>



                        <!-- Dropdown Akun -->
                        <div class="relative">
                            <button onclick="toggleDropdown()"
                                class="flex items-center space-x-2 focus:outline-none group">
                                <div class="text-sm text-gray-700 group-hover:text-indigo-600 transition">
                                    {{ Auth::guard('admin')->user()->role }}
                                </div>
                                <svg class="w-4 h-4 text-gray-700 group-hover:text-indigo-600 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Akun -->
                            <div id="userDropdown"
                                class="hidden absolute right-0 mt-2 w-28 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <form method="POST" action="{{ route('logout.admin') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </header>


            <!-- Main Content -->
            <main class="p-6 flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JS Interaksi Dropdown -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('button[onclick="toggleDropdown()"]');
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- AJAX Dynamic Dropdown -->
    <script>
        $(document).ready(function() {
            $('#rute').on('change', function() {
                var idRute = $(this).val();
                $('#jam').empty().append('<option selected disabled>Loading...</option>').prop('disabled',
                    true);

                if (idRute) {
                    $.ajax({
                        url: '/get-jam-keberangkatan/' + idRute,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#jam').empty();
                            if (data.length > 0) {
                                $('#jam').append(
                                    '<option selected disabled>Pilih Jam</option>');
                                $.each(data, function(index, item) {
                                    $('#jam').append('<option value="' + item
                                        .id_jadwal + '">' + item.jam_keberangkatan +
                                        '</option>');
                                });
                                $('#jam').prop('disabled', false);
                            } else {
                                $('#jam').append(
                                    '<option selected disabled>Tidak ada jadwal tersedia</option>'
                                );
                            }
                        }
                    });
                } else {
                    $('#jam').empty().append(
                        '<option selected disabled>Pilih Rute terlebih dahulu</option>').prop(
                        'disabled', true);
                }
            });
        });
    </script>

    <!-- Aktifkan Lucide Icons -->
    <script>
        lucide.createIcons();

        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        }

        function toggleNotifDropdown() {
            document.getElementById('notifDropdown').classList.toggle('hidden');
        }

        document.getElementById('mobileSidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>

</html>
