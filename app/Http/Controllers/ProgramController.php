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

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Ambil data pengguna dari cookie
            $userId = Cookie::get('usr_id'); // Pastikan cookie 'usr_id' sudah diset saat login
            if (!$userId) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'User ID tidak ditemukan di cookie.']);
            }
    
            // 2. Ambil data user menggunakan stored procedure
            $userData = DB::select(
                'EXEC pknow_getDataUserLogin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $userId, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            error_log('Data user: ' . json_encode($userData));
            if (empty($userData)) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'Data pengguna tidak ditemukan.']);
            }
            // Ambil kry_id dari data pengguna
            $kryUsername = $userData[0]->Username;
            error_log('kryid: ' . $kryUsername);

            // 3. Ambil data kelompok keahlian berdasarkan kry_id
            $dataKK = DB::select(
                'EXEC pknow_getDataKelompokKeahlianByPIC ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $kryUsername, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            error_log('Data kk: ' . json_encode($dataKK));
            if (empty($dataKK)) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'Data kelompok keahlian tidak ditemukan.']);
            }
    
            // Ambil kke_id (Key) dari data kelompok keahlian
            $kkeId = $dataKK[0]->Key;
            error_log('kk id: ' . $kkeId);
            // 4. Ambil data program berdasarkan kelompok keahlian
            $programs = DB::select(
                'EXEC pknow_getDataProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    1, '', '[Nama Program] ASC', '', $kkeId, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            error_log('Data program: ' . json_encode($programs));
            if (isset($programs[0]->Message) && $programs[0]->Message === 'data kosong') {
                $programs = []; // Set program menjadi array kosong untuk mempermudah pengecekan di blade
            }
            
            // Ambil data kategori program berdasarkan masing-masing program
            $categories = [];
            foreach ($programs as $program) {
                $programCategories = DB::select(
                    'EXEC pknow_getDataKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                    [
                        1, '', '[Nama Kategori] ASC', '', $program->Key,
                        null, null, null, null, null, null, null, null, null, null, 
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null,
                    ]
                );
                error_log('Hasil program categories untuk program ' . $program->Key . ': ' . json_encode($programCategories));

                // Tangani data kosong
                if (isset($programCategories[0]->Message) && $programCategories[0]->Message === 'data kosong') {
                    error_log('Data kosong untuk Program Key: ' . $program->Key);
                    continue;
                }

                // Validasi hasil dan merge
                if (is_array($programCategories) && !empty($programCategories)) {
                    error_log('Sebelum merge: ' . json_encode($categories));
                    $categories = array_merge($categories, $programCategories);
                    error_log('Setelah merge: ' . json_encode($categories));
                } else {
                    error_log('Program Categories kosong atau bukan array: ' . json_encode($programCategories));
                }
            }
            error_log('Categories final: ' . json_encode($categories));


    
            // 6. Kembalikan view dengan data yang sudah didapatkan
            return view('page.master-pic-kk.KelolaProgram.KelolaProgram', [
                'userData' => $userData[0],   // Data pengguna
                'dataKK' => $dataKK[0],      // Data kelompok keahlian
                'programs' => $programs,     // Data program
                'categories' => collect($categories), // Data kategori program
                'kkeId' => $dataKK[0]->Key,  // Pastikan kkeId dikirim ke view
            ]);
            
        } catch (\Exception $e) {
            // Tangani error dengan mengembalikan ke view error
            return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => $e->getMessage()]);
        }
    }
    
    public function detailKelompokKeahlian($role, $kkeId)
    {

        try {
            $userId = Cookie::get('usr_id'); // Pastikan cookie 'usr_id' sudah diset saat login
            if (!$userId) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'User ID tidak ditemukan di cookie.']);
            }
    
            // 2. Ambil data user menggunakan stored procedure
            $userData = DB::select(
                'EXEC pknow_getDataUserLogin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $userId, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            error_log('Data user: ' . json_encode($userData));
            if (empty($userData)) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'Data pengguna tidak ditemukan.']);
            }
            // Ambil kry_id dari data pengguna
            $kryUsername = $userData[0]->Username;
            error_log('kryid: ' . $kryUsername);

    
            // Ambil data kelompok keahlian berdasarkan username
            $dataKK = DB::select(
                'EXEC pknow_getDataKelompokKeahlianByPIC ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $kryUsername, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (empty($dataKK)) {
                throw new \Exception('Data kelompok keahlian tidak ditemukan.');
            }
            error_log('datkak: ' . json_encode($dataKK));
            // Cari kelompok keahlian yang sesuai dengan kkeId
            $kelompokKeahlian = collect($dataKK)->firstWhere('Key', $kkeId);
            if (!$kelompokKeahlian) {
                throw new \Exception('Kelompok keahlian tidak ditemukan.');
            }

            $anggota = DB::select(
                'EXEC pknow_getListAnggotaKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    1, '', '[Nama Anggota] ASC', 'Aktif', $kkeId, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            error_log('angg: ' . json_encode($anggota));
            if (empty($anggota) || (isset($anggota[0]->Message) && $anggota[0]->Message === 'data kosong')) {
                $anggota = []; // Jika kosong, set ke array kosong
            }
    
            // Ambil data program berdasarkan kkeId
            $programs = DB::select(
                'EXEC pknow_getDataProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    1, '', '[Nama Program] ASC', 'Aktif', $kkeId, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            error_log('program: ' . json_encode($programs));
            if (empty($programs) || (isset($programs[0]->Message) && $programs[0]->Message === 'data kosong')) {
                $programs = []; // Jika kosong, set ke array kosong
            }
    
            // Ambil data kategori program untuk setiap program
            $categories = [];
            foreach ($programs as $program) {
                $programCategories = DB::select(
                    'EXEC pknow_getDataKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                    [
                        1, '', '[Nama Kategori] ASC', '', $program->Key,
                        null, null, null, null, null, null, null, null, null, null, 
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null, null, null, null, null, null,
                        null, null, null, null, null,
                    ]
                );
            
                if (!empty($programCategories) && !(isset($programCategories[0]->Message) && $programCategories[0]->Message === 'data kosong')) {
                    $categories[$program->Key] = $programCategories; // Pastikan ini berjalan dengan benar
                }
            }
            error_log('Program Key: ' . $program->Key);
            error_log('Hasil Stored Procedure: ' . json_encode($programCategories));

            error_log('kategori: ' . json_encode($categories));
            // Return ke view dengan data yang sudah diambil
            return view('page.master-pic-kk.KelolaProgram.DetailKK', [
                'role' => $role,
                'kkeId' => $kkeId,
                'kelompokKeahlian' => $kelompokKeahlian, // Pastikan ini dikirim ke Blade
                'anggota' => $anggota,
                'programs' => $programs,
                'categories' => $categories,
            ]);
            
        } catch (\Exception $e) {
            // Tampilkan error jika ada masalah
            return view('page.master-pic-kk.KelolaProgram.DetailKK', ['error' => $e->getMessage()]);
        }
    }
    
    public function tambahProgramForm($role, $kkeId)
    {
        try {
            // Ambil informasi dari session/cookie jika diperlukan
            $userId = Cookie::get('usr_id');
            $role = Cookie::get('role');
    
            // Pastikan user memiliki akses
            if (!$userId || !$role) {
                throw new \Exception('Akses ditolak. Silakan login kembali.');
            }
            error_log('KELOMPOK: ' . $kkeId);
            error_log('ROLE: ' . $role);
            // Kirim data ke view
            return view('page.master-pic-kk.KelolaProgram.AddProgram', [
                'role' => $role,
                'kkeId' => $kkeId,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    

    public function tambahProgramSubmit(Request $request)
    {
        try {
            $request->validate([
                'gambar' => 'nullable|file|mimes:png|max:10240', // Validasi file gambar (10 MB max)
                'nama_program' => 'required|string|max:255',
                'deskripsi' => 'required|string|max:1000',
            ]);

            $userId = Cookie::get('usr_id'); // Pastikan cookie 'usr_id' sudah diset saat login
            if (!$userId) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'User ID tidak ditemukan di cookie.']);
            }
    
            // 2. Ambil data user menggunakan stored procedure
            $userData = DB::select(
                'EXEC pknow_getDataUserLogin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $userId, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            error_log('Punya user: ' . json_encode($userData));
            if (empty($userData)) {
                return view('page.master-pic-kk.KelolaProgram.KelolaProgram', ['error' => 'Data pengguna tidak ditemukan.']);
            }
            // Ambil kry_id dari data pengguna
            $kryId = $userData[0]->kry_id;

            // Ambil data dari input form
            $kkeId = $request->input('kke_id');
            $namaProgram = $request->input('nama_program');
            $deskripsi = $request->input('deskripsi');


            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
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
            // Panggil Stored Procedure
            $result = DB::select(
                'EXEC pknow_createProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $kkeId, $kryId, $namaProgram, $deskripsi, $gambarPath, $userId, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            
            // Redirect ke halaman sebelumnya dengan pesan sukses
            return redirect()->route('kelola_program.program', ['role' => Cookie::get('role')])
            ->with('success', 'Program berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteProgram(Request $request, $role)
    {
        $programKey = $request->input('programKey');
        
        // Ambil role dari cookie jika tidak disediakan di parameter
        $roleFromCookie = Cookie::get('role');
    
        try {
            // Validasi role jika diperlukan
            if ($role !== $roleFromCookie) {
                return response()->json(['success' => false, 'message' => 'Role tidak sesuai.']);
            }
            
            $delete = DB::select(
                'EXEC pknow_deleteProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $programKey, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            error_log('delete result: ' . json_encode($delete));
    
            return response()->json(['success' => true, 'message' => 'Program berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus program: ' . $e->getMessage()]);
        }
    }

    public function sendProgram(Request $request, $role)
    {
        $programKey = $request->input('programKey');
        
        // Ambil role dari cookie jika tidak disediakan di parameter
        $roleFromCookie = Cookie::get('role');
    
        try {
            // Validasi role jika diperlukan
            if ($role !== $roleFromCookie) {
                return response()->json(['success' => false, 'message' => 'Role tidak sesuai.']);
            }

            $sent = DB::select(
                'EXEC pknow_setStatusProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $programKey, 'Aktif', $roleFromCookie, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            error_log('sent result: ' . json_encode($sent));
    
    
            return response()->json(['success' => true, 'message' => 'Program berhasil diubah menjadi aktif.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim program: ' . $e->getMessage()]);
        }
    }

    public function edit($role, $programKey)
    {
        try {
            error_log('program id: ' . $programKey);
            error_log('rrrole: ' . $role);
            // Ambil data program berdasarkan key
            $programs = DB::select(
                'EXEC pknow_getProgramById ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $programKey, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (empty($programs)) {
                return redirect()->back()->withErrors('Program tidak ditemukan!');
            }

            error_log('yang ini: ' . json_encode($programs));
            return view('page.master-pic-kk.KelolaProgram.EditProgram', [
                'program' => $programs[0],
                'role' => $role,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
        

    public function update(Request $request, $role, $programKey)
    {
        // Debug awal
        $usr_id = Cookie::get('usr_id');
        $kke_id = $request->input('kke_id');
        error_log('KKiD: ' . $kke_id);
        error_log('User ID: ' . $usr_id);
        error_log('Program Key: ' . $programKey);
        error_log('KK Key: ' . $kke_id);
    
        try {
            // Validasi input
            $status = $request->input('status', 'Draft');
            $request->merge(['status' => $status]);
            $validated = $request->validate([
                'nama_program' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'status' => 'required|in:Draft,Aktif',
                'gambar' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            ]);
    
            // Debug hasil validasi
            error_log('Nama Program: ' . $validated['nama_program']);
            error_log('Deskripsi: ' . $validated['deskripsi']);
            error_log('Status: ' . $validated['status']);
    
            // Proses upload gambar (jika ada)
            $filePath = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'file/' . $fileName;
                $file->move(public_path('file'), $fileName);
                error_log('Uploaded File Path: ' . $filePath);
            } else {
                error_log('No file uploaded.');
            }
    
            // Debug data sebelum memanggil stored procedure
            error_log('Calling Stored Procedure: pknow_editProgram');
            error_log('Params:');
            error_log(' - Program Key: ' . $programKey);
            error_log(' - Nama Program: ' . $validated['nama_program']);
            error_log(' - Deskripsi: ' . $validated['deskripsi']);
            error_log(' - Status: ' . $validated['status']);
            error_log(' - File Path: ' . ($filePath ?? 'No Change'));
            error_log(' - User ID: ' . $usr_id);
    
            
            $result = DB::select(
                'EXEC pknow_editProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $programKey, $kke_id, $validated['nama_program'], $validated['deskripsi'], $validated['status'], $filePath ?? '', $usr_id, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            // Debug hasil stored procedure
            error_log('Stored Procedure Result: ' . json_encode($result));
    
            // Redirect dengan pesan sukses
            return redirect()->route('kelola_program.program', ['role' => Cookie::get('role')])
                ->with('success', 'Program berhasil diperbarui!');
        } catch (\Exception $e) {
            // Debug error jika terjadi exception
            error_log('Error: ' . $e->getMessage());
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function tambahKategoriForm($role, $programId)
    {
        try {
            // Periksa akses user
            $userId = Cookie::get('usr_id');
            if (!$userId) {
                throw new \Exception('Akses ditolak. Silakan login kembali.');
            }
    
            // Ambil data program berdasarkan programId
            $programData = DB::select(
                'EXEC pknow_getProgramById ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', 
                [
                    $programId, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (empty($programData)) {
                throw new \Exception('Program tidak ditemukan.');
            }
    
            $programName = $programData[0]->{"Nama Program"}; // Ambil nama program
            $programId = $programData[0]->{"Key"}; // Ambil ID program
    
            return view('page.master-pic-kk.KelolaProgram.AddKategoriProgram', [
                'role' => $role,
                'programId' => $programId, // Kirim programId ke view
                'programName' => $programName, // Kirim nama program ke view
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    

    public function tambahKategoriSubmit(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'program_id' => 'required|string|max:255', // Program ID
                'nama_kategori' => 'required|string|max:255', // Nama Kategori
                'deskripsi_kategori' => 'required|string|max:1000', // Deskripsi Kategori
            ]);
    
            // Ambil data user
            $userId = Cookie::get('usr_id'); // User ID untuk @p4
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Panggil Stored Procedure
            $result = DB::select(
                'EXEC pknow_createKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $request->input('program_id'), $request->input('nama_kategori'), $request->input('deskripsi_kategori'), $userId, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            // Cek hasil dari stored procedure
            if (!isset($result[0]->hasil) || $result[0]->hasil !== 'OK') {
                throw new \Exception('Gagal menyimpan kategori.');
            }
    
            return redirect()->route('kelola_program.program', ['role' => Cookie::get('role')])
                ->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editKategori($role, $categoryKey)
    {
        try {
            // Ambil data kategori berdasarkan Key
            $category = DB::select(
                'EXEC pknow_getKategoriById ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $categoryKey, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (empty($category)) {
                return redirect()->back()->withErrors('Kategori tidak ditemukan!');
            }
    
            return view('page.master-pic-kk.KelolaProgram.EditKategoriProgram', [
                'role' => $role,
                'category' => $category[0],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    
    public function updateKategoriSubmit(Request $request, $categoryKey)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_kategori' => 'required|string|max:255',
                'deskripsi_kategori' => 'required|string|max:1000',
            ]);
    
            $userId = Cookie::get('usr_id');
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Eksekusi Stored Procedure
            $result = DB::select(
                'EXEC pknow_editKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $categoryKey, $request->input('program_id'), $request->input('nama_kategori'), $request->input('deskripsi_kategori'), $request->input('status'), $userId, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (!isset($result[0]->hasil) || $result[0]->hasil !== 'OK') {
                throw new \Exception('Gagal memperbarui kategori.');
            }
    
            return redirect()->route('kelola_program.program', ['role' => Cookie::get('role')])
                ->with('success', 'Kategori berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function sendCategory(Request $request, $role)
    {
        try {
            $categoryKey = $request->input('categoryKey'); // Ambil dari request body
            if (!$categoryKey) {
                throw new \Exception('Category Key tidak ditemukan.');
            }
    
            $roleFromCookie = Cookie::get('role'); // Ambil role dari cookie
            if ($role !== $roleFromCookie) {
                throw new \Exception('Role tidak sesuai.');
            }
    
            $userId = Cookie::get('usr_id');
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
            // Eksekusi Stored Procedure
            $result = DB::select(
                'EXEC pknow_setStatusKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $categoryKey, // @p1: ID Kategori
                    'Aktif',      // @p2: Status Baru
                    $userId,      // @p3: Modified By
                    null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
            if (isset($result[0]->hasil) && str_contains($result[0]->hasil, 'ERROR')) {
                throw new \Exception($result[0]->hasil);
            }
    
            return response()->json(['success' => true, 'message' => 'Kategori berhasil diubah menjadi Aktif!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    

    public function deleteCategory(Request $request, $role)
    {
        try {
            $categoryKey = $request->input('categoryKey'); // Ambil dari request body
            if (!$categoryKey) {
                throw new \Exception('Category Key tidak ditemukan.');
            }
    
            $roleFromCookie = Cookie::get('role'); // Ambil role dari cookie
            if ($role !== $roleFromCookie) {
                throw new \Exception('Role tidak sesuai.');
            }
    
            $userId = Cookie::get('usr_id');
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Eksekusi Stored Procedure
            $result = DB::select(
                'EXEC pknow_deleteKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $categoryKey, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (isset($result[0]->hasil) && str_contains($result[0]->hasil, 'Penghapusan tidak diizinkan')) {
                throw new \Exception($result[0]->hasil);
            }
    
            return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function toggleProgramStatus(Request $request)
    {
        try {
            $programKey = $request->input('id');
            $newStatus = $request->input('newStatus');
            $userId = Cookie::get('usr_id');
    
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Ambil status program dan kategori terkait
            $program = DB::selectOne(
                'SELECT prg_status AS Status FROM pknow_msprogram WHERE prg_id = ?',
                [$programKey]
            );
    
            if (!$program) {
                throw new \Exception('Program tidak ditemukan.');
            }
    
            // Jika program akan dinonaktifkan, pastikan tidak ada kategori aktif
            if ($newStatus === 'Tidak Aktif') {
                $activeCategories = DB::select(
                    'SELECT COUNT(*) AS ActiveCount FROM pknow_mskategoriprogram WHERE prg_id = ? AND kat_status = ?',
                    [$programKey, 'Aktif']
                );
    
                if ($activeCategories[0]->ActiveCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Program memiliki kategori aktif. Nonaktifkan kategori terlebih dahulu.'
                    ]);
                }
            }
    
            // Ubah status program
            $result = DB::select(
                'EXEC pknow_setStatusProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $programKey, $newStatus, $userId, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (isset($result[0]->hasil) && str_contains($result[0]->hasil, 'ERROR')) {
                throw new \Exception($result[0]->hasil);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Status program berhasil diperbarui!',
                'newStatus' => $newStatus, // Kembalikan status baru untuk keperluan front-end
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    
    
    public function toggleCategoryStatus(Request $request)
    {
        try {
            $categoryKey = $request->input('id');
            $newStatus = $request->input('newStatus');
            $userId = Cookie::get('usr_id');
    
            if (!$userId) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Ambil data kategori dan status program terkait
            $category = DB::selectOne(
                'SELECT k.kat_status AS Status, p.prg_status AS ProgramStatus
                 FROM pknow_mskategoriprogram k
                 JOIN pknow_msprogram p ON k.prg_id = p.prg_id
                 WHERE k.kat_id = ?',
                [$categoryKey]
            );
    
            if (!$category) {
                throw new \Exception('Kategori tidak ditemukan.');
            }
    
            // Validasi perubahan status berdasarkan status program
            if ($newStatus === 'Aktif' && $category->ProgramStatus !== 'Aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengaktifkan kategori karena program terkait tidak aktif.'
                ]);
            }
    
            // Ubah status kategori
            $result = DB::select(
                'EXEC pknow_setStatusKategoriProgram ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $categoryKey, $newStatus, $userId, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                    null, null, null, null, null, null, null, null, null, null,
                ]
            );
    
            if (isset($result[0]->hasil) && str_contains($result[0]->hasil, 'ERROR')) {
                throw new \Exception($result[0]->hasil);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Status kategori program berhasil diperbarui!',
                'newStatus' => $newStatus, // Kembalikan status baru untuk keperluan front-end
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
        
}