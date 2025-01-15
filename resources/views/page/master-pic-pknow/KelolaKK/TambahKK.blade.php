<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Tambah Kelompok Keahlian</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@include('backbone.headerpicpknow', [
    'showMenu' => false, // Menyembunyikan menu pada halaman login
    'userProfile' => ['name' => 'User', 'role' => 'Guest', 'lastLogin' => ''],
    'menuItems' => [],
    'konfirmasi' => 'Konfirmasi',
    'pesanKonfirmasi' => 'Apakah Anda yakin ingin keluar?',
    'kelolaProgram' => false,
    'showConfirmation' => false, // Pastikan variabel ini ada untuk header
    ])


<div class="container" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Tambah Kelompok Keahlian</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="col-12">
        <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kelompok Keahlian (Maksimal 25 Huruf)</label> <span style="color: red;">*</span>
                    <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi (Minimal 100 huruf)<span style="color: red;"> *
                    </span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="programStudi" class="form-label">Program Studi</label> <span style="color: red;">*</span>
                    <select name="programStudi" id="programStudi" class="form-select @error('programStudi') is-invalid @enderror" required >
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($listProdi as $prodi)
                            <option value="{{ $prodi['value'] }}" {{ old('programStudi') == $prodi['value'] ? 'selected' : '' }}>
                                {{ $prodi['text'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('programStudi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <select name="personInCharge" id="personInCharge" class="form-control">
                    <option value="">-- Pilih PIC Kelompok Keahlian --</option>
                    @foreach ($listKaryawan as $item)
                        <option value="{{ $item['value'] }}">{{ $item['text'] }}</option>
                    @endforeach
                </select>

                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar (PNG, max 10MB)</label><span style="color: red;">*</span>
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary me-2">Batalkan</button>
                    <button type="" class="btn btn-secondary me-2" onclick="handleback()">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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

function handleSubmit() {
    // Ambil role dari cookie
    const role = "{{ Cookie::get('role') }}";

    // Validasi apakah role tersedia
    if (!role) {
        console.error("Role is missing. Cannot navigate to the add page.");
        Swal.fire("Error", "Role tidak ditemukan. Silakan login ulang.", "error");
        return;
    }

    // Navigasi ke halaman Tambah berdasarkan role
    const url = `/kelola_kk/${role}/tambah`;
    console.log("Navigating to:", url);
    window.location.href = url;
}
</script>

<script>

    const urlParams = new URLSearchParams(window.location.search);
    const role = "{{ Cookie::get('role') }}";
    const pengguna = "{{ Cookie::get('pengguna') }}";

    const usrId = "{{ Cookie::get('usr_id') }}";
    console.log('usr_id dari cookie:', usrId);
    
    console.log('Role:', role);
    console.log('Pengguna:', pengguna);

    function resetForm() {
        const form = document.querySelector('form');

        form.reset();

        const gambarPreview = document.getElementById('gambarPreview');
        gambarPreview.src = '{{ asset('assets/NoImage.png') }}';
        const personInChargeSelect = document.getElementById('personInCharge');
        personInChargeSelect.innerHTML = '<option value="">Pilih PIC</option>';

        console.log("Form telah direset.");
    }

    function previewImage(event) {
        const input = event.target;
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('gambarPreview').src = reader.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

</script>


<script>
    $(document).ready(function () {
        // Ketika dropdown Program Studi berubah
        $('#programStudi').on('change', function () {
            const prodiId = $(this).val(); // Ambil value dari Program Studi yang dipilih

            if (!prodiId) {
                // Kosongkan dropdown PIC jika Program Studi tidak dipilih
                $('#personInCharge').html('<option value="">-- Pilih PIC --</option>');
                return;
            }

            // Kirim request ke server untuk mendapatkan data PIC
            $.ajax({
                url: "{{ route('kelola_kk.getKaryawan') }}", // Route ke backend
                type: 'GET',
                data: { prodiId: prodiId }, // Kirimkan prodiId sebagai parameter
                success: function (response) {
                    // Reset dropdown PIC
                    let options = '<option value="">-- Pilih PIC --</option>';
                    response.forEach(function (karyawan) {
                        options += `<option value="${karyawan.value}">${karyawan.text}</option>`;
                    });

                    // Update dropdown PIC
                    $('#personInCharge').html(options);
                },
                error: function () {
                    alert('Gagal memuat data PIC. Silakan coba lagi.');
                }
            });
        });
    });
</script>


<script>
    function redirectToCreate(prodiId) {
        if (prodiId) {
            // Arahkan ke route dengan parameter prodiId
            window.location.href = "{{ route('kelola_kk.create') }}?prodiId=" + prodiId +"&role=${encodeURIComponent(role)}&pengguna=${encodeURIComponent(name)}";
        }
    }
</script>

<script>
    function handleback() {
        window.history.back();
    }
    </script>

<script>
    $(document).ready(function () {
        $("#formId").on("submit", function (event) {
            event.preventDefault(); // Prevent form submission

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('kelola_kk.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then(() => {
                            // Redirect atau refresh data jika diperlukan
                            window.location.href = "{{ route('kelola_kk') }}";
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: response.message,
                            icon: "error",
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON.message || "Terjadi kesalahan.",
                        icon: "error",
                    });
                },
            });
        });
    });
</script>

@include('backbone.footer')


