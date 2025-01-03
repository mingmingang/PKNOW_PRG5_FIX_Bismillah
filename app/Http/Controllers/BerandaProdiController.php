<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BerandaProdiController extends Controller
{
    public function index()
    {
        $slidesData = [
            [
                "title" => "Program Studi Pembuatan Peralatan dan Perkakas Produksi",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundP4.png",
                "mascot" => "assets/BackBeranda/MaskotP4.png",
            ],
            [
                "title" => "Program Studi Teknik Produksi Dan Proses Manufaktur",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundTPM.png",
                "mascot" => "assets/BackBeranda/maskotTPM.png",
            ],
            [
                "title" => "Manajemen Informatika",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundMI.png",
                "mascot" => "assets/BackBeranda/MaskotMI.png",
            ],
            [
                "title" => "Mesin Otomotif",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundMO.png",
                "mascot" => "assets/BackBeranda/MaskotMO.png",
            ],
            [
                "title" => "Mekatronika",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundMK.png",
                "mascot" => "assets/BackBeranda/MaskotMK.png",
            ],
            [
                "title" => "Teknologi Konstruksi Bangunan Gedung",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundTKB.png",
                "mascot" => "assets/BackBeranda/MaskotTKBG.png",
            ],
            [
                "title" => "Teknologi Rekayasa Pemeliharaan Alat Berat",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundTRPAB.png",
                "mascot" => "assets/BackBeranda/maskotTRPAB.png",
            ],
            [
                "title" => "Teknologi Rekayasa Logistik",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundTRL.png",
                "mascot" => "assets/BackBeranda/MaskotTRL.png",
            ],
            [
                "title" => "Teknologi Rekayasa Perangkat Lunak",
                "subtitle" => "“Sistem Manajemen Pengetahuan ini akan membantu Anda belajar lebih efisien. Mari kita mulai dengan menjelajahi fitur-fitur yang tersedia dengan mengakses menu yang disediakan.”",
                "buttonText" => "Knowledge Database",
                "backgroundImage" => "assets/BackBeranda/BackgroundTRPL.png",
                "mascot" => "assets/BackBeranda/MaskotTRPL.png",
            ]
        ];

        return view('backbone.BerandaProdi', compact('slidesData'));
    }
}
