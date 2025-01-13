<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
<div style="display: flex; justify-content: space-between; margin-top: 100px; margin-left: 100px; margin-right: 100px;">
    <div class="back-and-title" style="display: flex; margin-right: 10px;">
        <button style="background-color: transparent; border: none;" onclick="BackPage()">
            <img src="{{ asset('assets/backPage.png') }}" alt="" />
        </button>
        <h4 style="color: rgb(10, 94, 168); font-weight: bold; font-size: 30px; margin-top: 10px; margin-left: 20px;">
            Pengajuan Kelompok Keahlian
        </h4>
    </div>
    <div class="ket-draft">
        <span class="badge text-bg-dark" style="font-size: 16px;">Draft</span>
    </div>
</div>

<div class="card" style="margin: 100px; 100px; margin-top: 40px">

    <div class="row">

        <div class="col-12">
            <form action="{{ route('pengajuan_kk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kke_id" value="{{ $data->kke_id }}">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama</label><br>
                                    <span style="white-space: pre-wrap;">{{ Cookie::get('pengguna') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kelompok Keahlian</label><br>
                                    <span style="white-space: pre-wrap;">{{ $data->kke_nama }}</span>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="fw-bold mb-3">Lampiran Pendukung</div>
                                <div class="card">
                                    <div class="card-body p-4">
                                        <div class="alert alert-info fw-bold custom-alert" role="alert">
                                            Notes: Lampiran dapat berupa Sertifikat Keahlian, Surat Tugas, atau Berkas
                                            Lainnya
                                            yang berkaitan.
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p class="mb-0">Format Penamaan:
                                                    namafile_namakelompokkeahlian_namakaryawan
                                                    (Opsional)</p>
                                                <p>Contoh: SertifikasiMicrosoft_DataScience_CandraBagus</p>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm mb-3 rounded-4 py-2"
                                                id="addLampiranButton">
                                                <i class="fi fi-br-add pe-2"></i>Tambah Lampiran
                                            </button>
                                        </div>
                                        <div class="d-flex">
                                            <div style="width: 130%;">
                                                <div class="mb-3 mt-4" style="width: 600px;">
                                                    <div id="lampiranContainer">
                                                        <!-- Input Lampiran Pertama -->
                                                        <label for="lampiran_0" class="form-label fw-bold">Lampiran
                                                            1<span class="text-danger"> *</span></label>
                                                        <input class="form-control file-input" type="file"
                                                            id="lampiran_0" name="lampiran[]" accept=".pdf" required
                                                            style="width: 185%;">
                                                        <sub>Maksimum ukuran berkas adalah 10 MB</sub>
                                                    </div>
 <!-- Kontainer untuk informasi file -->
        <div class="file-info-container"></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end"
                                    style="margin-right: 20px; margin-top: 20px; margin-bottom: 20px;">
                                    <button class="btn btn-secondary btn-sm" type="button"
                                        style="margin-right: 10px; padding: 5px 15px; font-weight: bold; border-radius: 10px;">Batalkan</button>
                                    <button class="btn btn-primary btn-sm" type="submit"
                                        style="margin-right: 10px; padding: 5px 20px; font-weight: bold; border-radius: 10px; onclick="handlePengajuanKK()">Kirim</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <div style="width: 100%;">
                <label for="lampiran_${lampiranCount}" class="form-label fw-bold">Lampiran ${lampiranCount} <span class="text-danger"> *</span></label>
                <input class="form-control file-input" type="file" id="lampiran_${lampiranCount}" name="lampiran[]" accept=".pdf" required>
                <sub>Maksimum ukuran berkas adalah 10 MB</sub>
            </div>
            <button type="button" class="btn btn-danger btn-sm ms-3 delete-lampiran" data-id="${lampiranCount}" style="margin-top: 25px;">Hapus</button>
        `;
        container.appendChild(newLampiran);

        // Tambahkan event listener untuk tombol hapus baru
        addDeleteEventListener();
    });

    // Tampilkan Informasi File yang Diunggah
    // Tampilkan Informasi File yang Diunggah
    // Tampilkan Informasi File yang Diunggah
document.addEventListener('change', (event) => {
    if (event.target.classList.contains('file-input')) {
        const file = event.target.files[0]; // Mendapatkan file yang dipilih
        const parentElement = event.target.parentElement; // Elemen induk dari input file
        let fileInfoContainer = parentElement.querySelector('.file-info-container'); // Elemen untuk info file

        if (!fileInfoContainer) {
            // Jika elemen untuk info file belum ada, buat elemen baru
            fileInfoContainer = document.createElement('div');
            fileInfoContainer.classList.add('file-info-container');
            parentElement.appendChild(fileInfoContainer);
        }

        if (file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB'; // Ukuran file dalam MB
            const fileName = file.name; // Nama file
            const fileExt = fileName.split('.').pop(); // Ekstensi file

            // Menampilkan informasi file
            fileInfoContainer.innerHTML = `
                <div class="mt-2">
                    <strong>File Info:</strong>
                    <ul>
                        <li>Nama: ${fileName}</li>
                        <li>Ukuran: ${fileSize}</li>
                        <li>Ekstensi: ${fileExt}</li>
                    </ul>
                </div>`;
        } else {
            // Reset jika tidak ada file yang dipilih
            fileInfoContainer.innerHTML = '<div class="mt-2"><sub>Maksimum ukuran berkas adalah 10 MB</sub></div>';
        }
    }
});



    // Fungsi untuk menambahkan event listener pada tombol hapus
    function addDeleteEventListener() {
        const deleteButtons = document.querySelectorAll('.delete-lampiran');
        deleteButtons.forEach((button) => {
            button.removeEventListener('click', handleDelete); // Hindari duplikasi event listener
            button.addEventListener('click', handleDelete);
        });
    }

    // Fungsi hapus input lampiran
    function handleDelete(event) {
        const lampiranId = event.target.getAttribute('data-id');
        const lampiranElement = document.getElementById(`lampiran${lampiranId}`);
        if (lampiranElement) {
            lampiranElement.remove();
        }
    }

    // Tambahkan event listener awal untuk tombol hapus pertama
    addDeleteEventListener();
</script>


<script>
    function BackPage() {
        window.location.href = "{{ route('pengajuan_KK') }}";
    }
</script>

@include('backbone.footer')
