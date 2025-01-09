<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Persetujuan Anggota Keahlian</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@include('backbone.headerpicpknow', [
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
            <h4 class="mb-4">Detail Kelompok Keahlian</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Data Kelompok Keahlian -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ asset($data->kke_gambar) }}" alt="Gambar Kelompok Keahlian" width="500">
                    <p class="card-text"><strong>{{ $data->kke_nama }}</strong></p>
                    <p class="card-text"><strong>{{ $data->pro_nama }}</strong></p>
                    <p class="card-text"><strong>Tentang Kelompok Keahlian</strong></p>
                    <p>{{ $data->kke_deskripsi }}</p>
                    <label class="form-label"><strong>PIC:</strong> {{ $data->pic_nama }}</label><br>
                    <div class="list-group mb-4">
                @foreach($anggotaAktif as $anggota)
                    <div class="list-group-item">
                        <strong>{{ $anggota->nama }}</strong> ({{ $data->pro_nama }})
                    </div>
                @endforeach
            </div>
                </div>
            </div>
        </div>

        <!-- Daftar Anggota -->
        <div class="col-12">
        
        <h5 class="mb-3">Menunggu Persetujuan</h5>
<div class="list-group mb-4">
    @foreach($anggotaMenungguAcc as $anggota)
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>{{ $anggota->nama }}</strong> ({{ $data->pro_nama }})</span>
            <div>
            <button type="button" class="btn btn-info btn-sm" onclick="lihatLampiran('{{ $anggota->akk_id }}', '{{ $anggota->nama }}', '{{ $data->pro_nama }}')">
    Detail
</button>
 
                <!-- Tombol Setujui -->
                <form action="{{ route('persetujuan.updateStatus') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="akk_id" value="{{ $anggota->akk_id }}">
                    <input type="hidden" name="new_status" value="Aktif">
                    <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                </form>

                <!-- Tombol Tolak -->
                <form action="{{ route('persetujuan.updateStatus') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="akk_id" value="{{ $anggota->akk_id }}">
                    <input type="hidden" name="new_status" value="Ditolak">
                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
<div class="card mb-3">
    <div class="col-lg-20">
        <h3 class="col-6 mb-3 mt-3 fw-bold" style="color: rgb(10, 94, 168); font-size: 25px;">
            Detail pengajuan dan lampiran pendukung
        </h3>
        <div>
            <div class="col-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama</label><br>
                    <span id="lampiran-nama" style="white-space: pre-wrap;">-</span>
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Program Studi</label><br>
                    <span id="lampiran-prodi" style="white-space: pre-wrap;">-</span>
                </div>
            </div>
            <div class="mt-2 col-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Lampiran Pendukung</label><br>
                    <div id="lampiran-pengajuan" style="white-space: pre-wrap;">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<script>
    function lihatLampiran(akk_id, nama, prodi) {
    // Kosongkan konten sebelumnya
    document.getElementById('lampiran-nama').textContent = '-';
    document.getElementById('lampiran-prodi').textContent = '-';
    document.getElementById('lampiran-pengajuan').textContent = '-';

    // Tampilkan nama dan prodi
    document.getElementById('lampiran-nama').textContent = nama;
    document.getElementById('lampiran-prodi').textContent = prodi;

    // Kirim permintaan untuk mengambil lampiran
    fetch('{{ route("persetujuan.getDetailLampiran") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ akk_id: akk_id, page: 1, sort: '[ID Lampiran] ASC' }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const lampiranHTML = data.data.map(item => {
                    return `<a href="/${item.Lampiran}" target="_blank">${item.Lampiran}</a>`;
                }).join('<br>');
                document.getElementById('lampiran-pengajuan').innerHTML = lampiranHTML;
            } else {
                document.getElementById('lampiran-pengajuan').textContent = 'Tidak ada lampiran tersedia.';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('lampiran-pengajuan').textContent = 'Terjadi kesalahan saat memuat lampiran.';
        });
}

</script>

@include('backbone.footer')
