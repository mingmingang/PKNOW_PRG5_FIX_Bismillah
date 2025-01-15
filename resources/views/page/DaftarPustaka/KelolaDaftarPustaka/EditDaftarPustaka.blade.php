<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Tambah Daftar Pustaka</title>
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

<div class="container mt-5">
    <h4 class="text-primary font-weight-bold mb-4">Edit Daftar Pustaka</h4>

    <!-- Display success or error messages -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('daftar_pustaka.update', $data->pus_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h1 style="color : rgba(10, 94, 168, 1)">Edit Pustaka</h1>
        @method('PUT')
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <!-- Judul -->
                    <div class="col-md-6 form-group">
                        <label for="pus_judul" class="form-label">Judul Pustaka</label>
                        <input type="text" name="pus_judul" id="pus_judul" class="form-control" value="{{ $data->pus_judul }}" required>
                    </div>

                    <!-- Kelompok Keahlian -->
                    <div class="col-md-6 form-group">
                        <label for="kke_id" class="form-label">Kelompok Keahlian</label>
                        <select name="kke_id" id="kke_id" class="form-control" required>
                            @foreach($kelompokKeahlian as $kk)
                            <option value="{{ $kk->Key }}" {{ $data->kke_id == $kk->Key ? 'selected' : '' }}>
                                {{ $kk->{'Nama Kelompok Keahlian'} }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kata Kunci -->
                    <div class="col-md-12 form-group mt-3">
                        <label for="pus_kata_kunci" class="form-label">Kata Kunci</label>
                        <input type="text" name="pus_kata_kunci" id="pus_kata_kunci" class="form-control" value="{{ $data->pus_kata_kunci }}" required>
                    </div>

                    <!-- Gambar -->
                    <div class="col-md-6 form-group mt-3">
                        <label for="pus_gambar" class="form-label">Gambar</label>
                        <input type="file" name="pus_gambar" id="pus_gambar" class="form-control" onchange="previewImage(event)">
                        <div class="mt-2">
                            <!-- Jika data gambar ada, tampilkan -->
                            <img id="imagePreview"
                                src="{{ $data->pus_gambar ? asset($data->pus_gambar) : asset('images/NoImage.png') }}"
                                class="img-thumbnail"
                                style="max-width: 200px;">
                        </div>
                    </div>


                    <!-- File -->
                    <div class="col-md-6 form-group mt-3">
                        <label for="pus_file" class="form-label">File</label>
                        <input type="file" name="pus_file" id="pus_file" class="form-control">
                        <p class="mt-2">
                            <a href="{{ $data->pus_file ? asset($data->pus_file) : '#' }}" target="_blank">
                                {{ $data->pus_file ? 'Lihat File Sebelumnya' : 'Tidak Ada File' }}
                            </a>
                        </p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-md-12 form-group mt-3">
                        <label for="pus_keterangan" class="form-label">Deskripsi</label>
                        <textarea name="pus_keterangan" id="pus_keterangan" rows="4" class="form-control" required>{{ $data->pus_keterangan }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end mt-4 mt-3 mb-3">
            <button type="button" class="btn btn-secondary me-2 " onclick="window.history.back()">Batalkan</button>
            <button type="submit" class="btn btn-primary ml-3">Edit</button>
        </div>
    </form>
</div>


<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('imagePreview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }//
</script>

@include('backbone.footer')