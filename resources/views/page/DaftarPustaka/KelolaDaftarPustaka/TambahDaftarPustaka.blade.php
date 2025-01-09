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

@extends('layouts.app')

<div class="container mt-5">
    <h4 class="text-primary font-weight-bold mb-4">Tambah Daftar Pustaka</h4>

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
    <form action="{{ route('daftar_pustaka.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h1 style="color : rgba(10, 94, 168, 1)">Tambah Pustaka</h1>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <!-- Judul -->
                    <div class="col-md-6 form-group">
                        <label for="pus_judul" class="form-label">Judul Pustaka</label>
                        <input type="text" name="pus_judul" id="pus_judul" class="form-control @error('pus_judul') is-invalid @enderror" value="{{ old('pus_judul') }}" required>
                        @error('pus_judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kelompok Keahlian -->
                    <div class="col-md-6 form-group">
                        <label for="kke_id" class="form-label">Kelompok Keahlian</label>
                        <select name="kke_id" id="kke_id" class="form-control @error('kke_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelompok Keahlian --</option>
                            @foreach($kelompokKeahlian as $kk)
                            <option value="{{ $kk->Key }}">{{ $kk->{'Nama Kelompok Keahlian'} }}</option>
                            @endforeach
                        </select>
                        @error('kke_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kata Kunci  -->
                    <div class="col-md-12 form-group mt-3">
                        <label for="pus_kata_kunci" class="form-label">Kata Kunci</label>
                        <input name="pus_kata_kunci" id="pus_kata_kunci" rows="4" class="form-control @error('pus_kata_kunci') is-invalid @enderror" required>{{ old('pus_kata_kunci') }}</input>
                        @error('pus_kata_kunci')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gambar -->
                    <div class="col-md-6 form-group mt-3">
                        <label for="pus_gambar" class="form-label">Gambar (PNG, max 10MB)</label>
                        <input type="file" name="pus_gambar" id="pus_gambar" class="form-control @error('pus_gambar') is-invalid @enderror" accept="image/png" onchange="previewImage(event)">
                        @error('pus_gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <img id="imagePreview" src="{{ asset('images/NoImage.png') }}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>

                    <!-- File Pustaka -->
                    <div class="col-md-6 form-group mt-3">
                        <label for="pus_file" class="form-label">File Pustaka (PDF, DOCX, XLSX, PPTX, MP4)</label>
                        <input type="file" name="pus_file" id="pus_file" class="form-control @error('pus_file') is-invalid @enderror" accept=".pdf,.docx,.xlsx,.pptx,.mp4" required>
                        @error('pus_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi  -->
                    <div class="col-md-12 form-group mt-3">
                        <label for="pus_keterangan" class="form-label">Deskripsi / Ringkasan Pustaka</label>
                        <textarea name="pus_keterangan" id="pus_keterangan" rows="4" class="form-control @error('pus_keterangan') is-invalid @enderror" required>{{ old('pus_keterangan') }}</textarea>
                        @error('pus_keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-end mt-4 mb-3">
            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Batalkan</button>
            <button type="submit" class="btn btn-primary ml-3">Simpan</button>
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
    }
</script>

