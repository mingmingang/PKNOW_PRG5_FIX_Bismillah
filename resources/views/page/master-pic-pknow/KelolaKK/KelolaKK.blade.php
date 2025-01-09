<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Kelompok Keahlian - P-KNOW</title>
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

    <div class="backSearch">
        <h1>Kelola Kelompok Keahlian</h1>
        <p>ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian yang biasa disebut dengan Kelompok Keahlian.</p>
        <div class="input-wrapper search d-flex">
            <div class="d-flex" style="background:white; padding:0px 0px;justify-content:space-between;border-radius:30px;">
            <input type="text" id="searchQuery"  placeholder="Cari Kelompok Keahlian" style="width:600px; padding:10px; border:none;border-radius:30px">
            <button class="search-btn" onclick="handleSearch()" style="border:none; background:transparent; color:blue; margin:10px;"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
        <label for="searchFilterSort">Urut Berdasarkan</label>
        <select id="searchFilterSort" class="form-select">
            @foreach($dataFilterSort as $sort)
                <option value="{{ $sort['Value'] }}">{{ $sort['Text'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="searchFilterStatus">Status</label>
        <select id="searchFilterStatus" class="form-select">
    @foreach($dataFilterStatus as $status)
        <option value="{{ $status['Value'] }}">{{ $status['Text'] }}</option>
    @endforeach
</select>

    </div>
</div>
<div class="mt-3">
    <button class="btn btn-primary" onclick="handleSearch()">Filter</button>
</div>

<!-- Container -->
<div class="container" style="margin-top: 50px;">
    <!-- Data Aktif -->
    <div class="mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>↓ Data Aktif</h5>
            </div>
        </div>
        <div id="dataContainerAktif" class="row mt-3">
            @forelse ($data as $item)
                @if ($item->Status === 'Aktif')
                    <div class="col-md-4 mb-4">
                        <div class="card shadow">
                            <img src="{{ $item->Gambar ? asset($item->Gambar) : asset('default.jpg') }}" 
                                 class="card-img-top" 
                                 alt="{{ $item->{'Nama Kelompok Keahlian'} }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->{'Nama Kelompok Keahlian'} }}</h5>
                                <p class="card-text">{{ Str::limit($item->Deskripsi, 100) }}</p>
                                <p class="card-text"><strong>Status:</strong> {{ $item->Status }}</p>
                                <button class="btn btn-secondary btn-sm" onclick="toggleStatus('{{ $item->Key }}', '{{ $item->Status }}', '{{ $item->{'Kode Karyawan'} }}')">
                                    Nonaktifkan
                                </button>
                                <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $item->Key }}')">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="editData('{{ $item->Key }}')">
                                    <i class="fas fa-edit"></i> Edit
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
    
    <!-- Data Draft -->
    <div class="mb-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5>↓ Data Draft</h5>
            </div>
        </div>
        <div id="dataContainerDraft" class="row mt-3">
            @forelse ($data as $item)
                @if ($item->Status === 'Draft')
                    <div class="col-md-4 mb-4">
                        <div class="card shadow">
                            <img src="{{ $item->Gambar ? asset($item->Gambar) : asset('default.jpg') }}" 
                                 class="card-img-top" 
                                 alt="{{ $item->{'Nama Kelompok Keahlian'} }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->{'Nama Kelompok Keahlian'} }}</h5>
                                <p class="card-text">{{ Str::limit($item->Deskripsi, 100) }}</p>
                                <p class="card-text"><strong>Status:</strong> {{ $item->Status }}</p>
                                <button class="btn btn-warning btn-sm" onclick="toggleStatus('{{ $item->Key }}', '{{ $item->Status }}', '{{ $item->{'Kode Karyawan'} }}')">
                                    Kirim
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteData('{{ $item->Key }}')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="editData('{{ $item->Key }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $item->Key }}')">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center">Tidak ada data draft!</p>
            @endforelse
        </div>
    </div>
    <!-- Data Menunggu -->
<div class="mb-4">
    <div class="card bg-warning text-white">
        <div class="card-body">
            <h5>↓ Data Menunggu</h5>
        </div>
    </div>
    <div id="dataContainerMenunggu" class="row mt-3">
        @forelse ($data as $item)
            @if ($item->Status === 'Menunggu')
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="{{ $item->Gambar ? asset($item->Gambar) : asset('default.jpg') }}" 
                             class="card-img-top" 
                             alt="{{ $item->{'Nama Kelompok Keahlian'} }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->{'Nama Kelompok Keahlian'} }}</h5>
                            <p class="card-text">{{ Str::limit($item->Deskripsi, 100) }}</p>
                            <p class="card-text"><strong>Status:</strong> {{ $item->Status }}</p>
                            <!-- <button class="btn btn-primary btn-sm" onclick="toggleStatus('{{ $item->Key }}', '{{ $item->Status }}', '{{ $item->{'Kode Karyawan'} }}')">
                                Aktifkan
                            </button> -->
                            <button class="btn btn-primary btn-sm" onclick="editData('{{ $item->Key }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $item->Key }}')">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center">Tidak ada data menunggu!</p>
        @endforelse
    </div>
</div>

<!-- Data Tidak Aktif -->
<div class="mb-4">
    <div class="card bg-danger text-white">
        <div class="card-body">
            <h5>↓ Data Tidak Aktif</h5>
        </div>
    </div>
    <div id="dataContainerTidakAktif" class="row mt-3">
        @forelse ($data as $item)
            @if ($item->Status === 'Tidak Aktif')
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="{{ $item->Gambar ? asset($item->Gambar) : asset('default.jpg') }}" 
                             class="card-img-top" 
                             alt="{{ $item->{'Nama Kelompok Keahlian'} }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->{'Nama Kelompok Keahlian'} }}</h5>
                            <p class="card-text">{{ Str::limit($item->Deskripsi, 100) }}</p>
                            <p class="card-text"><strong>Status:</strong> {{ $item->Status }}</p>
                            <button class="btn btn-success btn-sm" onclick="toggleStatus('{{ $item->Key }}', '{{ $item->Status }}', '{{ $item->{'Kode Karyawan'} }}')">
                                Aktifkan
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="editData('{{ $item->Key }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $item->Key }}')">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>

                        </div>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center">Tidak ada data tidak aktif!</p>
        @endforelse
    </div>
</div>

</div>


<script>

var data = @json($data);
        const container = document.getElementById('dataContainer');
        container.innerHTML = '';
        console.log("data kk", data);
        if (data.length === 0) {
            container.innerHTML = '<p class="text-center">Tidak ada data!</p>';
        } else {
            data.forEach(item => {
    let statusSpecificButtons = '';

    // Tombol sesuai dengan status
    if (item.Status === 'Draft') {
        statusSpecificButtons = `
            <button class="btn btn-warning mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item['Kode Karyawan']}')">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
            <button class="btn btn-danger mt-2" onclick="deleteData('${item.Key}')">
                <i class="fas fa-trash-alt"></i> Hapus
            </button>
        `;
    } else if (item.Status === 'Aktif') {
        statusSpecificButtons = `
            <button class="btn btn-secondary mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item['Kode Karyawan']}')">
                <i class="fas fa-toggle-off"></i> Nonaktifkan
            </button>
        `;
    } else if (item.Status === 'Tidak Aktif') {
        statusSpecificButtons = `
            <button class="btn btn-success mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item['Kode Karyawan']}')">
                <i class="fas fa-toggle-on"></i> Aktifkan
            </button>
        `;
    }

    // Tambahkan tombol Lihat dan Edit untuk semua status
    statusSpecificButtons += `
        <button class="btn btn-info mt-2" onclick="viewDetail('${item.Key}')">
            <i class="fas fa-eye"></i> Lihat
        </button>
        <button class="btn btn-primary mt-2" onclick="editData('${item.Key}')">
            <i class="fas fa-edit"></i> Edit
        </button>
    `;

    // Render setiap item
    container.innerHTML += `
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="${item.Gambar ? '/' + item.Gambar : ''}" class="card-img-top" alt="${item['Nama Kelompok Keahlian']}">
                <div class="card-body">
                    <h5 class="card-title">${item['Nama Kelompok Keahlian']}</h5>
                    <p class="card-text">${item.Deskripsi}</p>
                    <p class="card-text"><strong>Status:</strong> ${item.Status}</p>
                    <div class="mt-3">
                        ${statusSpecificButtons}
                    </div>
                </div>
            </div>
        </div>
    `;
});
        }

            function viewDetail(id) {
            const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
            console.log("Navigating to detail page for ID:", id);
            window.location.href = `/kelola_kk/${role}/detail/${id}`;
        }
        
        function toggleStatus(id, currentStatus, personInCharge) {
    console.log("Toggling status:", id, currentStatus, personInCharge);

    let newStatus;

    // Validasi personInCharge untuk memastikan null jika kosong
    if (!personInCharge || personInCharge === "null" || personInCharge === undefined) {
        personInCharge = null; // Set PIC menjadi null
    }

    // Tentukan status baru berdasarkan kondisi
    if (currentStatus === "Draft") {
        if (personInCharge === null) {
            newStatus = "Menunggu"; // Jika PIC kosong, ubah ke Menunggu
        } else {
            newStatus = "Aktif"; // Jika PIC ada, ubah ke Aktif
        }
    } else if (currentStatus === "Aktif") {
        newStatus = "Tidak Aktif"; // Jika Aktif, ubah ke Tidak Aktif
    } else if (currentStatus === "Tidak Aktif") {
        newStatus = "Aktif"; // Jika Tidak Aktif, ubah ke Aktif
    }

    const payload = {
        id: id,
        newStatus: newStatus,
        personInCharge: personInCharge, // Kirim PIC (null jika kosong)
    };

    console.log("Payload to Server:", payload);

    fetch(`/kelola_kk/toggleStatus`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content"),
        },
        body: JSON.stringify(payload),
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                Swal.fire("Berhasil!", result.message, "success");
                window.location.reload();
            } else {
                Swal.fire("Gagal!", result.message, "error");
            }
        })
        .catch((error) => {
            console.error("Fetch error:", error);
            Swal.fire("Error!", "Terjadi kesalahan.", "error");
        });
}

        function handleAdd() {
            const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
            const pengguna = "{{ Cookie::get('pengguna') }}"; // Ambil pengguna dari cookie
            const name = "{{ Cookie::get('usr_id') }}";
            console.log("role:", role, "pengguna:", pengguna, "name:", name);

            if (!role || !name) {
                console.error('Role or pengguna parameter missing');
                return;
            }
            window.location.href = `/kelola_kk/${role}/tambah`;
        }

        function editData(id) {
            console.log("Navigating to edit page for ID:", id);
            const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
            const pengguna = "{{ Cookie::get('pengguna') }}"; // Ambil pengguna dari cookie
            const name = "{{ Cookie::get('usr_id') }}";
            console.log("role:", role, "pengguna:", pengguna, "name:", name);
            if (!role || !name) {
                console.error('Role or pengguna parameter missing');
                return;
            }
            window.location.href = `/kelola_kk/${role}/edit/${id}`;
        }

  async function deleteData(id) {
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
        // Kirim permintaan DELETE
        const response = await fetch(`/kelola_kk/deleteKK/${id}`, {
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
const role = "{{ Cookie::get('role') }}"; // Role diambil dari cookie
const apiLink = `/kelola_kk/${role}`;

async function fetchData(params = {}) {
    const role = "{{ Cookie::get('role') }}";
    const apiLink = `/kelola_kk/${role}`;
    console.log(apiLink);
    console.log('Fetching data with params:', params);

    const containerAktif = document.getElementById('dataContainerAktif');
    const containerMenunggu = document.getElementById('dataContainerMenunggu');
    const containerDraft = document.getElementById('dataContainerDraft');
    const containerTidakAktif = document.getElementById('dataContainerTidakAktif');

    // Debugging elemen
    console.log('Aktif:', !!containerAktif, 'Menunggu:', !!containerMenunggu, 'Draft:', !!containerDraft, 'Tidak Aktif:', !!containerTidakAktif);

    if (!containerAktif || !containerMenunggu || !containerDraft || !containerTidakAktif) {
        console.error('Salah satu elemen container tidak ditemukan.');
        return;
    }

    containerAktif.innerHTML = '<p>Loading...</p>';
    containerMenunggu.innerHTML = '<p>Loading...</p>';
    containerDraft.innerHTML = '<p>Loading...</p>';
    containerTidakAktif.innerHTML = '<p>Loading...</p>';

    try {
        const response = await fetch(apiLink + '?' + new URLSearchParams(params), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Opsional, untuk membantu deteksi AJAX
            }
        });

        if (!response.ok) throw new Error('Data tidak ditemukan.');

        const data = await response.json();
        console.log('Data yang diterima:', data);

        renderData(data);
    } catch (error) {
        console.error('Error saat mengambil data:', error.message);
        containerAktif.innerHTML = '<p>Error memuat data Aktif.</p>';
        containerMenunggu.innerHTML = '<p>Error memuat data Menunggu.</p>';
        containerDraft.innerHTML = '<p>Error memuat data Draft.</p>';
        containerTidakAktif.innerHTML = '<p>Error memuat data Tidak Aktif.</p>';
    }
}


function handleSearch() {
    const query = document.getElementById('searchQuery').value.toLowerCase();
    const sort = document.getElementById('searchFilterSort').value;
    const status = document.getElementById('searchFilterStatus').value;
    fetchData({ query, sort, status, page: 1 });
}

function renderData(data) {
    const containerAktif = document.getElementById('dataContainerAktif');
    const containerMenunggu = document.getElementById('dataContainerMenunggu');
    const containerDraft = document.getElementById('dataContainerDraft');
    const containerTidakAktif = document.getElementById('dataContainerTidakAktif');

    // Validasi: Pastikan elemen ada
    if (!containerAktif || !containerMenunggu || !containerDraft || !containerTidakAktif) {
        console.error('Salah satu elemen container tidak ditemukan.');
        return;
    }

    // Kosongkan masing-masing kontainer status
    containerAktif.innerHTML = '';
    containerMenunggu.innerHTML = '';
    containerDraft.innerHTML = '';
    containerTidakAktif.innerHTML = '';

    // Pisahkan data berdasarkan status
    const dataAktif = data.filter(item => item.Status === 'Aktif');
    const dataMenunggu = data.filter(item => item.Status === 'Menunggu');
    const dataDraft = data.filter(item => item.Status === 'Draft');
    const dataTidakAktif = data.filter(item => item.Status === 'Tidak Aktif');

    // Render data untuk masing-masing status
    appendDataToContainer(containerAktif, dataAktif);
    appendDataToContainer(containerMenunggu, dataMenunggu);
    appendDataToContainer(containerDraft, dataDraft);
    appendDataToContainer(containerTidakAktif, dataTidakAktif);
}

function appendDataToContainer(container, items) {
    if (items.length === 0) {
        container.innerHTML = '<p>Data tidak ditemukan.</p>';
        return;
    }

    items.forEach(item => {
        container.innerHTML += `
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="${item.Gambar ? '/' + item.Gambar : '/images/default.jpg'}" class="card-img-top" alt="${item['Nama Kelompok Keahlian']}">
                    <div class="card-body">
                        <h5 class="card-title">${item['Nama Kelompok Keahlian']}</h5>
                        <p class="card-text">${item.Deskripsi}</p>
                        <p class="card-text"><strong>Status:</strong> ${item.Status}</p>
                    </div>
                </div>
            </div>
        `;
    });
}



</script>


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@include('backbone.footer')