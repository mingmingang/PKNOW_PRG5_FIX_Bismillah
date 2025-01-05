<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

<div class="container" style="margin-top: 100px;">
    <h1>Kelola Kelompok Keahlian</h1>
    <p>ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian.</p>

    <!-- Search and Filters -->
    <div class="input-wrapper mb-4">
        <form id="searchForm" class="d-flex">
            <input
                type="text"
                id="searchQuery"
                class="form-control"
                placeholder="Cari"
                style="border-radius: 20px; height: 40px; border: none; width: 50%;"
            >
            <select id="searchFilterSort" class="form-select mx-2">
                @foreach($dataFilterSort as $filter)
                    <option value="{{ $filter['Value'] }}">{{ $filter['Text'] }}</option>
                @endforeach
            </select>
            <select id="searchFilterStatus" class="form-select mx-2">
                @foreach($dataFilterStatus as $filter)
                    <option value="{{ $filter['Value'] }}">{{ $filter['Text'] }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-primary px-4" onclick="handleSearch()">Cari</button>
        </form>
    </div>

    <!-- Button Tambah -->
    <div class="text-end mb-4">
        <button class="btn btn-success" onclick="handleAdd()">Tambah</button>
    </div>
 <!-- Data Table -->
 <div id="dataContainer" class="row"></div>

<!-- Pagination -->
<div id="pagination" class="d-flex justify-content-center mt-4"></div>
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

            if (item.Status === 'Draft') {
                statusSpecificButtons = `
                    <button class="btn btn-warning mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item.PersonInCharge}')">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                    <button class="btn btn-danger mt-2" onclick="deleteData('${item.Key}')">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                `;
            } else if (item.Status === 'Aktif') {
                statusSpecificButtons = `
                    <button class="btn btn-secondary mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item.PersonInCharge}')">
                        <i class="fas fa-toggle-off"></i> Nonaktifkan
                    </button>
                `;
            } else if (item.Status === 'Tidak Aktif') {
                statusSpecificButtons = `
                    <button class="btn btn-success mt-2" onclick="toggleStatus('${item.Key}', '${item.Status}', '${item.PersonInCharge}')">
                        <i class="fas fa-toggle-on"></i> Aktifkan
                    </button>
                `;
            }

            container.innerHTML += `
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="${item.Gambar ? '/' + item.Gambar : ''}" class="card-img-top" alt="${item['Nama Kelompok Keahlian']}">
                        <div class="card-body">
                            <h5 class="card-title">${item['Nama Kelompok Keahlian']}</h5>
                            <p class="card-text">${item.Deskripsi}</p>
                            <p class="card-text"><strong>Status:</strong> ${item.Status}</p>
                            <div class="d-flex justify-content-between">
                                <!-- Permanent Buttons -->
                                <button class="btn btn-primary" onclick="editData('${item.Key}')"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-info" onclick="viewDetail('${item.Key}')"><i class="fas fa-eye"></i> Lihat</button>
                            </div>
                            <!-- Status-Specific Buttons -->
                            <div class="mt-3">
                                ${statusSpecificButtons}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        }


</script>

<script>
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
            // const urlParams = new URLSearchParams(window.location.search);
            // const role = urlParams.get('role');
            // const name = urlParams.get('pengguna');

            const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
            const pengguna = "{{ Cookie::get('pengguna') }}"; // Ambil pengguna dari cookie
            const name = "{{ Cookie::get('usr_id') }}";
            console.log("role:", role, "pengguna:", pengguna, "name:", name);
            if (!role || !name) {
                console.error('Role or pengguna parameter missing');
                return;
            }
            window.location.href = `/kelola_kk/${role}/edit/${id}`;
            // window.location.href = `/kelola_kk/${role}/edit/${id}?role=${encodeURIComponent(role)}&pengguna=${encodeURIComponent(name)}`;
        }

        function viewDetail(id) {
            const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
            console.log("Navigating to detail page for ID:", id);
            window.location.href = `/kelola_kk/${role}/detail/${id}`;
        }

        function toggleStatus(id, currentStatus, personInCharge) {
        const newStatus = currentStatus === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
        const confirmationMessage = `Apakah Anda yakin ingin ${newStatus === 'Aktif' ? 'mengaktifkan' : 'menonaktifkan'} kelompok keahlian ini?`;

        Swal.fire({
            title: 'Konfirmasi',
            text: confirmationMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(async result => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/kelola_kk/toggleStatus`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id, newStatus, personInCharge })
                    });

                    const result = await response.json();
                    if (result.success) {
                        Swal.fire('Berhasil!', result.message, 'success');
                        window.location.reload();
                    } else {
                        Swal.fire('Gagal!', result.message, 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    }


</script>


<script>
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


</script>
    
<script>
    
const apiLink = "{{ route('kelola_kk.getTempDataKK') }}";
console.log(apiLink);
var data = @json($data);
console.log("ayamansdf");


async function fetchData(params = {}) {
    console.log('Fetching data with params:', params);
    try {
        const response = await fetch(apiLink + '?' + new URLSearchParams(params));
        if (!response.ok) {
            throw new Error('Data tidak ditemukan');
        }
        const data = await response.json();
        console.log('Response data:', data);
        renderData(data);
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('dataContainer').innerHTML = <p class="text-center">${error.message}</p>;
    }
}


    function handleSearch() {
        const query = document.getElementById('searchQuery').value;
        const sort = document.getElementById('searchFilterSort').value;
        const status = document.getElementById('searchFilterStatus').value;
        fetchData({ query, sort, status, page: 1 });
    }


    fetchData();
    
</script>


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@include('backbone.footer')