@extends('layouts.app')

@section('content')
<div class="backSearch">
    <h1>Daftar Pustaka</h1>
    <p>
        ASTRAtech memiliki banyak program studi, di dalam program studi terdapat kelompok keahlian yang biasa disebut dengan Kelompok Keahlian
    </p>
    <div class="input-wrapper">
        <div class="" style="width: 700px; display: flex; background-color: white; border-radius: 20px; height: 40px;">
            <input 
                id="pencarianPustaka" 
                name="query" 
                type="text" 
                placeholder="Cari Daftar Pustaka" 
                class="form-control" 
                style="border: none; width: 680px; height: 40px; border-radius: 20px;">
            <button 
                class="btn btn-primary px-4" 
                style="background-color: transparent; color: #08549F;" 
                onclick="handleSearch()">
                Cari
            </button>
        </div>
    </div>
</div>

@if($isLoading)
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@else
    <div class="d-flex flex-column">
        <div class="flex-fill">
            <div class="navigasi-layout-page">
                <p class="title-kk">Daftar Pustaka</p>
                <div class="left-feature">
                    <div class="status">
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="fas fa-circle" style="color: green;"></i>
                                    </td>
                                    <td>
                                        <p>Pustaka Saya</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-circle" style="color: #66ACE9;"></i>
                                    </td>
                                    <td>
                                        <p>Aktif/Publik</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-circle" style="color: red;"></i>
                                    </td>
                                    <td>
                                        <p>Tidak Aktif</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex">
                        <div class="">
                            <form id="filterForm" method="GET" action="{{ route('pustaka.index') }}">
                                <select id="ddUrut" name="sort" class="form-select">
                                    <option value="[Judul] ASC" selected>Judul Pustaka [↑]</option>
                                    <option value="[Judul] DESC">Judul Pustaka [↓]</option>
                                </select>
                                <select id="ddStatus" name="status" class="form-select mt-2">
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Filter</button>
                            </form>
                        </div>
                        <div class="ms-3">
                            <a href="{{ route('pustaka.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($currentData->isEmpty())
                <div class="alert alert-warning mt-3 mx-5">
                    Tidak ada data daftar pustaka!
                </div>
            @else
                <div class="mx-5">
                    @foreach($currentData as $data)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $data['judul'] }}</h5>
                                <p class="card-text">{{ $data['keterangan'] }}</p>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('pustaka.show', $data['id']) }}" class="btn btn-info">Detail</a>
                                    <a href="{{ route('pustaka.edit', $data['id']) }}" class="btn btn-warning">Edit</a>
                                    <form method="POST" action="{{ route('pustaka.destroy', $data['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex justify-content-center mt-4">
                {{ $currentData->links() }}
            </div>
        </div>
    </div>
@endif
@endsection
