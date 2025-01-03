@include('backbone.headerpicpknow', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
    ])
    <head>
<link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
<title>Beranda Utama - P-KNOW System</title>
</head>

<section class="sec1">
    <div class="ucapan">
        <h3>Selamat Datang di</h3>
        <h1>System Knowledge Management System</h1>
        <p>
            “Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih
            efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang
            tersedia dengan mengakses menu yang disediakan.”
        </p>
        <button>Knowledge Database</button>
    </div>

    <div class="imgDatang">
        <img class="ceweVR" src="{{ asset('assets/ceweVR_beranda.png') }}" alt="Ilustrasi Cewek VR" />
        <img class="cowoTop" src="{{ asset('assets/cowoTop_beranda.png') }}" alt="Ilustrasi Cowok" />
    </div>
</section>
@include('backbone.footer')

<script>
        // Kirim data roles dari Laravel ke JavaScript
    
    </script>