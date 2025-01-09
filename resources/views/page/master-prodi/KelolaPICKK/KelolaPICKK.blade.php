<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Kelompok Keahlian - Program Studi</title>
</head>

@include('backbone.HeaderProdi', [
    'showMenu' => true,
    'userProfile' => ['name' => 'User', 'role' => 'Program Studi', 'lastLogin' => now()],
    'menuItems' => [
        ['name' => 'Beranda', 'link' => '/dashboard'],
        ['name' => 'Kelompok Keahlian', 'link' => '/kelola_kk'],
    ],
    'kelolaProgram' => true,
    'showConfirmation' => false
])

<div class="container" style="margin-top: 120px;">
    <h1>Menentukan PIC Kelompok Keahlian</h1>
    <p>PIC Kelompok Keahlian dapat memodifikasi kelompok keahlian yang telah dibuat sebelumnya. Segala aktifitas kegiatan yang dilakukan akan diperiksa oleh PIC Kelompok Keahlian.</p>

    <!-- Status Legend -->
    <div class="mb-4">
        <table>
            <tbody>
                <tr>
                    <td><i class="fas fa-circle" style="color: #4a90e2;"></i></td>
                    <td><p>Aktif/Sudah Publikasi</p></td>
                </tr>
                <tr>
                    <td><i class="fas fa-circle" style="color: #b0b0b0;"></i></td>
                    <td><p>Menunggu PIC dari Prodi</p></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Menunggu PIC -->
    <div class="mb-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5>↓ Menunggu PIC dari Prodi</h5>
            </div>
        </div>
        <div class="row mt-3">
            @forelse ($data as $item)
                @if ($item->Status === 'Menunggu')
                    <div class="col-md-4 mb-4">
                        <div class="card">
                        <img src="{{ asset($item->Gambar) }}" class="card-img-top" alt="{{ $item->NamaKelompokKeahlian }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->NamaKelompokKeahlian }}</h5>
                                <p class="card-text">{{ $item->Deskripsi }}</p>
                                <button class="btn btn-primary btn-sm" onclick="editPIC('{{ $item->KeyId }}')">Tambah PIC</button>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center">Tidak ada data menunggu PIC!</p>
            @endforelse
        </div>
    </div>

    <!-- Aktif/Sudah Publikasi -->
    <div class="mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>↓ Data Aktif / Sudah Dipublikasikan</h5>
            </div>
        </div>
        <div class="row mt-3">
            @forelse ($data as $item)
                @if ($item->Status === 'Aktif')
                    <div class="col-md-4 mb-4">
                        <div class="card">
                        <img src="{{ asset($item->Gambar) }}" class="card-img-top" alt="{{ $item->NamaKelompokKeahlian }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->NamaKelompokKeahlian }}</h5>
                                <p class="card-text">{{ $item->Deskripsi }}</p>
                                <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $role }}', '{{ $item->KeyId }}')">
    <i class="fas fa-eye"></i> Lihat
</button>

                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center">Tidak ada data aktif!</p>
            @endforelse
        </div>
    </div>
</div>

@include('backbone.footer')

<script>
    function editPIC(id) {
        window.location.href = `/prodi/edit-pic/${id}`;
    }

    function viewDetail(role, id) {
    window.location.href = `/kelola_kk/${role}/detail/${id}`;
}


</script>
