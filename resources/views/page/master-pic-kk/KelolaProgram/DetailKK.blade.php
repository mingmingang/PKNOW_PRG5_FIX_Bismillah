<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Kelompok Keahlian - P-KNOW</title>
</head>
@include('backbone.headerPICKK', [
    'showMenu' => false,
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
])
<div class="container" style="margin-top: 50px;">
    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @else
<div class="card mb-4">
    <div class="card-body">
        <h1 class="text-primary">{{ $kelompokKeahlian->{"Nama Kelompok Keahlian"} ?? 'Nama Tidak Ditemukan' }}</h1>
        <p class="text-muted mb-1"><i class="fas fa-university"></i> {{ $kelompokKeahlian->Prodi ?? '-' }}</p>
        <p><i class="fas fa-user"></i> PIC: {{ $kelompokKeahlian->PIC ?? 'Tidak Ditemukan' }}</p>
        <p>{{ $kelompokKeahlian->Deskripsi ?? 'Deskripsi tidak tersedia.' }}</p>
    </div>
</div>


        <!-- Daftar Program -->
        <h3 class="text-primary">Daftar Program dalam Kelompok Keahlian</h3>
        @if(empty($programs))
            <div class="alert alert-warning mt-3">
                Tidak ada program yang terdaftar untuk kelompok keahlian ini.
            </div>
        @else
            @foreach($programs as $program)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <h5>{{ $program->{"Nama Program"} ?? 'Program Tidak Ditemukan' }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">{{ $program->Deskripsi ?? 'Deskripsi tidak tersedia.' }}</p>
                        <h6 class="text-primary">Daftar Kategori Program</h6>
<div class="row">
    @if(isset($categories[$program->Key]) && count($categories[$program->Key]) > 0)
    @foreach($categories[$program->Key] as $kat)
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6>{{ $kat->{"Nama Kategori"} ?? 'Kategori Tidak Ditemukan' }}</h6>
                    <p class="mb-1">{{ $kat->Deskripsi ?? 'Deskripsi tidak tersedia.' }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ $kat->Status ?? 'Tidak Diketahui' }}</p>
                    <p><strong>Materi Aktif:</strong> {{ $kat->MateriCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    @endforeach
@else
    <p class="text-muted">Tidak ada kategori untuk program ini.</p>
@endif

</div>

                    </div>
                </div>
            @endforeach
        @endif

        <!-- Daftar Anggota -->
        <h3 class="text-primary">Anggota Kelompok Keahlian</h3>
        <div class="row">
            @if(!empty($anggota) && count($anggota) > 0)
                @foreach($anggota as $member)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $member->{"Nama Anggota"} ?? 'Nama Tidak Ditemukan' }}</h6>
                                    <p class="mb-0 text-muted">{{ $member->Prodi ?? '-' }}</p>
                                    <span class="badge bg-success">{{ $member->Status ?? 'Tidak Aktif' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center">Tidak ada anggota dalam kelompok keahlian ini.</p>
            @endif
        </div>
    @endif
</div>

@include('backbone.footer')
