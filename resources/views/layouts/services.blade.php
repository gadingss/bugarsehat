<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Member - Gym & Yoga</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 w-64 h-screen bg-gray-900 text-white shadow-lg">
    <div class="px-6 py-4 border-b border-gray-700 flex items-center gap-3">
      <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-orange-500 text-xl font-bold">Y</div>
      <span class="text-lg font-semibold">Gym & Yoga</span>
    </div>

    <!-- Menu -->
    <nav class="mt-4">
      <ul class="space-y-1 px-4 text-sm">
        <!-- Dashboard -->
        <li>
          <a href="#" class="flex items-center gap-2 py-2 px-3 rounded bg-gray-800 hover:bg-gray-700">
            <!-- Icon -->
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h4v10H3V10zm7-6h4v16h-4V4zm7 10h4v6h-4v-6z" />
            </svg>
            Dashboard
          </a>
        </li>

        <!-- Header -->
        <div class="mt-6 px-4 text-xs font-bold text-gray-400 uppercase">Member Area</div>

        <!-- Profil -->
        <li>
          <a href="#" class="flex items-center gap-2 py-2 px-3 rounded hover:bg-gray-800">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A11.955 11.955 0 0112 15c2.167 0 4.182.613 5.879 1.661M15 12a3 3 0 10-6 0 3 3 0 006 0z" />
            </svg>
            Profil / Akun
          </a>
        </li>

        <!-- Membership -->
        <li>
          <div class="text-gray-400 font-semibold mt-4">Membership</div>
          <ul class="ml-6 mt-1 space-y-1 text-gray-300 text-sm">
            <li><a href="#" class="block py-1 hover:underline">Daftar Membership</a></li>
            <li><a href="#" class="block py-1 hover:underline">Renewal Membership</a></li>
            <li><a href="#" class="block py-1 hover:underline">Histori Membership</a></li>
          </ul>
        </li>

        <!-- Produk & Layanan -->
        <li>
          <div class="text-gray-400 font-semibold mt-4">Produk & Layanan</div>
          <ul class="ml-6 mt-1 space-y-1 text-gray-300 text-sm">
            <li><a href="#" class="block py-1 hover:underline">Katalog Produk & Layanan</a></li>
            <li><a href="#" class="block py-1 hover:underline">Paket Aktif</a></li>
            <li><a href="#" class="block py-1 hover:underline">Promo & Penawaran</a></li>
          </ul>
        </li>

        <!-- Transaksi -->
        <li>
          <div class="text-gray-400 font-semibold mt-4">Transaksi</div>
          <ul class="ml-6 mt-1 space-y-1 text-gray-300 text-sm">
            <li><a href="#" class="block py-1 hover:underline">Scan QR / Pilih Layanan</a></li>
            <li><a href="#" class="block py-1 hover:underline">Pembayaran</a></li>
            <li><a href="#" class="block py-1 hover:underline">Riwayat Pembelian</a></li>
          </ul>
        </li>

        <!-- Kunjungan -->
        <li>
          <div class="text-gray-400 font-semibold mt-4">Kunjungan</div>
          <ul class="ml-6 mt-1 space-y-1 text-gray-300 text-sm">
            <li><a href="#" class="block py-1 hover:underline">Riwayat Kunjungan</a></li>
            <li><a href="#" class="block py-1 hover:underline">Scan QR Checkin</a></li>
          </ul>
        </li>

        <!-- Laporan -->
        <li>
          <div class="text-gray-400 font-semibold mt-4">Laporan</div>
          <ul class="ml-6 mt-1 space-y-1 text-gray-300 text-sm">
            <li><a href="#" class="block py-1 hover:underline">Riwayat Transaksi</a></li>
            <li><a href="#" class="block py-1 hover:underline">Status Membership</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="ml-64 p-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Content</h1>
    <p class="text-gray-600">Selamat datang di aplikasi Gym & Yoga.</p>
  </main>

</body>
</html>
