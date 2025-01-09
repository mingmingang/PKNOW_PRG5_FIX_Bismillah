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

<div class="container" style="margin-top: 100px;">
<h1>Kelola Anggota - {{ $kelompok->kke_nama ?? 'Kelompok Keahlian' }}</h1>
<p><strong>Prodi:</strong> {{ $kelompok->Prodi ?? '-' }}</p>
<p><strong>PIC:</strong> {{ $kelompok->PIC ?? 'Tidak Ditemukan' }}</p>
<p><strong>Deskripsi:</strong> {{ $kelompok->kke_deskripsi ?? '-' }}</p>
<img src="{{ $kelompok->kke_gambar ? asset($kelompok->kke_gambar) : asset('default-cover.jpg') }}" alt="Gambar Cover" class="img-fluid">


<div class="row mt-4">
    <!-- Daftar Anggota -->
    <div class="col-md-7">
        <h3>Daftar Anggota</h3>
        <form method="GET" action="{{ route('kelola_anggota', ['role' => $role, 'id' => $kke_id]) }}">
    <div class="input-group mb-3">
        <input type="text" name="search" class="form-control" placeholder="Cari anggota..." value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Cari</button>
    </div>
</form>
<div class="list-group">
    @if(empty($anggota))
        <div class="text-center">
            <p>Belum ada anggota untuk kelompok keahlian ini.</p>
        </div>
    @else
        @foreach ($anggota as $item)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $item->{'Nama Anggota'} ?? 'Nama tidak tersedia' }}</h5>
                    <p class="mb-1">{{ $item->Prodi ?? 'Prodi tidak tersedia' }}</p>
                </div>
                @if(isset($item->{'Nama Anggota'}) && $item->{'Nama Anggota'})
                    <form method="POST" action="{{ route('kelola_anggota.delete', $item->Key ?? '') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="kke_id" value="{{ $kke_id }}">
                        <input type="hidden" name="role" value="{{ $role }}">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                @endif
            </div>
        @endforeach
    @endif
</div>

    </div>

    <!-- Tambah Anggota -->
    <div class="col-md-5">
        <h3>Tambah Anggota</h3>
        <form method="GET" action="{{ route('kelola_anggota', ['role' => $role, 'id' => $kke_id]) }}">
    <div class="mb-3">
        <select name="prodi" class="form-select">
            <option value="0">Semua Prodi</option>
            @foreach ($prodiList as $prodi)
            <option value="{{ $prodi['Value'] }}" 
        {{ request('prodi') == $prodi['Value'] ? 'selected' : '' }}>
    {{ $prodi['Text'] }}
</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Filter</button>
</form>
<script>
    console.log("Data Prodi List:", @json($prodiList));
    console.log("Data Dosen:", @json($dosen));
</script>


        <div class="list-group mt-3">
            @forelse ($dosen as $dos)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $dos->{'Nama Karyawan'} ?? 'Nama tidak tersedia' }}</h5>
                        <p class="mb-1">{{ $dos->Prodi ?? 'Prodi tidak tersedia' }}</p>
                    </div>
                    <form method="POST" action="{{ route('kelola_anggota.add') }}">
                        @csrf
                        <input type="hidden" name="kke_id" value="{{ $kke_id }}">
                        <input type="hidden" name="role" value="{{ $role }}">
                        <input type="hidden" name="kry_id" value="{{ $dos->Key }}">
                        <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                    </form>
                </div>
            @empty
                <p class="text-center">Tidak ada dosen tersedia.</p>
            @endforelse
        </div>
    </div>
</div>



<script>
    const kkeId = "{{ $kke_id ?? '' }}";

    function deleteAnggota(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus anggota ini?')) return;

        $.ajax({
            url: `/kelola_akk/delete_anggota/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                alert('Anggota berhasil dihapus.');
                location.reload();
            },
            error: function () {
                alert('Gagal menghapus anggota.');
            }
        });
    }

    function addAnggota(id) {
        if (!kkeId) {
            alert('ID Kelompok Keahlian tidak ditemukan.');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menambahkan anggota ini?')) return;

        $.ajax({
            url: '/kelola_akk/add_anggota',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                kke_id: kkeId,
                kry_id: id
            },
            success: function () {
                alert('Anggota berhasil ditambahkan.');
                location.reload();
            },
            error: function () {
                alert('Gagal menambahkan anggota.');
            }
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif


@include('backbone.footer')


