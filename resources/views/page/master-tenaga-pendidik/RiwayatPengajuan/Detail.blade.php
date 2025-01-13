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

<div class="d-flex justify-content-between mt-5 mx-5">
    <div class="back-and-title d-flex" style="margin-top: 60px; margin-left: 50px;">
        <button style="background-color:transparent; border:none" onclick="BackPage()">
            <img src="{{ asset('assets/backPage.png') }}" alt="" />
        </button>
        <h4 class="text-primary font-weight-bold" style="font-size: 30px; margin-top: 10px; margin-left: 20px;">
            Pengajuan Kelompok Keahlian
        </h4>
    </div>
    <div class="ket-draft" style="margin-top: 80px;  margin-right: 50px;">
        <p class="mb-0 font-weight-bold" style="margin-top: 10px;">
            <i class="fas fa-circle text-warning mr-3" style="width: 10px;"></i>
            Menunggu Persetujuan Prodi
        </p>
    </div>
</div>



<div style="display: flex; justify-content: space-between; margin-top: 10px; margin-left: 100px; margin-right: 100px;">
    <div class="row">


        <div class="col-12">
            <form>


                <div class="card mt-4">
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
                                    <label class="form-label fw-bold">Jabatan</label><br>
                                    <span style="white-space: pre-wrap;">{{ Cookie::get('role') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 my-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kelompok Keahlian</label><br>
                                    <span style="white-space: pre-wrap;">{{ $detailData->kke_nama }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 my-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label><br>
                                    <span style="white-space: pre-wrap;">{{ $detailData->akk_status }}</span>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <h5>Lampiran Pendukung</h5>
                                <div class="card">
                                    @if (!empty($detailData->Lampiran))
                                        @foreach ($detailData->Lampiran as $index => $lampiran)
                                            <div class="card-body p-4">
                                                <div>
                                                    <div>
                                                        <h5 class="mb-3" style="margin-top: 15px;">Lampiran
                                                            {{ $index + 1 }}</h5>
                                                        <a href="/{{ $lampiran }}" target="_blank"
                                                            rel="noopener noreferrer"
                                                            style="padding: 5px; margin-top: 20px; text-decoration: none; border-radius: 10px; color: white; background-color: rgb(10, 94, 168);">
                                                            {{ $lampiran }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="card-body p-4">
                                            <p>Tidak ada lampiran.</p>
                                        </div>
                                    @endif
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
    console.log("Data dari backend:", @json($detailData));
</script>

<script>
    function BackPage() {
        window.location.href = "{{ route('riwayat_pengajuan') }}";
    }
</script>

@include('backbone.footer')
