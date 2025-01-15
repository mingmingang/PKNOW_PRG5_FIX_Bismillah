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
    <title>Tambah Kategori Program - P-KNOW</title>
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
            <h2 class="text-primary">Tambah Kategori</h2>
            <!-- Tampilkan Nama Program -->
            <p><strong>Nama Program:</strong> {{ $programName }}</p>
            <form action="{{ route('submit_kategori') }}" method="POST">
                @csrf
                <input type="hidden" name="program_id" value="{{ $programId }}">
                <div class="form-group mt-3">
                    <label for="namaKategori">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="namaKategori" name="nama_kategori" placeholder="Nama Kategori" required>
                </div>

                <div class="form-group mt-3">
                    <label for="deskripsiKategori">Deskripsi/Penjelasan Singkat Kategori <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="deskripsiKategori" name="deskripsi_kategori" rows="5" placeholder="Deskripsi/Penjelasan Program" required></textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-secondary me-2">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
