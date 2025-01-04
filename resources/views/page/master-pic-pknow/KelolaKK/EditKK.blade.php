<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Edit Kelompok Keahlian</title>
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

<div class="container" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Edit Kelompok Keahlian</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="col-12">
            <form action="{{ route('kelola_kk.update', ['role' => urlencode($role), 'id' => $data->kke_id]) }}" method="POST" enctype="multipart/form-data" id="formId">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kelompok Keahlian</label>
                    <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ $data->kke_nama }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ $data->kke_deskripsi }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="programStudi" class="form-label">Program Studi</label>
                    <select name="programStudi" id="programStudi" class="form-select @error('programStudi') is-invalid @enderror" required>
                        @foreach($listProdi as $prodi)
                            <option value="{{ $prodi['value'] }}" {{ $prodi['value'] == $data->pro_id ? 'selected' : '' }}>
                                {{ $prodi['text'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('programStudi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="personInCharge" class="form-label">Person In Charge</label>
                    <select name="personInCharge" id="personInCharge" class="form-select @error('personInCharge') is-invalid @enderror">
                        <option value="">-- Pilih PIC --</option>
                        @foreach($listKaryawan as $karyawan)
                            <option value="{{ $karyawan['value'] }}" {{ $karyawan['value'] == $data->kry_id ? 'selected' : '' }}>
                                {{ $karyawan['text'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('personInCharge')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar (PNG, max 10MB)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    @if($data->kke_gambar)
                        <div class="mt-2">
                            <img src="{{ asset($data->kke_gambar) }}" alt="Gambar Kelompok Keahlian" width="100">
                        </div>
                    @endif
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary me-2">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const listProdi = @json($listProdi);
    console.log("List Program Studi:", listProdi);

    const listKaryawan = @json($listKaryawan);
    console.log("List Karyawan:", listKaryawan);
</script>

<script>
    $(document).ready(function () {
        $('#programStudi').on('change', function () {
            const prodiId = $(this).val();

            if (!prodiId) {
                $('#personInCharge').html('<option value="">-- Pilih PIC --</option>');
                return;
            }

            $.ajax({
                url: "{{ route('kelola_kk.getKaryawan') }}",
                type: 'GET',
                data: { prodiId: prodiId },
                success: function (response) {
                    let options = '<option value="">-- Pilih PIC --</option>';
                    response.forEach(function (karyawan) {
                        options += `<option value="${karyawan.value}">${karyawan.text}</option>`;
                    });
                    $('#personInCharge').html(options);
                },
                error: function () {
                    alert('Gagal memuat data PIC. Silakan coba lagi.');
                }
            });
        });
    });
</script>

@include('backbone.footer')
