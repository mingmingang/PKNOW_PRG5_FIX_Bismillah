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

class RiwayatPengajuanController extends Controller
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

        return view('page.master-tenaga-pendidik.RiwayatPengajuan.RiwayatPengajuan', compact('dataFilterSort', 'dataFilterStatus', 'roles'));
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
    
        // Ambil user ID dari cookies
        $usr_id = Cookie::get('usr_id');
        if (!$usr_id) {
            return response()->json([
                'error' => 'User ID tidak ditemukan di cookies.',
            ], 400);
        }
    
        try {
            // Panggil stored procedure `pknow_getDataUserLogin` untuk mendapatkan kry_id
            $userData = DB::select('EXEC pknow_getDataUserLogin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $usr_id,
                ...array_fill(1, 49, null), // Sisa parameter diisi null
            ]);
    
            if (empty($userData) || !isset($userData[0]->kry_id)) {
                throw new \Exception('Data user tidak ditemukan atau tidak valid.');
            }
    
            $kry_id = $userData[0]->kry_id;
    
            // Siapkan parameter untuk `pknow_getRiwayatPengajuan`
        $params = [
            $request->input('page', 1),                // @p1: Halaman saat ini
            $request->input('query', ''),             // @p2: Filter "Nama Kelompok Keahlian"
            $request->input('sort', '[Nama Kelompok Keahlian] ASC'), // @p3: Urutan
            $kry_id,                                  // @p4: kry_id dari user
            ...array_fill(4, 46, null),               // Sisa parameter diisi null
        ];

        // Panggil stored procedure `pknow_getRiwayatPengajuan`
        $data = DB::select('EXEC pknow_getRiwayatPengajuan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            // Jika hasil kosong
            if (count($data) === 0) {
                return view('page.master-tenaga-pendidik.RiwayatPengajuan.RiwayatPengajuan', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
            }
    
            // Return hasil ke view
            return view('page.master-tenaga-pendidik.RiwayatPengajuan.RiwayatPengajuan', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
 
    public function detail(Request $request, $role, $akk_id)
    {
        $detailData = DB::table('pknow_msanggotakeahlian as akk')
            ->join('pknow_mskelompokkeahlian as kk', 'akk.kke_id', '=', 'kk.kke_id')
            ->select(
                'akk.akk_id',
                'kk.kke_id',
                'kk.kke_nama',
                'akk.kry_id',
                'akk.akk_status'
            )
            ->where('akk.akk_id', $akk_id)
            ->first();
    
        if (!$detailData) {
            return redirect()->route('pengajuan_kk', ['role' => urlencode($role)])
                ->with('error', 'Detail data tidak ditemukan.');
        }
    
        // Ambil lampiran dan pecah menjadi array
        $lampiran = DB::table('pknow_mslampirananggotakeahlian')
            ->select('lak_lampiran')
            ->where('akk_id', $akk_id)
            ->get()
            ->pluck('lak_lampiran')
            ->toArray();
    
        // Gabungkan semua lampiran menjadi array terpisah
        $detailData->Lampiran = !empty($lampiran) ? explode(',', implode(',', $lampiran)) : [];
    
        return view('page.master-tenaga-pendidik.RiwayatPengajuan.Detail', compact('detailData', 'role'));
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
