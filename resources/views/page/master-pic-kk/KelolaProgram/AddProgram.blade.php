<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Search.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Program Kelompok Keahlian - P-KNOW</title>
</head>
@include('backbone.headerPICKK', [
    'showMenu' => false,
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
])
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <a href="{{ url()->previous() }}" class="btn btn-link">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <div class="card mt-3">
        <div class="card-body">
            <h2 class="text-primary">Tambah Program</h2>
            <form action="{{ route('submit_program') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kke_id" value="{{ $kkeId }}">
                <div class="form-group mt-3">
                    <label for="gambarProgram">Gambar Program (.png) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="gambarProgram" name="gambar" accept=".png" onchange="previewImage(event)">
                    <small class="form-text text-muted">Maksimum ukuran berkas adalah 10 MB</small>
                    <div class="mt-3">
                        <img id="preview" class="img-thumbnail" style="max-width: 200px; outline: none; border: none;">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="namaProgram">Nama Program <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="namaProgram" name="nama_program" placeholder="Nama Program" required>
                </div>

                <div class="form-group mt-3">
                    <label for="deskripsiProgram">Deskripsi/Penjelasan Program <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="deskripsiProgram" name="deskripsi" rows="5" placeholder="Deskripsi/Penjelasan Program" required></textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-secondary me-2">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '{{ asset("images/default-image.png") }}';
        }
    }
</script>
@endsection
