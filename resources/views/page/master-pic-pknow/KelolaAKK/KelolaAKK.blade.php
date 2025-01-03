@extends('layouts.app')

@section('content')
    <div class="app-container">
        <!-- Header -->
        @include('backbone.header', [
            'showMenu' => true,
            'dropdownContent' => [
                ['title' => 'Kelola Kelompok Keahlian', 'icon' => 'fas fa-cogs'],
                ['title' => 'Kelola Anggota', 'icon' => 'fas fa-users'],
                ['title' => 'Daftar Pustaka', 'icon' => 'fas fa-book']
            ],
            'userProfile' => [
                'name' => 'Budi Hartono',
                'role' => 'PIC P-KNOW',
                'lastLogin' => now()->format('l, d F Y H:i'),
                'photo' => asset('assets/fotobudi.png')
            ],
            'menuItems' => ['beranda', 'knowledge-database', 'i-learning']
        ])

        <main>
            <!-- Search Section -->
            @include('part.search', [
                'title' => 'Kelola Anggota',
                'description' => 'P-KNOW System dapat mengatur hak akses anggota yang terdaftar di sistem KMS ASTRAtech.',
                'placeholder' => 'Cari Anggota'
            ])

            <div class="navigasi-layout-page">
                <p class="title-kk">Kelompok Keahlian</p>
                <div class="left-feature">
                    <div class="status">
                        <table>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-circle" style="color: #4a90e2;"></i></td>
                                    <td><p>Aktif/Sudah Publikasi</p></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-circle" style="color: #b0b0b0;"></i></td>
                                    <td><p>Menunggu PIC dari Prodi</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kelompok Keahlian -->
            <div class="kelompok-keahlian-container">
                @foreach ([
                    ['title' => 'Android Developer', 'program' => 'Informatics Management', 'pic' => 'Kevin Trikusuma Dewa', 'description' => 'Android developers create applications for smartphones or tablets.', 'statusText' => 'Aktif/Sudah Publikasi', 'image' => asset('assets/developer.png')],
                    ['title' => 'Web Developer', 'program' => 'Computer Science', 'pic' => 'John Doe', 'description' => 'Web developers build and maintain websites.', 'statusText' => 'Aktif/Sudah Publikasi', 'image' => asset('assets/developer.png')],
                    ['title' => 'Data Scientist', 'program' => 'Data Science', 'pic' => 'Jane Smith', 'description' => 'Data scientists analyze complex data to help organizations make better decisions.', 'statusText' => 'Aktif/Sudah Publikasi', 'image' => asset('assets/developer.png')]
                ] as $kelompok)
                    <div class="kelompok-keahlian-item">
                        @include('part.kelompok-keahlian', [
                            'image' => $kelompok['image'],
                            'title' => $kelompok['title'],
                            'program' => $kelompok['program'],
                            'pic' => $kelompok['pic'],
                            'description' => $kelompok['description'],
                            'statusImage' => asset('assets/aktif.png'),
                            'statusText' => $kelompok['statusText'],
                            'buttonText' => 'Kelola Anggota',
                            'iconClass' => 'fas fa-user',
                            'onClick' => 'window.location.href = "/kelola-daftar-anggota-keahlian";',
                            'showDropdown' => false
                        ])
                    </div>
                @endforeach
            </div>
        </main>

        <!-- Footer -->
        @include('backbone.footer')
    </div>
@endsection
