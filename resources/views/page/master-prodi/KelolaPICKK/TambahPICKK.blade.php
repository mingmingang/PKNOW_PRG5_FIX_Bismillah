<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Kelompok Keahlian - Program Studi</title>
</head>

@include('backbone.HeaderProdi', [
    'showMenu' => true,
    'userProfile' => ['name' => 'User', 'role' => 'Program Studi', 'lastLogin' => now()],
    'menuItems' => [
        ['name' => 'Beranda', 'link' => '/dashboard'],
        ['name' => 'Kelompok Keahlian', 'link' => '/kelola_kk'],
    ],
    'kelolaProgram' => true,
    'showConfirmation' => false
])

<!-- @extends('layouts.app')

@section('content') -->
<div class="container" style="margin-top: 120px;">
    <h1>Edit PIC Kelompok Keahlian</h1>

    <form action="{{ route('update.pic', $id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                    <img src="{{ asset($data->kke_gambar) }}" alt="Preview" class="img-thumbnail mb-3">
                    </div>
                    <div class="col-md-6">
                        <label><strong>Nama Kelompok Keahlian:</strong></label>
                        <p>{{ $data->kke_nama }}</p>
                        
                        <label><strong>Deskripsi:</strong></label>
                        <p>{{ $data->kke_deskripsi }}</p>
                        
                        <label for="personInCharge"><strong>Pilih PIC:</strong></label>
                        <select name="personInCharge" id="personInCharge" class="form-control">
                            <option value="">-- Pilih PIC Kelompok Keahlian --</option>
                            @foreach ($listKaryawan as $item)
                                <option value="{{ $item->Value }}" {{ $data->kry_id == $item->Value ? 'selected' : '' }}>
                                    {{ $item->Text }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('kelola.pic') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>
@include('backbone.footer')