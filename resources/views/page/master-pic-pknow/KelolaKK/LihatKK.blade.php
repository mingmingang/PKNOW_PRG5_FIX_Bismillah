<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Detail Kelompok Keahlian</title>
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

<div class="container" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Detail Kelompok Keahlian</h4>
        </div>

        <div class="col-12">
            <form>
                <div class="mb-3">
                    <label class="form-label">Nama Kelompok Keahlian</label>
                    <input type="text" class="form-control" value="{{ $data->kke_nama }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" rows="4" readonly>{{ $data->kke_deskripsi }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Program Studi</label>
                    <input type="text" class="form-control" value="{{ $data->pro_nama }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Person In Charge</label>
                    <input type="text" class="form-control" value="{{ $data->kry_nama ?? 'Tidak ada' }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $data->kke_status }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    @if($data->kke_gambar)
                        <div class="mt-2">
                            <img src="{{ asset($data->kke_gambar) }}" alt="Gambar Kelompok Keahlian" width="200" class="rounded">
                        </div>
                    @else
                        <p>Tidak ada gambar.</p>
                    @endif
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('kelola_kk', ['role' => urlencode($role)]) }}" class="btn btn-secondary me-2">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

@include('backbone.footer')
