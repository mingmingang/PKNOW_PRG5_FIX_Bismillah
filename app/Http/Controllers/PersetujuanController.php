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

class PersetujuanController extends Controller
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

        return view('page.master-prodi.PersetujuanAnggotaKK.PersetujuanAnggotaKK', compact('dataFilterSort', 'dataFilterStatus', 'roles'));
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
            $request->input('page', 1),
            $request->input('query', ''),
            $request->input('sort', '[Nama Kelompok Keahlian] ASC'),
            $request->input('status', 'Aktif'),
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        ];
    
        try {
            // Panggil stored procedure
            $data = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
    
            // Hitung jumlah anggota
            foreach ($data as &$item) {
                $anggotaAktif = DB::table('pknow_msanggotakeahlian')
                    ->where('kke_id', $item->Key)
                    ->where('akk_status', 'Aktif')
                    ->count();
    
                $menungguAcc = DB::table('pknow_msanggotakeahlian')
                    ->where('kke_id', $item->Key)
                    ->where('akk_status', 'Menunggu Acc')
                    ->count();
    
                $item->AnggotaAktif = $anggotaAktif;
                $item->MenungguAcc = $menungguAcc;
            }
    
            return view('page.master-prodi.PersetujuanAnggotaKK.PersetujuanAnggotaKK', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    

    public function detailPersetujuan(Request $request, $role, $id)
    {
        // 1. Ambil data Kelompok Keahlian berdasarkan kke_id, termasuk nama PIC
    $data = DB::table('pknow_mskelompokkeahlian as kk')
    ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
    ->leftJoin('ERP_PolmanAstra.dbo.ess_mskaryawan as kar', 'kk.kry_id', '=', 'kar.kry_id') // Join dengan tabel karyawan
    ->select(
        'kk.*',
        'pro.pro_nama',
        DB::raw("kar.kry_nama_depan + ' ' + kar.kry_nama_blkg as pic_nama") // Alias untuk nama PIC
    )
    ->where('kk.kke_id', $id)
    ->first();

if (!$data) {
    return redirect()->route('persetujuan', ['role' => urlencode($role)])
        ->with('error', 'Data tidak ditemukan.');
}


    // Ambil data Kelompok Keahlian
    $data = DB::table('pknow_mskelompokkeahlian as kk')
        ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
        ->leftJoin('ERP_PolmanAstra.dbo.ess_mskaryawan as kar', 'kk.kry_id', '=', 'kar.kry_id')
        ->select(
            'kk.*',
            'pro.pro_nama',
            DB::raw("kar.kry_nama_depan + ' ' + kar.kry_nama_blkg as pic_nama")
        )
        ->where('kk.kke_id', $id)
        ->first();

    if (!$data) {
        return redirect()->route('persetujuan', ['role' => urlencode($role)])
            ->with('error', 'Data tidak ditemukan.');
    }

    // Ambil data anggota dengan status "Menunggu Acc"
    $anggotaMenungguAcc = DB::table('pknow_msanggotakeahlian as akk')
        ->join('ERP_PolmanAstra.dbo.ess_mskaryawan as kar', 'akk.kry_id', '=', 'kar.kry_id')
        ->select(
            'akk.akk_id',
            'akk.kry_id',
            DB::raw("kar.kry_nama_depan + ' ' + kar.kry_nama_blkg as nama"),
            'akk.akk_status'
        )
        ->where('akk.kke_id', $id)
        ->where('akk.akk_status', 'Menunggu Acc')
        ->get();

    // Ambil data anggota dengan status "Aktif"
    $anggotaAktif = DB::table('pknow_msanggotakeahlian as akk')
        ->join('ERP_PolmanAstra.dbo.ess_mskaryawan as kar', 'akk.kry_id', '=', 'kar.kry_id')
        ->select(
            'akk.akk_id',
            'akk.kry_id',
            DB::raw("kar.kry_nama_depan + ' ' + kar.kry_nama_blkg as nama"),
            'akk.akk_status'
        )
        ->where('akk.kke_id', $id)
        ->where('akk.akk_status', 'Aktif')
        ->get();

    // Ambil data lampiran
    $lampiran = DB::table('pknow_mslampirananggotakeahlian as la')
        ->join('pknow_msanggotakeahlian as akk', 'la.akk_id', '=', 'akk.akk_id')
        ->select('la.lak_id', 'la.lak_lampiran', 'akk.kry_id', 'akk.akk_id')
        ->where('akk.kke_id', $id)
        ->get();



// 6. Kirim data ke view
return view('page.master-prodi.PersetujuanAnggotaKK.DetailPersetujuan', compact('data', 'anggotaMenungguAcc', 'anggotaAktif', 'lampiran','role'));
    
       
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
    
    public function updateStatusAnggota(Request $request)
    {
        $request->validate([
            'akk_id' => 'required|string', // ID Anggota Keahlian
            'new_status' => 'required|string|in:Aktif,Ditolak', // Status baru
        ]);
    
        try {
            // Periksa User ID dari cookies
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan.');
            }
    
            // Eksekusi stored procedure untuk mengubah status
            $result = DB::select('EXEC pknow_setStatusAnggotaKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
                $request->input('akk_id'),    // @p1
                $request->input('new_status'), // @p2
                $usr_id,                      // @p3
                ...array_fill(3, 47, null),   // Sisanya null
            ]);
    
            // Ambil hasil eksekusi prosedur
            if (!empty($result) && isset($result[0]->Status)) {
                return redirect()->back()->with('success', 'Status berhasil diperbarui menjadi: ' . $result[0]->Status);
            } else {
                throw new \Exception('Gagal memperbarui status anggota.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDetailLampiran(Request $request)
{
    try {
        $params = [
            $request->input('page', 1),       // @p1: Halaman saat ini
            $request->input('sort', '[ID Lampiran] ASC'), // @p2: Urutkan berdasarkan ID Lampiran
            $request->input('akk_id', ''),   // @p3: ID Anggota Keahlian
            ...array_fill(3, 47, null)       // Parameter lainnya diisi NULL
        ];

        // Panggil stored procedure
        $data = DB::select('EXEC pknow_getDetailLampiran ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

        // Kembalikan hasil dalam format JSON
        return response()->json(['success' => true, 'data' => $data], 200);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

}
