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
        <h1>Pengajuan Anggota Keahlian</h1>
        <p>ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian yang biasa disebut dengan Kelompok Keahlian</p>
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
        <div id="dataContainer" class="row"></div>
    </div>

 
</div>

    <!-- Data Table -->
<div class="card-body ml-5 mr-5 ">
    <!-- Pagination -->
    <div id="pagination" class="d-flex justify-content-center mt-4"></div>

    <div class="card-keterangan" style="background: #0E6EFE; border-radius: 5px; padding: 10px 20px; width: 40%; margin-bottom: 20px; color: white; font-weight: bold;">
        ↓ Terdaftar sebagai anggota keahlian
    </div>
    <div id="datacontainerTerdaftar" class="row"></div>

    <div class="card-keterangan" style="background:rgb(254, 182, 14); border-radius: 5px; padding: 10px 20px; width: 40%; margin-bottom: 20px; color: white; font-weight: bold;">
        ↓ Menunggu Persetujuan Prodi
    </div>
    <div id="datacontainerTerdaftar" class="row"></div>

    <div class="card-keterangan" style="background: #A7AAAC; border-radius: 5px; padding: 10px 20px; width: 40%; margin-bottom: 20px; color: white; font-weight: bold;">
        ↓ Kelompok Keahlian Lainnya
    </div>
    <div id="datacontainerKKLainnya" class="row"></div>

    </div>
    </div>

    <script>

    var data = @json($data);
            const containerTerdaftar = document.getElementById('datacontainerTerdaftar');
            const containerKKLainnya = document.getElementById('datacontainerKKLainnya');
            
            containerTerdaftar.innerHTML = '';
            containerKKLainnya.innerHTML = '';

            console.log("data kk", data);
            if (data.length === 0) {
                containerTerdaftar.innerHTML = '<p class="text-center">Tidak ada data!</p>';
                containerKKLainnya.innerHTML = '<p class="text-center">Tidak ada data!</p>';

            } else {
                data.forEach(item => {
    const cardTemplate = `
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="${item.Gambar ? '/' + item.Gambar : ''}" class="card-img-top" alt="${item['Nama Kelompok Keahlian']}">
            <div class="card-body">
                <h5 class="card-title">${item['Nama Kelompok Keahlian']}</h5>
                <p class="card-text">${item.Deskripsi}</p>
                <p class="card-text"><strong>Status:</strong> ${item.Status || 'Tidak Ada Status'}</p>
                <p class="card-text"><strong>Prodi:</strong> ${item.Prodi}</p>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary" onclick="gabung('${item.Key}')"><i class="fas fa-plus"></i> Gabung</button>
                    <button class="btn btn-primary" onclick="detailKK('${item.Key}')"><i class="fas fa-list"></i> Detail</button>
                </div>
            </div>
        </div>
    </div>
    `;
    if (item.Status === 'Aktif') {
        containerKKLainnya.innerHTML += cardTemplate;
    } else {
        containerTerdaftar.innerHTML += cardTemplate;
    }
});

        }
    </script>

    <script>
            function gabung(id) {
                const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
                console.log("Navigating to detail page for ID:", id);
                window.location.href = `/pengajuan_kk/${role}/gabung/${id}`;
            }

            function detailKK(id) {
                const role = "{{ Cookie::get('role') }}"; // Ambil role dari cookie
                console.log("Navigating to detail page for ID:", id);
                window.location.href = `/pengajuan_kk/${role}/detailKK/${id}`;
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
        
    const apiLink = "{{ route('pengajuan_KK') }}";
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