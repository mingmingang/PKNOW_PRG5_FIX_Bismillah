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
    
            // Untuk permintaan AJAX (fetch API atau axios)
            if ($request->ajax()) {
                if (count($data) === 0) {
                    return response()->json(['message' => 'Tidak ada data.'], 200); // Jika data kosong
                }
    
                return response()->json($data); // Kembalikan data JSON
            }
    
            // Untuk tampilan view di browser
            return view('page.master-pic-pknow.KelolaKK.KelolaKK', compact('dataFilterSort', 'dataFilterStatus', 'data'));
    
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat mengambil data.',
                    'details' => $e->getMessage(),
                ], 500);
            }
    
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    
    public function create(Request $request)
    {
        $roles = session('roles');
    
        // Fetch list of Prodi
        $listProdi = DB::select('EXEC pknow_getListProdi ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
            array_fill(0, 50, null));
        $listProdi = collect($listProdi)->map(function ($item) {
            return ['value' => $item->Value, 'text' => $item->Text];
        });
    
        $listKaryawan = [];
    
        // Fetch list of karyawan untuk Prodi tertentu
        if ($request->has('prodiId') && $request->get('prodiId') !== '') {
            $listKaryawan = DB::select('EXEC pknow_getListKaryawan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                array_merge([$request->get('prodiId')], array_fill(1, 49, null)));
    
            // Ambil semua `kry_id` yang sudah menjadi PIC
            $existingPICs = DB::table('pknow_mskelompokkeahlian')
                ->whereNotNull('kry_id') // Hanya yang sudah menjadi PIC
                ->pluck('kry_id') // Ambil kry_id
                ->toArray(); // Ubah ke array
    
            // Filter out karyawan yang sudah menjadi PIC
            $listKaryawan = collect($listKaryawan)->filter(function ($item) use ($existingPICs) {
                return !in_array($item->Value, $existingPICs);
            })->values(); // Reset indeks array
        }
    
        return view('page.master-pic-pknow.KelolaKK.TambahKK', compact('listProdi', 'listKaryawan', 'roles'));
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
                return response()->json([
                    'success' => true,
                    'message' => 'Kelompok Keahlian berhasil ditambahkan.',
                ]);
            } else {
                throw new \Exception('Terjadi kesalahan saat menyimpan data.');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
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
            ->leftJoin('ERP_PolmanAstra.dbo.ess_mskaryawan as kry', 'kk.kry_id', '=', 'kry.kry_id')
            ->select(
                'kk.*',
                'pro.pro_nama',
                DB::raw("CONCAT(kry.kry_nama_depan, ' ', kry.kry_nama_blkg) as kry_nama")
            )
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
            'newStatus' => 'required|string|in:Aktif,Tidak Aktif,Menunggu',
            'personInCharge' => 'nullable|string',
        ]);
    
        try {
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Ambil data kelompok keahlian berdasarkan ID
            $kelompokKeahlian = DB::table('pknow_mskelompokkeahlian')
                ->where('kke_id', $request->id)
                ->first();
    
            if (!$kelompokKeahlian) {
                throw new \Exception('Data Kelompok Keahlian tidak ditemukan.');
            }
    
            // Jika status saat ini adalah Draft
            if ($kelompokKeahlian->kke_status === 'Draft') {
                if (empty($kelompokKeahlian->kry_id)) {
                    // Jika PIC belum ada, ubah status menjadi Menunggu
                    $request->merge(['newStatus' => 'Menunggu']);
                } else {
                    // Jika PIC ada, ubah status menjadi Aktif
                    $request->merge(['newStatus' => 'Aktif']);
                }
            }
    
            // Panggil SP untuk mengubah status
            $result = DB::select('EXEC pknow_setStatusKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $request->id,                // @p1
                $request->newStatus,         // @p2
                $request->personInCharge,    // @p3
                $usr_id,                     // @p4
                ...array_fill(4, 46, null),  // Sisanya null
            ]);
    
            // Periksa hasil dari SP
            if (!empty($result) && isset($result[0]->hasil) && $result[0]->hasil === 'SUKSES') {
                return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
            } else {
                throw new \Exception('Gagal memperbarui status.');
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    
    public function kelolaPICIndex()
    {
        $userId = Cookie::get('usr_id'); 
        if (!$userId) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }
    
        $params = array_fill(0, 50, ''); // Isi parameter default
        $params[0] = $userId; // Set parameter pertama sebagai `usr_id`
    
        try {
            $data = DB::select('EXEC pknow_getDataKelompokKeahlianByProdi ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
        } catch (\Exception $e) {
            Log::error('Error fetching data kelompok keahlian: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    
        return view('page.master-prodi.KelolaPICKK.KelolaPICKK', compact('data'));
    }
    
    

    public function kelolaPICEdit($id)
    {
        $userId = Cookie::get('usr_id'); // Ambil user ID dari cookie
        if (!$userId) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }
    
        // Fetch Kelompok Keahlian berdasarkan ID
        $data = DB::table('pknow_mskelompokkeahlian')
            ->where('kke_id', $id)
            ->first(); // Ambil satu baris data
    
        if (!$data) {
            return redirect()->route('kelola.pic')->with('error', 'Data tidak ditemukan.');
        }
    
        // Fetch list karyawan untuk dropdown PIC
        $listKaryawan = DB::select('EXEC pknow_getListKaryawan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
            $data->pro_id, // Parameter Prodi ID
            ...array_fill(1, 49, '')
        ]);
    
        // Ambil semua `kry_id` yang sudah menjadi PIC (kecuali PIC saat ini)
        $existingPICs = DB::table('pknow_mskelompokkeahlian')
            ->whereNotNull('kry_id') // Hanya karyawan yang sudah menjadi PIC
            ->where('kke_id', '!=', $id) // Kecualikan ID Kelompok Keahlian yang sedang diedit
            ->pluck('kry_id') // Ambil kry_id
            ->toArray(); // Ubah ke array
    
        // Tambahkan PIC saat ini ke daftar jika tidak null
        if ($data->kry_id) {
            $currentPIC = DB::table('ERP_PolmanAstra.dbo.ess_mskaryawan')
                ->select('kry_id as Value', DB::raw("CONCAT(kry_nama_depan, ' ', kry_nama_blkg) as Text"))
                ->where('kry_id', $data->kry_id)
                ->first();
    
            if ($currentPIC) {
                $listKaryawan[] = $currentPIC; // Tambahkan ke list
            }
        }
    
        // Filter karyawan yang belum menjadi PIC atau adalah PIC saat ini
        $listKaryawan = collect($listKaryawan)->filter(function ($item) use ($existingPICs, $data) {
            return !in_array($item->Value, $existingPICs) || $item->Value === $data->kry_id;
        })->unique('Value')->values(); // Reset indeks array dan hapus duplikat
    
        return view('page.master-prodi.KelolaPICKK.TambahPICKK', compact('data', 'listKaryawan', 'id'));
    }

    public function kelolaPICUpdate(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'personInCharge' => 'required', // Wajib memilih PIC
        ]);
    
        // Ambil `usr_id` dari cookie
        $usr_id = Cookie::get('usr_id');
        if (!$usr_id) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }
    
        try {
            // Ambil data kelompok keahlian saat ini
            $data = DB::table('pknow_mskelompokkeahlian')
                ->where('kke_id', $id)
                ->first();
    
            if (!$data) {
                return redirect()->back()->with('error', 'Data kelompok keahlian tidak ditemukan.');
            }
    
            // Siapkan parameter untuk stored procedure
            $params = [
                $id,                           // @p1: ID Kelompok Keahlian
                $data->kke_nama,               // @p2: Nama Kelompok Keahlian (tetap menggunakan data lama)
                $data->pro_id,                 // @p3: Program Studi ID (tetap menggunakan data lama)
                $request->personInCharge,      // @p4: Person In Charge (PIC) baru
                $data->kke_deskripsi,          // @p5: Deskripsi (tetap menggunakan data lama)
                $data->kke_gambar,             // @p6: Gambar (tetap menggunakan data lama)
                null,                          // @p7: Tidak digunakan
                $usr_id,                       // @p8: User ID yang melakukan perubahan
                ...array_fill(8, 42, null)     // Sisanya null
            ];
    
            // Panggil stored procedure untuk update data
            $result = DB::select('EXEC pknow_editKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
    
            if (!empty($result) && $result[0]->hasil === 'OK') {
                // Setelah SP berhasil dijalankan, update status menjadi Aktif
                DB::table('pknow_mskelompokkeahlian')
                    ->where('kke_id', $id)
                    ->update(['kke_status' => 'Aktif', 'kke_modif_by' => $usr_id, 'kke_modif_date' => now()]);
    
                return redirect()->route('kelola.pic')->with('success', 'PIC berhasil diperbarui dan status diubah menjadi Aktif.');
            } else {
                return redirect()->back()->with('error', 'Gagal memperbarui PIC. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function lihatKK($id)
    {
        // Ambil data Kelompok Keahlian berdasarkan ID
        $data = DB::table('pknow_mskelompokkeahlian as kk')
            ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
            ->leftJoin('ERP_PolmanAstra.dbo.ess_mskaryawan as kry', 'kk.kry_id', '=', 'kry.kry_id')
            ->select(
                'kk.*',
                'pro.pro_nama',
                DB::raw("CONCAT(kry.kry_nama_depan, ' ', kry.kry_nama_blkg) as kry_nama")
            )
            ->where('kk.kke_id', $id)
            ->first();
    
        // Jika data tidak ditemukan, kembali ke halaman sebelumnya
        if (!$data) {
            return redirect()->route('kelola_kk')->with('error', 'Data tidak ditemukan.');
        }
    
        // Render view untuk menampilkan data
        return view('page.master-prodi.KelolaKK.LihatKK', compact('data'));
    }
    

}