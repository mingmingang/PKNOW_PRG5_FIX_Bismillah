<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Pengajuan Kelompok Keahlian</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@include('backbone.headertenagapendidik', [
    'showMenu' => false,
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false,
])
<div style="display: flex; justify-content: space-between; margin-top: 100px; margin-left: 70px; margin-right: 70px;">
    <div class="back-and-title" style="display: flex;">
        <button style="background-color: transparent; border: none;" onclick="BackPage()">
            <img src="{{ asset('assets/backPage.png') }}" alt="" />
        </button>
        <h4 style="color: rgb(10, 94, 168); font-weight: bold; font-size: 30px; margin-top: 10px; margin-left: 20px;">
            Detail Kelompok Keahlian
        </h4>
    </div>
    
</div>
<div class="card" style="margin: 80px; 80px; margin-top: 30px;">
    <div class="row">
        <div class="col-12">
        
            <form>
                @csrf
                <div class="card-body p-4">
                    <div class="row pt-2">
                        <div class="col-lg-7 px-4">
                            <h3 class="mb-3 fw-semibold">{{ $data->kke_nama }}</h3>
                            <h6 class="fw-semibold">
                                <span class="bg-primary me-2" style="padding: 2px;"></span>
                                {{ $data->pro_nama }}
                            </h6>
                            <div class="pt-2 ps-2">
                                <i class="fa fa-user text-dark"></i>
                                <span>PIC:{{ $data->pic_nama }}</span>
                            </div>
                            <hr class="mb-0" style="opacity: 0.2;">
                            <p class="py-3" style="text-align: justify;">{{ $data->kke_deskripsi }}</p>
                        </div>

                        <div class="col-lg-5">
                            @if ($anggotaAktif->isEmpty())
                                <p>Tidak ada anggota aktif.</p>
                            @else
                                <ul class="list-group">
                                    @foreach ($anggotaAktif as $anggota)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $anggota->nama }}</span>
                                           
                                            
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div>
                            <!-- Daftar Program yang Aktif -->
                            <div class="col-12 mt-4">
                                <h5 class="text-primary">Daftar Program dalam Kelompok Keahlian {{ $data->kke_nama }}</h5>
                                @if (empty($listProgramWithKategori))
                                    <p>Tidak ada program yang aktif.</p>
                                @else
                                    @foreach ($listProgramWithKategori as $index => $item)
                                        <div class="card card-program mt-3 border-secondary">
                                            <div
                                                class="card-body d-flex justify-content-between align-items-center border-bottom border-secondary">
                                                <p class="fw-medium mb-0" style="width: 20%;">
                                                    {{ $index + 1 }}. {{ $item['program']->{'Nama Program'} }}
                                                </p>
                                                <p class="mb-0 pe-3" style="width: 80%;">
                                                    {{ $item['program']->Deskripsi }}
                                                </p>
                                            </div>
                                            <div class="p-3 pt-0">
                                                <p class="text-primary fw-semibold mb-2">
                                                    Daftar Kategori Program
                                                </p>
                                                @if (empty($item['kategori']))
                                                    <p>Tidak ada kategori yang aktif.</p>
                                                @else
                                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
                                                        @foreach ($item['kategori'] as $katIndex => $kategori)
                                                            <div class="col mb-3">
                                                                <div class="card card-kategori-program">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title">
                                                                            {{ $index + 1 }}-{{ $katIndex + 1 }}.
                                                                            {{ $kategori->{'Nama Kategori'} }}
                                                                        </h6>
                                                                        <p class="card-text"
                                                                            style="text-align: justify;">
                                                                           
                                                                            {{ $kategori->Deskripsi }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tombol Kembali -->

            </form>
        </div>
    </div>
</div>
<script>
    // Log data program dengan kategori ke konsol browser
    console.log(@json($listProgramWithKategori));
</script>

<script>
    function BackPage() {
        window.location.href = "{{ route('pengajuan_KK') }}";
    }
</script>
@include('backbone.footer')
