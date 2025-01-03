@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="text-primary font-weight-bold">Tambah Daftar Pustaka</h4>
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

    <form action="{{ route('master.daftar-pustaka.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="file-preview">
                            <div class="preview-img">
                                <img src="{{ asset('images/NoImage.png') }}" alt="No Preview Available" id="imagePreview" class="img-fluid" style="width: 200px; border-radius: 20px;">
                            </div>
                        </div>
                        <div class="fileupload">
                            <label for="pus_gambar">Gambar Daftar Pustaka (.png)</label>
                            <input type="file" id="pus_gambar" name="pus_gambar" class="form-control" onchange="previewImage(event)">
                            @error('pus_gambar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="pus_judul">Judul / Nama File Pustaka</label>
                        <input type="text" name="pus_judul" id="pus_judul" class="form-control" value="{{ old('pus_judul') }}" required>
                        @error('pus_judul')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="kke_id">Kelompok Keahlian</label>
                        <select name="kke_id" id="kke_id" class="form-control" required>
                            <option value="">Pilih Kelompok Keahlian</option>
                            @foreach($listKK as $kk)
                                <option value="{{ $kk->id }}" {{ old('kke_id') == $kk->id ? 'selected' : '' }}>{{ $kk->nama_kelompok_keahlian }}</option>
                            @endforeach
                        </select>
                        @error('kke_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="pus_kata_kunci">Kata Kunci</label>
                        <input type="text" name="pus_kata_kunci" id="pus_kata_kunci" class="form-control" value="{{ old('pus_kata_kunci') }}" required>
                        @error('pus_kata_kunci')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="pus_file">File Pustaka (.pdf, .docx, .xlsx, .pptx, .mp4)</label>
                        <input type="file" name="pus_file" id="pus_file" class="form-control" required>
                        @error('pus_file')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="pus_keterangan">Sinopsis / Ringkasan Pustaka</label>
                        <textarea name="pus_keterangan" id="pus_keterangan" class="form-control" required>{{ old('pus_keterangan') }}</textarea>
                        @error('pus_keterangan')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Batalkan</button>
            <button type="submit" class="btn btn-primary ml-2">Simpan</button>
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
@endsection
