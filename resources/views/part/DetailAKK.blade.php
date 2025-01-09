@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    <p><strong>Prodi:</strong> {{ $prodi }}</p>
    <p><strong>PIC:</strong> {{ $pic }}</p>
    <h2>Deskripsi</h2>
    <p>{{ $desc }}</p>
</div>
@endsection
