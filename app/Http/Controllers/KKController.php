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
            // Ambil usr_id dari cookies
            $usr_id = Cookie::get('usr_id');

            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan di cookies.');
            }
            // Simpan gambar jika ada
            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('file');
                $file->move($destinationPath, $fileName);
                $gambarPath = 'file/' . $fileName;
            }

            $result = DB::select('EXEC pknow_createKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $request->nama,            // @p1
                $request->programStudi,    // @p2
                $request->personInCharge,  // @p3
                $request->deskripsi,       // @p4
                $gambarPath,               // @p5
                $usr_id,    // @p6
                ...array_fill(6, 44, null) // Sisanya null
            ]);
    
            if (!empty($result) && $result[0]->hasil === 'OK') {
                // Redirect ke halaman sesuai role dan pengguna
                return redirect()->route('kelola_kk', [
                    'role' => urlencode($request->query('role')), // Ambil role dari query
                    'pengguna' => urlencode($request->query('pengguna')) // Ambil pengguna dari query
                ])->with('success', 'Kelompok Keahlian berhasil ditambahkan.');
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
    
    public function edit(Request $request, $role, $id)
    {
        $data = DB::table('pknow_mskelompokkeahlian')
        ->where('kke_id', $id)
        ->first(); // Ambil satu baris data

    if (!$data) {
        return redirect()->route('kelola_kk', ['role' => urlencode($role)])
            ->with('error', 'Data tidak ditemukan.');
    }

    $listProdi = DB::select('EXEC pknow_getListProdi ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
        array_fill(0, 50, null));
    $listProdi = collect($listProdi)->map(function ($item) {
        return ['value' => $item->Value, 'text' => $item->Text];
    });

    // Ambil daftar karyawan menggunakan SP
    $listKaryawan = DB::select('EXEC pknow_getListKaryawan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
        array_merge([$data->pro_id], array_fill(1, 49, null)));
    $listKaryawan = collect($listKaryawan)->map(function ($item) {
        return ['value' => $item->Value, 'text' => $item->Text];
    });
    return view('page.master-pic-pknow.KelolaKK.editKK', compact('data', 'listProdi', 'listKaryawan', 'role'));
    }

    public function update(Request $request, $role, $id)
    {
        $request->validate([
            'nama' => 'required|max:25',
            'programStudi' => 'required',
            'personInCharge' => 'nullable',
            'deskripsi' => 'required|min:100|max:200',
            'gambar' => 'nullable|image|mimes:png|max:10240', // Maks 10MB
        ]);

        try {
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan di cookies.');
            }

            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('file');
                $file->move($destinationPath, $fileName);
                $gambarPath = 'file/' . $fileName;
            }

            $result = DB::select('EXEC pknow_editKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $id,                        // @p1
                $request->nama,             // @p2
                $request->programStudi,     // @p3
                $request->personInCharge,   // @p4
                $request->deskripsi,        // @p5
                $gambarPath,                // @p6
                null, $usr_id,        // @p8
                ...array_fill(9, 42, null)  // Sisanya null
            ]);

            if (!empty($result) && $result[0]->hasil === 'OK') {
                return redirect()->route('kelola_kk', ['role' => urlencode($role)])->with('success', 'Data berhasil diperbarui.');
            } else {
                throw new \Exception('Gagal memperbarui data.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function detail(Request $request, $role, $id)
    {
        $data = DB::table('pknow_mskelompokkeahlian as kk')
            ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
            ->select('kk.*', 'pro.pro_nama')
            ->where('kk.kke_id', $id)
            ->first();
    
        if (!$data) {
            return redirect()->route('kelola_kk', ['role' => urlencode($role)])
                ->with('error', 'Data tidak ditemukan.');
        }
    
        return view('page.master-pic-pknow.KelolaKK.LihatKK', compact('data', 'role'));
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'newStatus' => 'required|string|in:Aktif,Tidak Aktif',
            'personInCharge' => 'required|string'
        ]);
    
        try {
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            if ($request->newStatus === 'Aktif' && empty($request->personInCharge)) {
                throw new \Exception('Person In Charge harus diisi untuk mengaktifkan.');
            }
    
            $result = DB::select('EXEC pknow_setStatusKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $request->id,                // @p1
                $request->newStatus,         // @p2
                $request->personInCharge,    // @p3
                $usr_id,                     // @p4
                ...array_fill(4, 46, null)   // Sisanya null
            ]);
    
            if (!empty($result) && isset($result[0]->hasil) && $result[0]->hasil === 'SUKSES') {
                return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
            } else {
                $errorMessage = isset($result[0]->ErrorMessage) ? $result[0]->ErrorMessage : 'Gagal memperbarui status.';
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    
}