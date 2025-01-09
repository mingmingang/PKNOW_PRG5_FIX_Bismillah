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

class PengajuanKKController extends Controller
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

        return view('page.master-tenaga-pendidik.PengajuanAnggotaKeahlian.PengajuanAnggotaKeahlian', compact('dataFilterSort', 'dataFilterStatus', 'roles'));
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
            $request->input('status', 'Aktif'),            // @p4: Filter status
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        ];

        try {
            // Panggil stored procedure
            $data = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            // Jika hasil kosong
            if (count($data) === 0) {
                // return response()->json(['message' => 'Tidak ada data.'], 404);
                return view('page.master-tenaga-pendidik.PengajuanAnggotaKeahlian.PengajuanAnggotaKeahlian', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
            }

            // Return hasil sebagai JSON
            return view('page.master-tenaga-pendidik.PengajuanAnggotaKeahlian.PengajuanAnggotaKeahlian', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function gabung(Request $request, $role, $id)
    {
        $data = DB::table('pknow_mskelompokkeahlian as kk')
            ->leftJoin('sia_msprodi as pro', 'kk.pro_id', '=', 'pro.pro_id')
            ->select('kk.*', 'pro.pro_nama')
            ->where('kk.kke_id', $id)
            ->first();
    
        if (!$data) {
            return redirect()->route('pengajuan_kk', ['role' => urlencode($role)])
                ->with('error', 'Data tidak ditemukan.');
        }
    
        return view('page.master-tenaga-pendidik.PengajuanAnggotaKeahlian.GabungKelompokKeahlian', compact('data', 'role'));
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
    
    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'kke_id' => 'required|string', // ID Kelompok Keahlian
        'lampiran.*' => 'file|mimes:pdf|max:10240', // Validasi file PDF maksimal 10MB
    ]);

    try {
        // Ambil user ID dari cookies
        $usr_id = Cookie::get('usr_id');
        if (!$usr_id) {
            throw new \Exception('User ID tidak ditemukan di cookies.');
        }

        // Ambil kry_id berdasarkan usr_id
        $karyawan = DB::table('ERP_PolmanAstra.dbo.ess_mskaryawan')
            ->where('kry_username', $usr_id)
            ->where('kry_status', 'Aktif')
            ->first();

        if (!$karyawan) {
            throw new \Exception('Data karyawan tidak ditemukan untuk user yang login.');
        }

        $kry_id = $karyawan->kry_id;

        // Dapatkan parameter input
        $kke_id = $request->input('kke_id'); // ID kelompok keahlian
        $status = 'Menunggu Acc'; // Default status untuk pengajuan
        $created_by = $usr_id; // Creator user ID

        // Upload lampiran
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran')[0];
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('file');
            $file->move($destinationPath, $fileName);
            $lampiranPath = 'file/' . $fileName;
        }

        // Panggil stored procedure
        $result = DB::select('EXEC pknow_createAnggotaKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
            $kke_id,     // @p1: kke_id
            $kry_id,     // @p2: kry_id
            $status,     // @p3: akk_status
            $created_by, // @p4: akk_created_by
            $lampiranPath, // @p5: lampiran
            ...array_fill(5, 45, null), // Sisa parameter diisi null
        ]);

        // Handle hasil
        if (!empty($result) && isset($result[0]->hasil)) {
            return redirect()->route('pengajuan_kk')->with('success', 'Pengajuan berhasil dikirim!');
        } else {
            throw new \Exception('Gagal menyimpan data pengajuan.');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function detailKK(Request $request, $role, $id)
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
        return redirect()->route('pengajuan_kk', ['role' => urlencode($role)])
            ->with('error', 'Data tidak ditemukan.');
    }

    // 2. Siapkan parameter untuk stored procedure pknow_getDataProgram
    $paramsProgram = [
        '1', // @p1: Halaman (misalnya, 1)
        '', // @p2: Query pencarian (kosong untuk semua)
        '[Nama Program] ASC', // @p3: Sortir berdasarkan Nama Program secara ascending
        'Aktif', // @p4: Status filter (hanya Aktif)
        $id, // @p5: kke_id
    ];

    // Tambahkan 45 parameter kosong untuk mencapai total 50 parameter
    for ($i = 6; $i <= 50; $i++) {
        $paramsProgram[] = null;
    }

    // 3. Eksekusi stored procedure pknow_getDataProgram
    $programData = DB::select('EXEC pknow_getDataProgram ' . implode(', ', array_fill(0, 50, '?')), $paramsProgram);

    // 4. Cek apakah ada data program
    if (count($programData) === 0 || (count($programData) === 1 && isset($programData[0]->Message))) {
        $listProgram = [];
    } else {
        $listProgram = $programData;
    }

    // 5. Untuk setiap program, ambil kategori yang aktif menggunakan stored procedure pknow_getDataKategoriProgram
    $listProgramWithKategori = [];

    foreach ($listProgram as $program) {
        $paramsKategori = [
            '1', // @p1: Halaman (misalnya, 1)
            '', // @p2: Query pencarian (kosong untuk semua)
            '[Nama Kategori] ASC', // @p3: Sortir berdasarkan Nama Kategori secara ascending
            'Aktif', // @p4: Status filter (hanya Aktif)
            $program->Key, // @p5: prg_id (menggunakan 'Key' yang merupakan prg_id)
        ];

        // Tambahkan 45 parameter kosong untuk mencapai total 50 parameter
        for ($i = 6; $i <= 50; $i++) {
            $paramsKategori[] = null;
        }

        // Eksekusi stored procedure pknow_getDataKategoriProgram
        $kategoriData = DB::select('EXEC pknow_getDataKategoriProgram ' . implode(', ', array_fill(0, 50, '?')), $paramsKategori);

        // Cek apakah ada data kategori
        if (count($kategoriData) === 0 || (count($kategoriData) === 1 && isset($kategoriData[0]->Message))) {
            $listKategori = [];
        } else {
            $listKategori = $kategoriData;
        }

        // Tambahkan program dan kategorinya ke dalam listProgramWithKategori
        $listProgramWithKategori[] = [
            'program' => $program,
            'kategori' => $listKategori,
        ];
    }

    // 6. Kirim data ke view
    return view('page.master-tenaga-pendidik.PengajuanAnggotaKeahlian.DetailKK', compact('data', 'role', 'listProgramWithKategori'));
}

}
