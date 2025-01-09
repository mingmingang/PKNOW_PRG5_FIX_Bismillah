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

    
    public function getTempDataKK(Request $request, $role)
    {
        // Debugging untuk memastikan parameter diterima
        error_log('Role: ' . $role);
        error_log('Request Params: ' . print_r($request->all(), true));
    
        $params = [
            $request->input('page', 1), // @p1
            $request->input('query', '') !== null ? $request->input('query', '') : '', // @p2
            $request->input('sort', '[Nama Kelompok Keahlian] ASC'), // @p3
            $request->input('status', ''), // @p4
            ...array_fill(4, 46, null), // @p5 sampai @p50
        ];
    
        try {
            // Debugging parameter yang dikirim ke SP
            error_log('Parameters ke SP: ' . print_r($params, true));
    
            // Eksekusi stored procedure
            $data = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
            
            
            // Debugging hasil dari SP
            error_log('Data dari SP: ' . print_r($data, true));
            error_log('Request Headers: ' . print_r($request->header(), true));

    
            // Jika permintaan menggunakan AJAX, kembalikan JSON
            if ($request->ajax()) {
                error_log('Mengembalikan JSON: ' . json_encode($data));
                return response()->json($data);
            } else {
                error_log('Deteksi AJAX: Permintaan dianggap bukan AJAX');
                // Kembalikan view untuk akses biasa
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
            
                return view('page.master-pic-pknow.KelolaKK.KelolaKK', compact('data', 'dataFilterSort', 'dataFilterStatus'));
            }
        } catch (\Exception $e) {
            error_log('Error saat memproses: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage(),
            ], 500);
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
    
        // Gunakan role untuk menentukan tampilan blade
        if ($role === 'prodi') {
            return view('page.master-prodi.KelolaKK.LihatKK', compact('data', 'role'));
        } else {
            return view('page.master-pic-pknow.KelolaKK.LihatKK', compact('data', 'role'));
        }
    }
    
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'newStatus' => 'required|string|in:Aktif,Tidak Aktif,Menunggu',
            'personInCharge' => 'nullable|string', // PIC bisa nullable
        ]);
    
        try {
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            Log::info('Toggle Status Payload:', [
                'id' => $request->id,
                'newStatus' => $request->newStatus,
                'personInCharge' => $request->personInCharge,
            ]);
    
            $id = $request->id;
            $newStatus = $request->newStatus;
            $personInCharge = $request->personInCharge;
    
            $params = [
                $id,                  // @p1
                $newStatus,           // @p2
                $personInCharge,      // @p3
                $usr_id,              // @p4
                now(),                // @p5
                ...array_fill(5, 45, null), // @p6 sampai @p50
            ];
    
            $result = DB::select(
                'EXEC pknow_setStatusKelompokKeahlian ' . implode(', ', array_fill(0, count($params), '?')),
                $params
            );
    
            if (!empty($result) && isset($result[0]->hasil) && $result[0]->hasil === 'SUKSES') {
                return response()->json(['success' => true, 'message' => "Status berhasil diubah menjadi '$newStatus'."]);
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
        $role = Cookie::get('role'); // Ambil role dari cookie
    
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
    
        return view('page.master-prodi.KelolaPICKK.KelolaPICKK', compact('data', 'role'));
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

    public function kelolaAKK(Request $request, $role)
    {
        $query = $request->input('query', ''); // Ambil query dari input
    
        $params = [
            $request->input('page', 1), // @p1
            $query ?: '%', // @p2 (Jika kosong, gunakan '%' untuk menampilkan semua data)
            $request->input('sort', '[Nama Kelompok Keahlian] ASC'), // @p3
            'Aktif', // Filter status hanya "Aktif" untuk @p4
            ...array_fill(4, 46, null), // @p5 sampai @p50
        ];
    
        try {
            // Panggil stored procedure langsung
            $data = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
    
            // Debug data untuk memastikan hasil
            error_log('Data Kelola AKK: ' . print_r($data, true));
    
            $dataFilterSort = [
                ['Value' => '[Nama Kelompok Keahlian] asc', 'Text' => 'Nama Kelompok Keahlian [↑]'],
                ['Value' => '[Nama Kelompok Keahlian] desc', 'Text' => 'Nama Kelompok Keahlian [↓]'],
            ];
    
            return view('page.master-pic-pknow.KelolaAKK.KelolaAKK', [
                'data' => collect($data), // Konversi menjadi collection untuk kemudahan manipulasi
                'dataFilterSort' => $dataFilterSort,
            ]);
        } catch (\Exception $e) {
            error_log('Error di Kelola AKK: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
    
    
    
    public function tambahAnggota(Request $request, $role, $id)
    {
        error_log("datakuu: " . $id);

        try {
            // Ambil detail kelompok keahlian
            $kelompok = DB::table('pknow_mskelompokkeahlian as kk')
                ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
                ->leftJoin('ERP_PolmanAstra.dbo.ess_mskaryawan as kry', 'kk.kry_id', '=', 'kry.kry_id')
                ->select(
                    'kk.kke_id',
                    'kk.kke_nama',
                    'kk.kke_deskripsi',
                    'kk.kke_gambar',
                    'kk.kke_status',
                    'pro.pro_nama as Prodi',
                    DB::raw("ISNULL(CONCAT(kry.kry_nama_depan, ' ', kry.kry_nama_blkg), 'Tidak Ditemukan') as PIC")
                )
                ->where('kk.kke_id', $id)
                ->first();
    
            if (!$kelompok) {
                return back()->with('error', 'Data tidak ditemukan.');
            }
    
            $kke_id = $id;
            $search = $request->input('search', '') !== null ? $request->input('search', '') : ''; // Pencarian
            $filterProdi = $request->input('prodi', '0'); // Filter Prodi
            $filterStatus = $request->input('status', 'Aktif'); 
            error_log("id prodi: " . $filterProdi);
            // Ambil daftar anggota
            $paramsAnggota = [
                1, // Page
                $search, // Query untuk pencarian anggota
                '[Nama Anggota] ASC', // Urutan anggota
                $filterStatus, // Status
                $kke_id, // ID Kelompok Keahlian
                ...array_fill(5, 45, null)
            ];
            error_log('Anott List: ' . json_encode($paramsAnggota));
            error_log("Search Value: " . ($search === '' ? 'EMPTY STRING' : 'NULL or VALUE: ' . $search));
            $anggota = DB::select('EXEC pknow_getListAnggotaKeahlian ' . implode(', ', array_fill(0, 50, '?')), $paramsAnggota);
    
            if (isset($anggota[0]->Message) && $anggota[0]->Message === 'data kosong') {
                $anggota = [];
            }
            
            // Ambil daftar dosen dengan filter pencarian dan prodi
            $paramsDosen = [ // Query pencarian dosen
                $filterProdi, // Filter berdasarkan prodi
                '', '', '', ...array_fill(4, 46, null)
            ];

           

            $dosen = collect(DB::select('EXEC pknow_getListDosen ' . implode(', ', array_fill(0, 50, '?')), $paramsDosen));
            // ->filter(function ($item) use ($filterProdi) {
            //     return !$filterProdi || $item->Prodi === $filterProdi; // Hanya tampilkan yang sesuai prodi
            // });

            // Ambil daftar prodi untuk dropdown filter
            $prodiList = DB::select('EXEC pknow_getListProdi ' . implode(', ', array_fill(0, 50, '?')), array_fill(0, 50, null));
            // error_log('Prodi List: ' . json_encode($prodiList));
            $prodiList = collect($prodiList)->map(function ($item) {
                return [
                    'Value' => $item->Value, // Ini `pro_id`
                    'Text' => $item->Text,   // Ini `pro_nama`
                ];
            });
            
            return view('page.master-pic-pknow.KelolaAKK.TambahAKK', compact('kelompok', 'anggota', 'dosen', 'prodiList', 'kke_id', 'role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    
    
    public function addAnggota(Request $request)
    {
        $usr_id = Cookie::get('usr_id');
        if (!$usr_id) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }
    
        try {
            // Eksekusi SP untuk menambahkan anggota
            $params = [
                $request->kke_id, // ID Kelompok Keahlian
                $request->kry_id, // ID Karyawan
                $usr_id,          // User ID (PIC)
                ...array_fill(3, 47, null)
            ];
            error_log('Parameters to SP: ' . json_encode($params));
    
            // Eksekusi stored procedure
            $result = DB::select('EXEC pknow_createAnggotaByPIC ' . implode(', ', array_fill(0, 50, '?')), $params);
    
            error_log('Result from SP: ' . json_encode($result));
    
            return redirect()->route('kelola_anggota', ['role' => $request->role, 'id' => $request->kke_id])
            ->with('success', 'Anggota berhasil ditambahkan.');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    public function deleteAnggota(Request $request, $id)
    {
        $usr_id = Cookie::get('usr_id');
        if (!$usr_id) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }
    
        try {
            $params = [
                $id,           // ID Anggota
                'Dibatalkan',  // Status baru
                $usr_id,       // ID User yang menghapus
                ...array_fill(3, 47, null)
            ];
            error_log('Delete Anggota Params: ' . json_encode($params));
    
            // Eksekusi stored procedure untuk menghapus anggota
            $result = DB::select('EXEC pknow_setStatusAnggotaKeahlian ' . implode(', ', array_fill(0, 50, '?')), $params);
    
            // Log hasil eksekusi stored procedure
            error_log('Delete Anggota Result: ' . json_encode($result));
    
            return redirect()->route('kelola_anggota', ['role' => $request->role, 'id' => $request->kke_id])
            ->with('success', 'Anggota berhasil dihapus.');
        } catch (\Exception $e) {
            error_log('Error di Delete Anggota: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
}