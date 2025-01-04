<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/BerandaPengguna.css') }}">
</head>

<body>
    @include('backbone.HeaderMahasiswa', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
    ])
    <section class="background-beranda" style="background-image: url('{{ asset('assets/backgroundPengguna.png') }}')">
        <div class="ucapan">
            <h3>Selamat Datang</h3>
            <h1>Civitas Akademika ASTRAtech!</h1>
            <p>
                “Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih
                efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang
                tersedia dengan mengakses menu yang disediakan.”
            </p>
            <button>Knowledge Database</button>
        </div>

        <div class="imgDatang-maskot" style="height: 100%; paddingTop: 20vh; width: 27%; marginLeft : 0px;">
            <img class="maskot" src="{{ asset('assets/maskotUser.png') }}" alt="Ilustrasi Maskot User" style="height: {{ $maskotHeight ?? '100%' }};">
        </div>
    </section>

    @include('backbone.footer')
</body>

</html>