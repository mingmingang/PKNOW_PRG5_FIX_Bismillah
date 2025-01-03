<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\Encryptor;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;

class KKController extends Controller
{
    
    public function index()
    {
        $roles = session('roles');
        $dataFilterSort = [
            ['Value' => '[Nama Kelompok Keahlian] asc', 'Text' => 'Nama Kelompok Keahlian [↑]'],
            ['Value' => '[Nama Kelompok Keahlian] desc', 'Text' => 'Nama Kelompok Keahlian [↓]'],
        ];

        $dataFilterStatus = [
            ['Value' => '', 'Text' => 'Semua'],
            ['Value' => 'Menunggu', 'Text' => 'Menunggu PIC Prodi'],
            ['Value' => 'Draft', 'Text' => 'Draft'],
            ['Value' => 'Aktif', 'Text' => 'Aktif'],
            ['Value' => 'Tidak Aktif', 'Text' => 'Tidak Aktif'],
        ];

        return view('page.master-pic-pknow.KelolaKK.KelolaKK', compact('dataFilterSort', 'dataFilterStatus', 'roles'));
    }

    public function getTempDataKK(Request $request)
    {
        $dataFilterSort = [
            ['Value' => '[Nama Kelompok Keahlian] asc', 'Text' => 'Nama Kelompok Keahlian [↑]'],
            ['Value' => '[Nama Kelompok Keahlian] desc', 'Text' => 'Nama Kelompok Keahlian [↓]'],
        ];

        $dataFilterStatus = [
            ['Value' => '', 'Text' => 'Semua'],
            ['Value' => 'Menunggu', 'Text' => 'Menunggu PIC Prodi'],
            ['Value' => 'Draft', 'Text' => 'Draft'],
            ['Value' => 'Aktif', 'Text' => 'Aktif'],
            ['Value' => 'Tidak Aktif', 'Text' => 'Tidak Aktif'],
        ];

        $params = [
            $request->input('page', 1),                // @p1: Halaman saat ini
            $request->input('query', ''),             // @p2: Filter "Nama Kelompok Keahlian"
            $request->input('sort', '[Nama Kelompok Keahlian] ASC'), // @p3: Urutan
            $request->input('status', ''),            // @p4: Filter status
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        ];

        try {
            // Panggil stored procedure
            $data = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            // Jika hasil kosong
            if (count($data) === 0) {
                // return response()->json(['message' => 'Tidak ada data.'], 404);
                return view('page.master-pic-pknow.KelolaKK.KelolaKK', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
            }

            // Return hasil sebagai JSON
            return view('page.master-pic-pknow.KelolaKK.KelolaKK', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function create(Request $request)
    {
        $roles = session('roles');

        $listProdi = DB::select('EXEC pknow_getListProdi ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
        array_fill(0, 50, null)); // Isi parameter dengan null karena tidak digunakan
    // Parsing hasil menjadi array
    $listProdi = collect($listProdi)->map(function ($item) {
        return ['value' => $item->Value, 'text' => $item->Text];
    });

    $listKaryawan = [];

    // Panggil stored procedure untuk mendapatkan list karyawan
    if ($request->has('prodiId') && $request->get('prodiId') !== '') {
        $listKaryawan = DB::select('EXEC pknow_getListKaryawan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
            array_merge([$request->get('prodiId')], array_fill(1, 49, null)));

        // Parsing hasil Karyawan
        $listKaryawan = collect($listKaryawan)->map(function ($item) {
            return ['value' => $item->Value, 'text' => $item->Text];
        });
    }
    return view('page.master-pic-pknow.KelolaKK.TambahKK', compact('listProdi', 'listKaryawan','roles'));
    }

    public function getListKaryawan(Request $request)
    {
        $prodiId = $request->query('prodiId', '');

        // Validasi jika prodiId kosong
        if (empty($prodiId)) {
            return response()->json(['error' => 'Program Studi tidak dipilih'], 400);
        }

        // Panggil stored procedure untuk mendapatkan list karyawan
        $listKaryawan = DB::select('EXEC pknow_getListKaryawan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
            array_merge([$prodiId], array_fill(1, 49, null)));

        // Format hasil untuk JSON
        $listKaryawan = collect($listKaryawan)->map(function ($item) {
            return ['value' => $item->Value, 'text' => $item->Text];
        });

        return response()->json($listKaryawan);
    }

    public function store(Request $request)
    {
        // Validasi input
        $role = $request->query('role');
        $pengguna = $request->query('pengguna');

        $request->validate([
            'nama' => 'required|max:25',
            'programStudi' => 'required',
            'personInCharge' => 'nullable',
            'deskripsi' => 'required|min:100|max:200',
            'gambar' => 'nullable|image|mimes:png|max:10240', // Maks 10MB
        ]);
    
        try {
            // Simpan gambar jika ada
            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                // Dapatkan file yang diunggah
                $file = $request->file('gambar');
                
                // Tentukan nama file (opsional, jika ingin menggunakan nama unik)
                $fileName = time() . '_' . $file->getClientOriginalName();
            
                // Tentukan lokasi penyimpanan
                $destinationPath = public_path('file');
            
                // Pindahkan file ke lokasi yang ditentukan
                $file->move($destinationPath, $fileName);
            
                // Simpan path untuk keperluan database
                $gambarPath = 'file/' . $fileName;
            }

            // Jalankan stored procedure
            $result = DB::select('EXEC pknow_createKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $request->nama,            // @p1
                $request->programStudi,    // @p2
                $request->personInCharge,  // @p3
                $request->deskripsi,       // @p4
                $gambarPath,               // @p5
                'budi_h',      // @p6 (Created By)
                ...array_fill(6, 44, null) // Sisanya null
            ]);
    
            // Periksa hasil dari stored procedure
            if (!empty($result) && $result[0]->hasil === 'OK') {
                return response()->json([
                    'success' => true,
                    'redirectUrl' => "/kelola_kk/{$role}?role=" . urlencode($role) . "&pengguna=" . urlencode($pengguna),
                ]);
            } else {
                throw new \Exception('Terjadi kesalahan saat menyimpan data.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }


    public function deleteKelompokKeahlian($id)
    {
        try {
            // Jalankan stored procedure
            $result = DB::select('EXEC pknow_deleteKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                array_merge([$id], array_fill(1, 49, null)));

            // Cek hasil dari stored procedure
            if (!empty($result) && $result[0]->hasil === 'SUKSES') {
                return response()->json(['success' => true, 'message' => 'Kelompok Keahlian berhasil dihapus.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus Kelompok Keahlian.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    

    
}