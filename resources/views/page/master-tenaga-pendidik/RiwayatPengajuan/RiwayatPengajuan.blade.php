<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Pengajuan Anggota Keahlian</title>
</head>
@include('backbone.headertenagapendidik', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
])

<div class="app-container">
    <div class="backSearch">
        <h1>Riwayat Anggota Keahlian</h1>
        <p>Riwayat Pengajuan akan menampilkan pengajuan anggota keahlia yang anda ajukan, hanya terdapat satu kelompok keahlian yang pengajuannya akan diterima oleh Program Studi.</p>
        <div class="input-wrapper">
            <button style="border: none; background: transparent;">
                <i class="fas fa-search search-icon"></i>
            </button>
            <input type="text" class="search" placeholder="Cari?" />
        </div>
    </div>
    <div class="navigasi-layout-page">
    <p class="title-kk">Kelompok Keahlian</p>
    <div class="left-feature">
        <div class="status">
            <table>
                <tbody>
                    <tr>
                        <td><i class="fas fa-circle" style="color: grey;"></i></td>
                        <td><p>Dibatalkan</p></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-circle" style="color: rgb(220, 53, 69);"></i></td>
                        <td><p>Ditolak</p></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tes" style="display: flex;">
            <div>
                <button type="button" 
                        class="btn btn- px-3 custom-add py-2 add-button fw-semibold rounded-4" 
                        
                        title="Saring atau Urutkan Data" 
                        style="background-color: white; color: black; box-shadow: rgba(0, 0, 0, 0.224) 0px 0px 10px;">
                    <i class="fi fi-br-apps-sort pe-2" style="margin-top: 5px;"></i>Filter
                </button>
                
            </div>
        </div>
    </div>
</div>



</div>

<!-- Data Table -->
<div class="card-body ml-5 mr-5 ">
    <!-- Pagination -->
    <div id="pagination" class="d-flex justify-content-center mt-4"></div>


    <div id="datacontainerKKLainnya" class="row" ></div>

</div>
</div>

<script>
    // Data kelompok keahlian diambil dari PHP
    var data = @json($data);
    console.log("chio", data);

    const containerKKLainnya = document.getElementById('datacontainerKKLainnya');



    containerKKLainnya.innerHTML = '';
    // Filter data untuk hanya menampilkan status Ditolak dan Dibatalkan
    const filteredData = data.filter(item => item.Status === 'Ditolak' || item.Status === 'Dibatalkan');

    if (filteredData.length === 0) {
        containerKKLainnya.innerHTML =
            '<p class="text-center">Tidak ada data dengan status Ditolak atau Dibatalkan!</p>';
    } else {
        filteredData.forEach(item => {
            let cardTemplate = `
            <div class="col-md-4 mb-4" style="margin-top: -40px;">
                <div class="card">
                    <img src="${item.Gambar ? '/' + item.Gambar : ''}" class="card-img-top" alt="${item['Nama Kelompok Keahlian']}">
                    <div class="card-body">
                        <h3 class="mb-3 fw-semibold" style="color: rgb(10, 94, 168);">${item['Nama Kelompok Keahlian']}</h3>
                        <h5 class="fw-semibold">
                            Status: ${item.Status}
                        </h5>
                        <h5 class="fw-semibold">
                            <i class="fas fa-graduation-cap" style="font-size: 20px; color: black;"></i>
                            ${item.Prodi}
                        </h5>
                        <p class="card-text">${item.Deskripsi}</p>
        `;

            // Tambahkan tombol Riwayat untuk Ditolak atau Dibatalkan
            cardTemplate += `
            <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary" onclick="detail('${item.Key}')"><i class="fas fa-history"></i> Riwayat</button>
                    </div>
           
        `;

            cardTemplate += `</div></div></div>`;

            // Masukkan ke dalam container
            containerKKLainnya.innerHTML += cardTemplate;
        });
    }
</script>


<script>
    function detail(akk_id) {
        const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
        console.log("Navigating to detail page for ID:", akk_id);
        window.location.href = `/riwayat_pengajuan/${role}/detail/${akk_id}`;
    }

    function toggleStatus(id, currentStatus, personInCharge) {
        const newStatus = currentStatus === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
        const confirmationMessage =
            `Apakah Anda yakin ingin ${newStatus === 'Aktif' ? 'mengaktifkan' : 'menonaktifkan'} kelompok keahlian ini?`;

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            id,
                            newStatus,
                            personInCharge
                        })
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
    const apiLink = "{{ route('riwayat_pengajuan') }}";
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
            document.getElementById('dataContainer').innerHTML = < p class = "text-center" > $ {
                error.message
            } < /p>;
        }
    }


    function handleSearch() {
        const query = document.getElementById('searchQuery').value;
        const sort = document.getElementById('searchFilterSort').value;
        const status = document.getElementById('searchFilterStatus').value;
        fetchData({
            query,
            sort,
            status,
            page: 1
        });
    }


    fetchData();
</script>


@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@include('backbone.footer')
