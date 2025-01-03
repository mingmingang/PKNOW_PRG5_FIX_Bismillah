<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeaderController extends Controller
{
    public function index()
    {
        // Data simulasi untuk contoh
        $userProfile = [
            'name' => 'John Doe',
            'role' => 'Admin',
            'lastLogin' => '2024-08-08',
            'photo' => '/images/profile.jpg',
        ];

        $listMenu = [
            [
                'head' => 'Beranda',
                'link' => '/beranda',
                'icon' => 'fas fa-home',
                'isHidden' => false,
                'sub' => [],
            ],
            [
                'head' => 'Kelola',
                'link' => '/kelola',
                'icon' => 'fas fa-cogs',
                'isHidden' => false,
                'sub' => [
                    ['title' => 'Kelola Kelompok Keahlian', 'link' => '/kelola/kelompok'],
                    ['title' => 'Kelola Anggota', 'link' => '/kelola/anggota'],
                ],
            ],
        ];

        $countNotifikasi = 5; // Contoh notifikasi

        return view('header', compact('userProfile', 'listMenu', 'countNotifikasi'));
    }

    public function tampilHeader()
    {
        $usr_id = Cookie::get('usr_id');
        $role = Cookie::get('role');
        $pengguna = Cookie::get('pengguna');

            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan di cookies.');
            }
        // Logika logout, misalnya: Auth::logout();
        return view('header', compact('userProfile', 'listMenu', 'countNotifikasi'));
    }



    public function logout()
    {
        // Logika logout, misalnya: Auth::logout();
        return redirect('/login');
    }

    public function notifications()
    {
        return redirect('/notifications');
    }
}
