<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Program - P-KNOW</title>

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
                <h1 class="text-primary mb-4">Edit Program</h1>
                <form id="editProgramForm" method="POST" action="{{ route('program.update', ['role' => $role, 'programKey' => $program->{"Key"}]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="kke_id" value="{{ $program->KKiD }}">
                    <div class="form-group">
                        <label for="programImage">Gambar Program (.png) <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <img id="imagePreview" src="{{ $program->{"Gambar"} ? asset($program->{"Gambar"}) : asset('default.jpg') }}" alt="Gambar Program" class="img-thumbnail" style="width: 100%; max-width: 300px; height: auto;">
                        </div>
                        <input type="file" name="gambar" id="programImage" class="form-control" accept="image/png" onchange="previewImage(event)">
                        <small class="form-text text-muted">Maksimum ukuran berkas adalah 10 MB.</small>
                    </div>

                    <div class="form-group mt-4">
                        <label for="programName">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" name="nama_program" id="programName" class="form-control" value="{{ $program->{"Nama Program"} }}" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="programDescription">Deskripsi/Penjelasan Program <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="programDescription" class="form-control" rows="5" required>{{ $program->{"Deskripsi"} }}</textarea>
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
            imageSrc: "{{ $program->{"Gambar"} ? asset($program->{"Gambar"}) : asset('default.jpg') }}",
            namaProgram: "{{ $program->{"Nama Program"} }}",
            deskripsi: `{{ $program->{"Deskripsi"} }}`
        };

        // Preview image when user uploads a new file
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Reset form to initial data
        function resetForm() {
            // Reset text inputs
            document.getElementById('programName').value = initialData.namaProgram;
            document.getElementById('programDescription').value = initialData.deskripsi;

            // Reset image preview
            document.getElementById('imagePreview').src = initialData.imageSrc;

            // Clear file input
            document.getElementById('programImage').value = '';
        }
    </script>
</body>
</html>
