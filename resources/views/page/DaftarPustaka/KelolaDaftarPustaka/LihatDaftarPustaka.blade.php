<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Tambah Daftar Pustaka</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@include('backbone.headerpicpknow', [
'showMenu' => false,
'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
'menuItems' => [],
'konfirmasi' => 'Konfirmasi',
'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
'kelolaProgram' => false,
'showConfirmation' => false,
])

<div class="container mt-5">
    <h4 class="text-primary font-weight-bold mb-4">Detail Daftar Pustaka</h4>
    <h1 class="title" style="color : rgba(10, 94, 168, 1)">Daftar Pustaka</h1>
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <!-- Gambar -->
                    <img
                        src="{{ $data->pus_gambar ? asset($data->pus_gambar) : asset('images/NoImage.png') }}"
                        alt="Gambar Pustaka"
                        class="img-thumbnail"
                        style="border-radius: 20px; width: 100%; height: auto; object-fit: cover;">
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <h3 style="color : rgba(10, 94, 168, 1)">Judul</h3>
                        <p>{{ $data->pus_judul }}</p>
                    </div>
                    <div class="mb-3">
                        <h3 style="color : rgba(10, 94, 168, 1)">Deskripsi</h3>
                        <p>{{ $data->pus_keterangan }}</p>
                    </div>
                    <div class="mb-3">
                        <h3 style="color : rgba(10, 94, 168, 1)">Kata Kunci</h3>
                        <p>{{ $data->pus_kata_kunci }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h4 style="color : rgba(10, 94, 168, 1)">File Preview</h4>
                <div class="file-preview">
                    @php
                    $fileExtension = strtolower(pathinfo($data->pus_file, PATHINFO_EXTENSION));
                    @endphp

                    @if($fileExtension === 'pdf')
                    <!-- Preview PDF -->
                    <iframe src="{{ asset($data->pus_file) }}" style="width: 100%; height: 100vh" frameborder="0"></iframe>
                    @elseif($fileExtension === 'mp4')
                    <!-- Video Player -->
                    <video controls style="width: 100%; height: auto;">
                        <source src="{{ asset($data->pus_file) }}" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                    @elseif($fileExtension === 'docx')
                    <!-- Word Viewer -->
                    <p>Dokumen Word tidak dapat ditampilkan langsung. Silakan unduh file untuk melihatnya.</p>
                    <a href="{{ asset($data->pus_file) }}" class="btn btn-primary" download>Unduh File</a>
                    @elseif($fileExtension === 'xlsx')
                    <!-- Excel Viewer -->
                    <p>Dokumen Excel tidak dapat ditampilkan langsung. Silakan unduh file untuk melihatnya.</p>
                    <a href="{{ asset($data->pus_file) }}" class="btn btn-primary" download>Unduh File</a>
                    @elseif($fileExtension === 'pptx')
                    <!-- PowerPoint Viewer -->
                    <p>Dokumen PowerPoint tidak dapat ditampilkan langsung. Silakan unduh file untuk melihatnya.</p>
                    <a href="{{ asset($data->pus_file) }}" class="btn btn-primary" download>Unduh File</a>
                    @else
                    <!-- File Tidak Didukung s-->
                    <p>File dengan format {{ $fileExtension }} tidak didukung untuk preview. Silakan unduh file untuk melihatnya.</p>
                    <a href="{{ asset($data->pus_file) }}" class="btn btn-primary" download>Unduh File</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-secondary me-3 mt-3 mb-3" onclick="window.history.back()">Kembali</button>

</div>
@include('backbone.footer')