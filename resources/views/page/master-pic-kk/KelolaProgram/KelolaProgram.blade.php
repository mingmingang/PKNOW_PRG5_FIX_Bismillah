<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Program - P-KNOW</title>
</head>

@include('backbone.headerPICKK', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
    ])

<div class="backSearch">
    <h1>Kelola Program</h1>
    <p>ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian yang biasa disebut dengan Kelompok Keahlian.</p>
</div>

<div class="container mt-5">
@if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ $errors->first() }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    @if(isset($error))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @else
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="text-primary">{{ $dataKK->{"Nama Kelompok Keahlian"} }}</h3>
                <p>{{ $dataKK->Deskripsi }}</p>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-clipboard-list"></i> {{ count($programs) }} Program
                    </div>
                    <div>
                        <i class="fas fa-user"></i> 1 Anggota <!-- Ubah sesuai data -->
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <!-- Button Tambah Program -->
                    <button class="btn btn-primary" onclick="handleAddProgram()">
                        <i class="fas fa-plus-circle"></i> Tambah Program
                    </button>
                    <!-- Button Detail Kelompok Keahlian -->
                    <button class="btn btn-secondary" onclick="handleDetailKelompokKeahlian('{{ $kkeId }}')">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <h4 class="text-primary">Daftar Program dalam Kelompok Keahlian {{ $dataKK->{"Nama Kelompok Keahlian"} }}</h4>

        @if(empty($programs) || (isset($programs[0]->Message) && $programs[0]->Message === 'data kosong'))
            <div class="alert alert-warning mt-3">
                Tidak ada data! Silahkan klik tombol tambah program di atas..
            </div>
        @else
            <div class="accordion" id="programAccordion">
    @foreach($programs as $program)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center" id="heading{{ $program->Key }}">
                <div class="d-flex align-items-center">
                    <img src="{{ $program->Gambar ? asset($program->Gambar) : asset('default.jpg') }}" alt="Program Image" class="img-thumbnail me-3" style="width: 50px; height: 50px;">
                    <div>
                        <h5 class="program-title">
                            {{ $program->{"Nama Program"} }}
                        </h5>
                        <p class="mb-0 text-muted">{{ Str::limit($program->Deskripsi, 100) }}</p>
                    </div>
                </div>
                <div class="d-flex">
                    <!-- Icon Edit -->
                    <button class="btn btn-sm btn-primary me-2" title="Edit Program" onclick="handleEditProgram('{{ $program->Key }}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Delete Button -->
                    @if($program->Status === 'Draft')
                        <button class="btn btn-sm btn-danger me-2" title="Hapus Program" onclick="handleDeleteProgram('{{ $program->Key }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                    <!-- Send Button -->
                    @if($program->Status === 'Draft')
                        <button class="btn btn-sm btn-success me-2" title="Kirim Program" onclick="handleSendProgram('{{ $program->Key }}')">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    @endif
                    <button class="btn btn-light btn-sm" onclick="toggleStatus(this, '{{ $program->Key }}', '{{ $program->Status }}', 'program')">
                        <i class="{{ $program->Status === 'Aktif' ? 'fas fa-toggle-on text-primary' : 'fas fa-toggle-off text-secondary' }}"></i>
                    </button>

                    <!-- Dropdown Kategori -->
                    <button
                        class="btn btn-sm btn-outline-secondary dropdown-icon"
                        type="button"
                        data-toggle="collapse"
                        data-target="#collapse-{{ $program->Key }}"
                        aria-expanded="false"
                        aria-controls="collapse-{{ $program->Key }}">
                        <i id="icon-{{ $program->Key }}" class="fas fa-chevron-down"></i>
                    </button>


                </div>
            </div>
            <div id="collapse-{{ $program->Key }}" class="collapse" aria-labelledby="heading{{ $program->Key }}" data-parent="#programAccordion">
    <div class="card-body">
        <h5 class="text-primary">Daftar Kategori Program</h5>
        <!-- Tombol Tambah Kategori -->
        <button class="btn btn-sm btn-outline-primary mb-3" onclick="handleAddCategory('{{ $program->Key }}')">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>

        <!-- Kategori Program -->
        <div id="categories-{{ $program->Key }}">

        @php
        $programCategories = collect($categories)->filter(function ($category) use ($program) {
            return (string) $category->ProID === (string) $program->Key;
        });
        @endphp


            @if ($programCategories->isEmpty())
                <p class="text-muted">Tidak ada kategori ditemukan.</p>
            @else
            @if ($programCategories && count($programCategories) > 0)
                @foreach ($programCategories as $category)
                    <div class="col-md-6 mb-3">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>
                                    {{ $category->{"Nama Kategori"} }}
                                    @if ($category->Status === 'Draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @elseif ($category->Status === 'Aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif ($category->Status === 'Tidak Aktif')
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </h6>
                                <span class="badge bg-info text-white">{{ $category->MateriCount ?? 0 }} Materi</span>
                            </div>
                            <p>{{ $category->Deskripsi }}</p>

                            <!-- Ikon Aksi -->
                            <div class="d-flex justify-content-end mt-2">
                                @if ($category->Status === 'Draft')
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-primary me-2" title="Edit Kategori" onclick="handleEditCategory('{{ $category->Key }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-sm btn-danger me-2" title="Hapus Kategori" onclick="handleDeleteCategory('{{ $category->Key }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <!-- Tombol Kirim -->
                                    <button class="btn btn-sm btn-success" title="Kirim Kategori" onclick="handleSendCategory('{{ $category->Key }}')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                @else
                                    <!-- Tombol Nonaktifkan -->
                                    <button class="btn btn-sm btn-secondary" title="Nonaktifkan Kategori" onclick="toggleStatus(this, '{{ $category->Key }}', '{{ $category->Status }}', 'category')">
                                        <i class="fas {{ $category->Status === 'Aktif' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                @else
    <p class="text-muted">Tidak ada kategori ditemukan.</p>
@endif
            @endif
        </div>
    </div>
</div>

        </div>
    @endforeach
</div>

        @endif
    @endif
</div>


<script>
    function handleAddCategory(programId) {
        const role = "{{ Cookie::get('role') }}";
        console.log("prg id", programId);
        window.location.href = `/kelola_program/${role}/${programId}/tambah_kategori`;
    }

function toggleCategories(programKey) {
    const container = document.getElementById(`collapse-${programKey}`);
    const icon = document.getElementById(`icon-${programKey}`);

    if (!container || !icon) {
        console.error(`Container atau ikon tidak ditemukan untuk programKey: ${programKey}`);
        return;
    }

    // Toggle visibility
    if (container.classList.contains("show")) {
        container.classList.remove("show"); // Tutup kategori
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
    } else {
        container.classList.add("show"); // Tampilkan kategori
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
    }
}





    function loadCategories(programKey) {
        console.log(`Memuat kategori untuk program ${programKey}`);
        
        // Ambil elemen kontainer kategori
        const container = document.querySelector(`#categories-${programKey}`);
        
        // Pastikan kontainer ada
        if (!container) {
            console.error(`Container untuk program ${programKey} tidak ditemukan.`);
            return;
        }
        
        // Cek apakah kategori sudah dimuat sebelumnya
        if (container.getAttribute('data-loaded')) {
            console.log('Kategori sudah dimuat.');
            return;
        }

        // Simulasi pemuatan kategori (ganti dengan logika sebenarnya jika perlu)
        setTimeout(() => {
            container.innerHTML = `
                <div class="col-md-6 mb-3">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Contoh Kategori</h6>
                            <span class="badge bg-info text-white">5</span>
                        </div>
                        <p>Deskripsi kategori</p>
                    </div>
                </div>
            `;

            // Tandai kategori sudah dimuat
            container.setAttribute('data-loaded', 'true');
            console.log(`Kategori untuk program ${programKey} berhasil dimuat.`);
        }, 1000); // Simulasi pemuatan dengan jeda 1 detik
    }

    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', () => {
            console.log('Dropdown clicked:', button.getAttribute('data-bs-target'));
        });
    });

    function handleEditProgram(programKey) {
        const role = "{{ Cookie::get('role') }}"; // Pastikan role tersedia di cookie
        window.location.href = `/kelola_program/${role}/${programKey}/edit`;
    }

    function handleDeleteProgram(programKey) {
        const role = "{{ Cookie::get('role') }}";

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Program ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/kelola_program/${role}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute("content")
                    },
                    body: JSON.stringify({ programKey })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire('Berhasil!', result.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal!', result.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                });
            }
        });
    }

    function handleSendProgram(programKey) {
        const role = "{{ Cookie::get('role') }}";

        fetch(`/kelola_program/${role}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute("content")
            },
            body: JSON.stringify({ programKey })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire('Berhasil!', result.message, 'success');
                location.reload();
            } else {
                Swal.fire('Gagal!', result.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
        });
    }

function toggleStatus(button, id, currentStatus, type) {
    const newStatus = currentStatus === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
    const endpoint = type === 'program' ? '/kelola_program/toggleStatus' : '/kelola_kategori/toggleStatus';

    // Tambahkan loader
    const loader = document.createElement('span');
    loader.classList.add('spinner-border', 'spinner-border-sm', 'ms-2');
    button.appendChild(loader);

    fetch(endpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content"),
        },
        body: JSON.stringify({ id, newStatus }),
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                Swal.fire("Berhasil!", result.message, "success");

                const icon = button.querySelector("i");
                if (newStatus === "Aktif") {
                    icon.classList.remove("fa-toggle-off", "text-secondary");
                    icon.classList.add("fa-toggle-on", "text-primary");
                } else {
                    icon.classList.remove("fa-toggle-on", "text-primary");
                    icon.classList.add("fa-toggle-off", "text-secondary");
                }

                button.setAttribute("onclick", `toggleStatus(this, '${id}', '${newStatus}', '${type}')`);
            } else {
                Swal.fire("Gagal!", result.message, "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            Swal.fire("Error!", "Terjadi kesalahan saat mengubah status.", "error");
        })
        .finally(() => {
            loader.remove(); // Hapus loader setelah proses selesai
        });
}



    function handleAddProgram() {
    const role = "{{ Cookie::get('role') }}";
    const kkeId = "{{ $kkeId }}";
    console.log("kke id", kkeId);
    window.location.href = `/kelola_program/${role}/${kkeId}/tambah`;
    }

    function handleDetailKelompokKeahlian(kkeId) {
    const role = "{{ Cookie::get('role') }}";
        window.location.href = `/kelola_program/${role}/${kkeId}/detail`;
    }

    function handleEditCategory(categoryKey) {
        const role = "{{ Cookie::get('role') }}"; // Pastikan role tersedia di cookie
        window.location.href = `/kelola_program/${role}/${categoryKey}/edit_kategori`;
    }

function handleSendCategory(categoryKey) {
    const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie

    fetch(`/kelola_kategori/${role}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute("content"),
        },
        body: JSON.stringify({
            categoryKey: categoryKey, // Kirim categoryKey
        }),
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire('Berhasil!', result.message, 'success');
            location.reload();
        } else {
            Swal.fire('Gagal!', result.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
    });
}

function handleDeleteCategory(categoryKey) {
    const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Kategori ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/kelola_kategori/${role}/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute("content"),
                },
                body: JSON.stringify({
                    categoryKey: categoryKey, // Kirim categoryKey
                }),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success');
                    location.reload();
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
            });
        }
    });
}




</script>

@include('backbone.footer')
