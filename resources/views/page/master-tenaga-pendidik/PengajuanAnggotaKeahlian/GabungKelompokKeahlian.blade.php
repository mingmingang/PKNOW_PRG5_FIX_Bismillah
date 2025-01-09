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
<div class="container" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Pengajuan Kelompok Keahlian</h4>
                   </div>

        <div class="col-12">
            <form action="{{ route('pengajuan_kk.store') }}" method="POST" enctype="multipart/form-data"
                id="formId">
                @csrf
                <input type="hidden" name="kke_id" value="{{ $data->kke_id }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" value="{{ Cookie::get('pengguna') }}"
                                    readonly>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Kelompok Keahlian</label>
                                <input type="text" class="form-control" value="{{ $data->kke_nama }}" readonly>
                            </div>
                            <div class="col-12 mt-3">
                                <h5>Lampiran Pendukung</h5>
                                <p class="text-muted">
                                    Notes: Lampiran dapat berupa Sertifikat Keahlian, Surat Tugas, atau Berkas Lainnya
                                    yang berkaitan.<br>
                                    Format Penamaan: namafile_namakelompokkeahlian_namakaryawan (Opsional).<br>
                                    Contoh: SertifikasiMicrosoft_DataScience_CandraBagus
                                </p>
                                <div id="lampiranContainer">
                                    <!-- Input Lampiran Pertama -->
                                    <div class="d-flex align-items-center mb-3" id="lampiran1">
                                        <label class="form-label me-3">Lampiran 1 *</label>
                                        <input type="file" name="lampiran[]" class="form-control file-input"
                                            accept=".pdf" required>
                                        <div class="file-info ms-3 text-muted"></div>
                                    </div>
                                </div>
                                <!-- Tombol Tambah Lampiran -->
                                <button type="button" class="btn btn-primary btn-sm" id="addLampiranButton">Tambah
                                    Lampiran</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary me-2">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let lampiranCount = 1; // Mulai dari 1 karena sudah ada input pertama

    // Tambah Input Lampiran Baru
    document.getElementById('addLampiranButton').addEventListener('click', () => {
        lampiranCount++;
        const container = document.getElementById('lampiranContainer');
        const newLampiran = document.createElement('div');
        newLampiran.className = 'd-flex align-items-center mb-3';
        newLampiran.id = `lampiran${lampiranCount}`;
        newLampiran.innerHTML = `
            <label class="form-label me-3">Lampiran ${lampiranCount} *</label>
            <input type="file" name="lampiran[]" class="form-control file-input" accept=".pdf" required>
            <div class="file-info ms-3 text-muted"></div>
        `;
        container.appendChild(newLampiran);
    });

    // Tampilkan Informasi File yang Diunggah
    document.addEventListener('change', (event) => {
        if (event.target.classList.contains('file-input')) {
            const file = event.target.files[0];
            const fileInfo = event.target.nextElementSibling; // Elemen div untuk info file
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                const fileName = file.name;
                const fileExt = fileName.split('.').pop();
                fileInfo.innerHTML = `
                    <strong>File Info:</strong>
                    <ul>
                        <li>Nama: ${fileName}</li>
                        <li>Ukuran: ${fileSize}</li>
                        <li>Ekstensi: ${fileExt}</li>
                    </ul>
                `;
            } else {
                fileInfo.innerHTML = ''; // Kosongkan jika tidak ada file
            }
        }
    });
</script>


@include('backbone.footer')
