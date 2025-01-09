<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Anggota Kelompok Keahlian - P-KNOW</title>
</head>

@include('backbone.headerpicpknow', [
    'showMenu' => true,
    'userProfile' => ['name' => 'User', 'role' => 'PIC P-KNOW', 'lastLogin' => 'Terakhir Masuk: ' . now()],
])

<div class="backSearch">
    <h1>Kelola Kelompok Keahlian</h1>
    <p>ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian yang biasa disebut dengan Kelompok Keahlian.</p>
    <div class="input-wrapper search d-flex">
        <div class="d-flex" style="background:white; padding:0px 0px;justify-content:space-between;border-radius:30px;">
            <input 
                type="text" 
                id="searchQuery" 
                placeholder="Cari Kelompok Keahlian" 
                value="{{ request('query') }}" 
                style="width:600px; padding:10px; border:none; border-radius:30px">
            <button 
                class="search-btn" 
                onclick="handleSearch()" 
                style="border:none; background:transparent; color:blue; margin:10px;">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h3 class="text-primary mb-3">Kelompok Keahlian</h3>
    <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>↓ Data Aktif</h5>
            </div>
        </div>
    <div class="row mt-3">
        @forelse ($data as $item)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $item->{'Gambar'} ? asset($item->{'Gambar'}) : asset('default.jpg') }}" 
                         class="card-img-top" 
                         alt="{{ $item->{'Nama Kelompok Keahlian'} }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->{'Nama Kelompok Keahlian'} }}</h5>
                        <p class="card-text"><i class="fas fa-user"></i> PIC: {{ $item->{'PIC'} ?? 'Belum Ditentukan' }}</p>
                        <p class="card-text">
                            {{ Str::limit($item->{'Deskripsi'}, 100) }} <!-- Batas karakter deskripsi -->
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="kelolaAnggota('{{ $item->{'Key'} }}')">
                            <i class="fas fa-users"></i> Kelola Anggota
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">Tidak ada data.</p>
        @endforelse
    </div>
</div>



@include('backbone.footer')

<script>
function handleSearch() {
    const query = document.getElementById('searchQuery').value.trim();
    const url = query ? `?query=${encodeURIComponent(query)}` : `?query=`;
    window.location.href = url;
}
function kelolaAnggota(id) {
    const role = "{{ urlencode(Cookie::get('role')) }}"; // Pastikan role diambil dengan benar dan di-encode
    const url = `/kelola_akk/${role}/anggota/${id}`;
    console.log(url); // Debug untuk melihat URL yang dihasilkan
    window.location.href = url;
}

</script>
