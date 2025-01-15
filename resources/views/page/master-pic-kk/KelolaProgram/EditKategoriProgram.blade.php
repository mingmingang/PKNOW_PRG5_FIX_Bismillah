<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Kategori - P-KNOW</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/Beranda.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @include('backbone.headerPICKK', [
        'showMenu' => false,
        'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
        'menuItems' => [],
        'konfirmasi' => 'Konfirmasi',
        'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
        'kelolaProgram' => false,
        'showConfirmation' => false,
    ])

    <div class="container" style="margin-top: 100px;">
        <div class="card">
            <div class="card-body">
                <h1 class="text-primary mb-4">Edit Kategori Program</h1>
                <form id="editKategoriForm" method="POST" action="{{ route('submit_update_kategori', $category->Key) }}">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $category->{"Program ID"} }}">
                    <input type="hidden" name="status" value="{{ $category->{"Status"} }}">

                    <div class="form-group mt-4">
                        <label for="kategoriName">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" id="kategoriName" class="form-control" value="{{ $category->{"Nama Kategori"} }}" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="kategoriDescription">Deskripsi/Penjelasan Singkat Kategori <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_kategori" id="kategoriDescription" class="form-control" rows="5" required>{{ $category->{"Deskripsi"} }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Batalkan</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('backbone.footer')

    <script>
        // Store initial data
        const initialData = {
            namaKategori: "{{ $category->{"Nama Kategori"} }}",
            deskripsi: `{{ $category->{"Deskripsi"} }}`
        };

        // Reset form to initial data
        function resetForm() {
            // Reset text inputs
            document.getElementById('kategoriName').value = initialData.namaKategori;
            document.getElementById('kategoriDescription').value = initialData.deskripsi;
        }
    </script>
</body>
</html>
