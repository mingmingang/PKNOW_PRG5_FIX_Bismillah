<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Kelompok Keahlian</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Detail Kelompok Keahlian</h1>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ asset($data->kke_gambar ?? 'default-image.png') }}" alt="Preview" class="img-thumbnail mb-3">
                </div>
                <div class="col-md-6">
                    <label><strong>Nama Kelompok Keahlian:</strong></label>
                    <p>{{ $data->kke_nama }}</p>

                    <label><strong>Deskripsi:</strong></label>
                    <p>{{ $data->kke_deskripsi }}</p>

                    <label><strong>Program Studi:</strong></label>
                    <p>{{ $data->pro_nama }}</p>

                    <label><strong>PIC:</strong></label>
                    <p>{{ $data->kry_nama ?? 'Belum Ditentukan' }}</p>

                    <a href="{{ route('kelola.pic') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
