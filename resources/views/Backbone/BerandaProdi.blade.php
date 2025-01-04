<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Prodi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/BerandaProdi.css') }}">
</head>
<body>
@include('backbone.HeaderProdi', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
    ])
    <div class="slider-container">
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $slidesData = [
                        [
                            "title" => "Program Studi Teknik Produksi Dan Proses Manufaktur",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "buttonColor" => "Blue",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundP4.png'),
                            "mascot" => asset('assets/backBeranda/MaskotP4.png'),
                        ],
                        [
                            "title" => "Program Studi Teknik Produksi Dan Proses Manufaktur",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundTPM.png'),
                            "mascot" => asset('assets/BackBeranda/maskotTPM.png'),
                        ],
                        [
                            "title" => "Manajemen Informatika",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundMI.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotMI.png'),
                        ],
                        [
                            "title" => "Mesin Otomotif",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundMO.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotMO.png'),
                        ],
                        [
                            "title" => "Mekatronika",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundMK.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotMK.png'),
                        ],
                        [
                            "title" => "Teknologi Konstruksi Bangunan Gedung",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundTKBG.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotTKBG.png'),
                        ],
                        [
                            "title" => "Teknologi Rekayasa Pemeliharaan Alat Berat",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundTRPAB.png'),
                            "mascot" => asset('assets/BackBeranda/maskotTRPAB.png'),
                        ],
                        [
                            "title" => "Teknologi Rekayasa Logistik",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundTRL.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotTRL.png'),
                        ],
                        [
                            "title" => "Teknologi Rekayasa Perangkat Lunak",
                            "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                            "buttonText" => "Knowledge Database",
                            "backgroundImage" => asset('assets/BackBeranda/BackgroundTRPL.png'),
                            "mascot" => asset('assets/BackBeranda/MaskotTRPL.png'),
                        ]
                    ];
                @endphp

                @foreach ($slidesData as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="content-wrapper" style="background-image: url('{{ $slide['backgroundImage'] }}')">
                            <div class="text-content">
                                <h1>{{ $slide['title'] }}</h1>
                                <p>{{ $slide['subtitle'] }}</p>
                                <button class="action-button">{{ $slide['buttonText'] }}</button>
                            </div>
                            <div class="mascot">
                                <img src="{{ $slide['mascot'] }}" alt="Mascot">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    @include('backbone.footer') 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
