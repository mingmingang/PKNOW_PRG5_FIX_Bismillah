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
    <title>pp</title>
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
        <h1>Persetujuan Anggota Keahlian</h1>
        <p>Program Studi dapat menyetujui persetujuan pengajuan anggota keahlian yang diajukan oleh Tenaga Pendidik
            untuk menjadi anggota dalam Kelompok Keahlian. Program Studi dapat melihat lampiran pengajuan dari Tenaga
            Pendidik untuk menjadi bahan pertimbangan</p>
    </div>
    <div class="navigasi-layout-page">
        <p class="title-kk">Kelompok Keahlian</p>
        <div class="left-feature">
            <div class="status">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <i class="fas fa-circle" style="color: #4a90e2;"></i>
                            </td>
                            <td>
                                <p>Aktif/Sudah Publikasi</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i class="fas fa-circle" style="color: #FFC619;"></i>
                            </td>
                            <td>
                                <p>Menunggu Persetujuan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

 
</div>

<!-- Data Table -->
<div id="dataContainer" class="row" style="margin-left: 68px; margin-right: 68px;"></div>


<script>
    var data = @json($data);
    const container = document.getElementById('dataContainer');
    container.innerHTML = '';
    console.log("data kk", data);
    if (data.length === 0) {
        container.innerHTML = '<p class="text-center">Tidak ada data!</p>';
    } else {
        data.forEach(item => {
            container.innerHTML += `
                <div class="col-lg-4 mb-3">
                    <div class="card p-0" 
                                         style="border-radius: 10px; border-color: '#67ACE9'>
                        
<div class="card-body p-0">

  <img src="${item.Gambar ? '/' + item.Gambar : ''}" alt="${item['Nama Kelompok Keahlian']} style="width: 390px; height: 180px; object-fit: cover; margin: 10px; border-radius: 10px;"">
        
            <h5 
                class="card-title px-3 pt-2 pb-3" 
                style="color: #0A5EA8; font-weight: bold; margin-bottom: 0;"
            >
               ${item['Nama Kelompok Keahlian']}
            </h5>

            
            <div class="card-body p-3" style="margin-top: -20px;">
                <div>
                    <i class="fas fa-users btn px-0 pb-1 text-primary" title="Anggota Kelompok Keahlian"></i>
                    <span>
                     
                              <a href="#" class="fw-semibold text-dark text-decoration-none">
                            ${item.AnggotaAktif} Anggota Aktif
                        </a>
                    </span>
                </div>
                <div>
                    <i class="fas fa-clock btn px-0 pb-1 text-primary" title="Menunggu Persetujuan"></i>
                    <span>
                        <a href="#" class="fw-semibold text-dark text-decoration-none">
                            ${item.MenungguAcc} Menunggu Persetujuan
                        </a>
                    </span>
                </div>
                <p 
                    class="lh-sm mt-2" 
                >
                    ${item.Deskripsi}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                      
                    <button 
                        class="btn btn-primary btn-sm"
                        title="Lihat detail Persetujuan Anggota Keahlian"
                        onclick="detailPersetujuan('${item.Key}')"
                    >
                        <i class="fas fa-user"></i> Lihat Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

                       
                    </div>
                </div>
            `;
        });
    }
</script>

<script>
    function detailPersetujuan(id) {
        const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
        console.log("Navigating to detail page for ID:", id);
        window.location.href = `/persetujuan/${role}/detailPersetujuan/${id}`;
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
