<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Pustaka - P-KNOW</title>
</head>
@include('backbone.headerpicpknow', [
'showMenu' => false, // Menyembunyikan menu pada halaman login
'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
'menuItems' => [],
'konfirmasi' => 'Konfirmasi',
'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
'kelolaProgram' => false,
'showConfirmation' => false, // Pastikan variabel ini ada untuk header
])

<!-- Back Ground Image, Search Box -->
<div
    class=""
    style="background-image: url('{{ asset('assets/backgroundSearch.png') }}'); 
           background-size: cover; 
           background-position: center; 
           padding: 20px; 
           border-radius: 0px; 
           color: white; 
           margin-top: 80px;
           text-align: center;
           height: 200px;">
    <h1>Daftar Pustaka</h1>
    <p>
        ASTRAtech memiliki banyak program studi, di dalam program studi
        terdapat kelompok keahlian <br>yang biasa disebut dengan Kelompok
        Keahlian
    </p>
    <form id=" searchForm" class="d-flex" style="justify-content: center; align-items: center; gap: 10px;">
        <div class="input-wrapper search d-flex">
            <div class="d-flex" style="background:white; padding:0px 0px;justify-content:space-between;border-radius:30px;">
                <input type="text" id="searchQuery" placeholder="Cari Daftar Pustaka" style="width:600px; padding:10px; border:none;border-radius:30px">
                <button class="search-btn" onclick="handleSearch()" style="border:none; background:transparent; color: rgb(8, 84, 159); margin:10px;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Judul, Filters, and Tambah -->
<div class="container input-wrapper mb-1 d-flex align-items-center mt-3 mb-3 ">
    <h2 class="flex-grow-1" style="color : rgba(10, 94, 168, 1)">Daftar Pustaka</h2>
    <div class="d-flex align-items-center">
        <select id="searchFilterSort" class="form-select mx-2" onchange="handleChange()">
            @foreach($dataFilterSort as $filter)
            <option value="{{ $filter['Value'] }}">{{ $filter['Text'] }}</option>
            @endforeach
        </select>
        <select id="searchFilterStatus" class="form-select mx-2" onchange="handleChange()">
            @foreach($dataFilterStatus as $filter)
            <option value="{{ $filter['Value'] }}">{{ $filter['Text'] }}</option>
            @endforeach
        </select>
        <button class="btn" style="background-color: rgba(0, 123, 255, 1); color: white;" onclick="handleAdd()">
            <i class="fas fa-add"></i> Tambah Pustaka
        </button>
    </div>
</div>

<div class="container mt-3"> 
    <!-- Pustaka Saya -->
    <div class="card-keterangan" style="background: #198754; border-radius: 5px; padding: 10px 20px; width: 40%; margin-bottom: 20px; color: white; font-weight: bold;">
        ↓ Pustaka Saya
    </div>
    <div id="dataContainerMyPustaka" class="row"></div>

    <!-- Pustaka Publik -->
    <div class="card-keterangan" style="background: #67ACE9; border-radius: 5px; padding: 10px 20px; width: 40%; margin-bottom: 20px; color: white; font-weight: bold;">
        ↓ Aktif / Pustaka Lain
    </div>
    <div id="dataContainerPublicPustaka" class="row"></div>
</div>

<script>
    const name = "{{ Cookie::get('usr_id') }}"; // Ambil user aktif dari cookie
    const containerMyPustaka = document.getElementById('dataContainerMyPustaka');
    const containerPublicPustaka = document.getElementById('dataContainerPublicPustaka');
    const data = @json($data);

    // Reset kontainer
    containerMyPustaka.innerHTML = '';
    containerPublicPustaka.innerHTML = '';

    if (data.length === 0) {
        containerMyPustaka.innerHTML = '<p class="text-center">Tidak ada data!</p>';
        containerPublicPustaka.innerHTML = '<p class="text-center">Tidak ada data!</p>';
    } else {
        data.forEach(book => {
            // Template kartu dengan tombol aksi yang bervariasi berdasarkan kondisi
            const cardTemplate = (isOwner) => `
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="${book.Gambar ? '/' + book.Gambar : ''}" class="card-img-top" alt="${book['Kelompok Keahlian']}">
                        <div class="card-body">
                            <h3 class="text-xl font-bold text-blue-600" style="font-size: 20px; width: 100%;">${book.Judul}</h3>
                            <div class="kk" style="font-size: 18px; font-weight: bold;">
                                <i class="fas fa-book" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                <span>${book['Kelompok Keahlian']}</span>
                            </div>
                            <div class="mb-1 mt-2">
                                <i class="fas fa-user" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                <span style="font-size: 16px; font-weight: 600;">${book.Uploader} • ${new Date(book.Creadate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</span>
                            </div>
                            <p class="card-text">${book.Keterangan}</p>
                            <p class="card-text"><strong>Status:</strong> ${book.Status}</p>
                            ${
                                isOwner
                                    ? `
                                        <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleEdit('${book.Key}')">
                                            <i class="fas fa-edit"></i> 
                                        </button>
                                        <button class="btn" style="color: rgba(255, 0, 0, 1);" onclick="handleDelete('${book.Key}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleLihat('${book.Key}')">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <i 
                                            id="statusToggle-${book.Key}" 
                                            class="fas ${book.Status === 'Aktif' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}" 
                                            style="font-size: 24px; cursor: pointer;"
                                            onclick="handleStatusChange('${book.Key}', '${book.Status === 'Aktif' ? 'Tidak Aktif' : 'Aktif'}')">
                                        </i>
                                    `
                                    : `
                                        <button class="btn btn-primary" onclick="handleLihat('${book.Key}')">
                                            Lihat
                                        </button>
                                    `
                            }
                        </div>
                    </div>
                </div>
            `;

            // Tentukan apakah pustaka milik pengguna pemilik
            const isOwner = name === book.Uploader || name === book.PemilikKK;

            // Tambahkan ke kontainer yang sesuai
            if (isOwner) {
                containerMyPustaka.innerHTML += cardTemplate(true);
            } else {
                containerPublicPustaka.innerHTML += cardTemplate(false);
            }
        });
    }
</script>



<script>
    function handleAdd() {
        // console.log("Navigating to tambah page");
        const role = "{{ Cookie::get('role') }}";
        const pengguna = "{{ Cookie::get('pengguna') }}";
        const name = "{{ Cookie::get('usr_id') }}";
        console.log("role:", role, "pengguna:", pengguna, "name:", name);

        if (!role || !name) {
            console.error('Role or pengguna parameter missing');
            return;
        }
        window.location.href = `/daftar_pustaka/${role}/tambah`;
        console.log("Tes")
    }

    function handleEdit(id) {
        const role = "{{ Cookie::get('role') }}";
        const pengguna = "{{ Cookie::get('pengguna') }}";
        const name = "{{ Cookie::get('usr_id') }}";
        if (!role) {
            console.error('Role parameter missing');
            return;
        }
        window.location.href = `/daftar_pustaka/${role}/edit/${id}`;
    }

    function handleLihat(id) {
        const role = "{{ Cookie::get('role') }}";
        const pengguna = "{{ Cookie::get('pengguna') }}";
        const name = "{{ Cookie::get('usr_id') }}";
        if (!role) {
            console.error('Role parameter missing');
            return;
        }
        window.location.href = `/daftar_pustaka/${role}/lihat/${id}`;
    }


    async function handleDelete(id) {
        // Tampilkan konfirmasi menggunakan SweetAlert
        const confirmation = await Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        });

        if (!confirmation.isConfirmed) {
            return; // Batalkan jika pengguna memilih "Batal"
        }

        try {
            const role = "{{ Cookie::get('role') }}";
            // Kirim permintaan DELETE
            const response = await fetch(`/daftar_pustaka/deletePustaka/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                // Tampilkan pesan sukses dengan SweetAlert
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
                window.location.reload(); // Refresh data setelah penghapusan berhasil
            } else {
                // Tampilkan pesan error dengan SweetAlert
                Swal.fire({
                    title: 'Gagal!',
                    text: result.message,
                    icon: 'error',
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus data.',
                icon: 'error',
            });
        }
    }

    async function handleStatusChange(id, status) {
        try {
            const response = await fetch(`/daftar_pustaka/setStatusPustaka/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    status
                }),
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
                window.location.reload(); // Refresh data setelah penghapusan berhasil
                // Update status di UI
                const checkbox = document.querySelector(`#statusToggle-${id}`);
                const label = document.querySelector(`label[for="statusToggle-${id}"]`);
                if (checkbox) checkbox.checked = result.newStatus === 'Aktif';
                if (label) label.textContent = result.newStatus;
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: result.message,
                    icon: 'error',
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengubah status.',
                icon: 'error',
            });
        }
    }

    function handleSearch() {
        const role = "{{ Cookie::get('role') }}";
        const page = 1; // Halaman pencarian
        const query = document.getElementById('searchQuery').value || '';
        const status = document.getElementById('searchFilterStatus').value || '';
        const sort = document.getElementById('searchFilterSort').value || '[Kelompok Keahlian] ASC';

        const dataContainerMyPustaka = document.getElementById('dataContainerMyPustaka');
        const dataContainerPublicPustaka = document.getElementById('dataContainerPublicPustaka');
        dataContainerMyPustaka.innerHTML = '<p>Loading...</p>';
        dataContainerPublicPustaka.innerHTML = '<p>Loading...</p>';

        // Kirim data ke backend menggunakan fetch
        fetch(`/daftar_pustaka/${role}/search`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    page: page,
                    query: query,
                    status: status,
                    sort: sort,
                    ...Array(46).fill(null),
                }),
            })
            .then(response => {
                console.log("errr", response)
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                dataContainerMyPustaka.innerHTML = '';
                dataContainerPublicPustaka.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    dataContainerMyPustaka.innerHTML = '<p>Tidak ada data!</p>';
                    dataContainerPublicPustaka.innerHTML = '<p>Tidak ada data!</p>';
                    return;
                }

                data.forEach(book => {
                    const isOwner = "{{ Cookie::get('usr_id') }}" === book.Uploader;

                    const cardTemplate = `
                        <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="${book.Gambar ? '/' + book.Gambar : ''}" class="card-img-top" alt="${book['Kelompok Keahlian']}">
                            <div class="card-body">
                                <h3 class="text-xl font-bold text-blue-600" style="font-size: 20px; width: 100%;">${book.Judul}</h3>
                                <div class="kk" style="font-size: 18px; font-weight: bold;">
                                    <i class="fas fa-book" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                    <span>${book['Kelompok Keahlian']}</span>
                                </div>
                                <div class="mb-1 mt-2">
                                    <i class="fas fa-user" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                    <span style="font-size: 16px; font-weight: 600;">${book.Uploader} • ${new Date(book.Creadate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</span>
                                </div>
                                <p class="card-text">${book.Keterangan}</p>
                                <p class="card-text"><strong>Status:</strong> ${book.Status}</p>
                                ${
                                    isOwner
                                        ? `
                                            <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleEdit('${book.Key}')">
                                                <i class="fas fa-edit"></i> 
                                            </button>
                                            <button class="btn" style="color: rgba(255, 0, 0, 1);" onclick="handleDelete('${book.Key}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleLihat('${book.Key}')">
                                                <i class="fas fa-list"></i>
                                            </button>
                                            <i 
                                                id="statusToggle-${book.Key}" 
                                                class="fas ${book.Status === 'Aktif' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}" 
                                                style="font-size: 24px; cursor: pointer;"
                                                onclick="handleStatusChange('${book.Key}', '${book.Status === 'Aktif' ? 'Tidak Aktif' : 'Aktif'}')">
                                            </i>
                                        `
                                        : `
                                            <button class="btn btn-primary" onclick="handleLihat('${book.Key}')">
                                                Lihat
                                            </button>
                                        `
                                }
                            </div>
                        </div>
                    </div>
                    `;

                    if (isOwner) {
                        dataContainerMyPustaka.innerHTML += cardTemplate;
                    } else {
                        dataContainerPublicPustaka.innerHTML += cardTemplate;
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                dataContainerMyPustaka.innerHTML = '<p>Terjadi kesalahan saat mengambil data.</p>';
                dataContainerPublicPustaka.innerHTML = '<p>Terjadi kesalahan saat mengambil data.</p>';
            });
    }

    function handleChange() {
        const role = "{{ Cookie::get('role') }}"; // Role pengguna
        const page = 1; // Halaman pencarian (biasanya dimulai dari 1)

        const query = document.getElementById('searchQuery').value || ''; // Query pencarian
        const sort = document.getElementById('searchFilterSort').value || '[Judul] ASC'; // Filter urutan
        const status = document.getElementById('searchFilterStatus').value || ''; // Filter status

        const dataContainerMyPustaka = document.getElementById('dataContainerMyPustaka');
        const dataContainerPublicPustaka = document.getElementById('dataContainerPublicPustaka');
        dataContainerMyPustaka.innerHTML = '<p>Loading...</p>';
        dataContainerPublicPustaka.innerHTML = '<p>Loading...</p>';

        console.log("status", status)
        console.log("sort", sort)

        // Kirim data ke backend menggunakan fetch
        fetch(`/daftar_pustaka/${role}/search`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    page: page,
                    query: query, // Kirim query kosong jika tidak ada input
                    sort: sort,
                    status: status,
                    ...Array(46).fill(null), // Pastikan 50 parameter yang dikirimkan
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                dataContainerMyPustaka.innerHTML = '';
                dataContainerPublicPustaka.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    dataContainerMyPustaka.innerHTML = '<p>Tidak ada data!</p>';
                    dataContainerPublicPustaka.innerHTML = '<p>Tidak ada data!</p>';
                    return;
                }

                data.forEach(book => {
                    const isOwner = "{{ Cookie::get('usr_id') }}" === book.Uploader;

                    const cardTemplate = `
                        <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="${book.Gambar ? '/' + book.Gambar : ''}" class="card-img-top" alt="${book['Kelompok Keahlian']}">
                            <div class="card-body">
                                <h3 class="text-xl font-bold text-blue-600" style="font-size: 20px; width: 100%;">${book.Judul}</h3>
                                <div class="kk" style="font-size: 18px; font-weight: bold;">
                                    <i class="fas fa-book" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                    <span>${book['Kelompok Keahlian']}</span>
                                </div>
                                <div class="mb-1 mt-2">
                                    <i class="fas fa-user" style="margin-right: 10px; color: black; font-size: 20px;"></i>
                                    <span style="font-size: 16px; font-weight: 600;">${book.Uploader} • ${new Date(book.Creadate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</span>
                                </div>
                                <p class="card-text">${book.Keterangan}</p>
                                <p class="card-text"><strong>Status:</strong> ${book.Status}</p>
                                ${
                                    isOwner
                                        ? ` 
                                            <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleEdit('${book.Key}')">
                                                <i class="fas fa-edit"></i> 
                                            </button>
                                            <button class="btn" style="color: rgba(255, 0, 0, 1);" onclick="handleDelete('${book.Key}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn" style="color: rgba(0, 123, 255, 1);" onclick="handleLihat('${book.Key}')">
                                                <i class="fas fa-list"></i>
                                            </button>
                                            <i 
                                                id="statusToggle-${book.Key}" 
                                                class="fas ${book.Status === 'Aktif' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}" 
                                                style="font-size: 24px; cursor: pointer;"
                                                onclick="handleStatusChange('${book.Key}', '${book.Status === 'Aktif' ? 'Tidak Aktif' : 'Aktif'}')">
                                            </i>
                                        `
                                        : ` 
                                            <button class="btn btn-primary" onclick="handleLihat('${book.Key}')">
                                                Lihat
                                            </button>
                                        `
                                }
                            </div>
                        </div>
                    </div>
                    `;

                    if (isOwner) {
                        dataContainerMyPustaka.innerHTML += cardTemplate;
                    } else {
                        dataContainerPublicPustaka.innerHTML += cardTemplate;
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                dataContainerMyPustaka.innerHTML = '<p>Terjadi kesalahan saat mengambil data.</p>';
                dataContainerPublicPustaka.innerHTML = '<p>Terjadi kesalahan saat mengambil data.</p>';
            });
    }
</script>


<script>
    // Deteksi flash message untuk sukses
    @if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
    });
    @endif

    // Deteksi flash message untuk error
    @if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        timer: 3000,
        showConfirmButton: false,
    });
    @endif
    //
</script>