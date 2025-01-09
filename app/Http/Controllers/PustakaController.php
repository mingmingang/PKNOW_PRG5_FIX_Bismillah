<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;


class PustakaController extends Controller
{
    public function index()
    {
        $roles = session('roles');
        $dataFilterSort = [
            ['Value' => '[Judul] ASC', 'Text' => 'Judul Pustaka [↑]'],
            ['Value' => '[Judul] DESC', 'Text' => 'Judul Pustaka [↓]'],
        ];

        $dataFilterStatus = [
            ['Value' => 'Aktif', 'Text' => 'Aktif'],
            ['Value' => 'Tidak Aktif', 'Text' => 'Tidak Aktif'],
        ];

        return view('page.DaftarPustaka.Index', compact('dataFilterSort', 'dataFilterStatus', 'roles'));
    }

    public function getTempDataPustaka(Request $request)
    {
        $dataFilterSort = [
            ['Value' => '[Judul] ASC', 'Text' => 'Judul Pustaka [↑]'],
            ['Value' => '[Judul] DESC', 'Text' => 'Judul Pustaka [↓]'],
        ];

        $dataFilterStatus = [
            ['Value' => 'Aktif', 'Text' => 'Aktif'],
            ['Value' => 'Tidak Aktif', 'Text' => 'Tidak Aktif'],
        ];

        $params = [
            $request->input('page', 1),                // @p1: Halaman saat ini
            $request->input('query', ''),             // @p2: Filter "Nama Kelompok Keahlian"
            $request->input('sort', '[Judul] ASC'),   // @p3: Sort
            $request->input('status', ''),          // @p4: Filter status
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        try {
            // Panggil stored procedure
            $data = DB::select('EXEC pknow_getTempDataPustaka ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            // Jika hasil kosong
            if (count($data) === 0) {
                // return response()->json(['message' => 'Tidak ada data.'], 404);
                return view('page.DaftarPustaka.Index', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
            }

            // Return hasil sebagai JSON
            return view('page.DaftarPustaka.Index', compact('dataFilterSort', 'dataFilterStatus'))->with('data', $data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request, $role)
    {
        // Pastikan parameter role tidak memblokir akses
        if (!$role) {
            return redirect()->route('daftar_pustaka')->with('error', 'Role tidak valid');
        }
        // Panggil stored procedure atau query langsung
        $kelompokKeahlian = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
            1,
            '',
            '[Nama Kelompok Keahlian] ASC',
            'Aktif',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        return view('page.DaftarPustaka.KelolaDaftarPustaka.TambahDaftarPustaka', compact('kelompokKeahlian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pus_judul' => 'required|max:30',
            'pus_kata_kunci' => 'required',
            'pus_keterangan' => 'required',
            'pus_file' => 'required|file|mimes:pdf,docx,xlsx,pptx,mp4|max:20480',
            'pus_gambar' => 'nullable|image|mimes:png|max:10240',
            'kke_id' => 'required',
        ]);

        try {
            // Ambil usr_id dari cookies
            $usr_id = Cookie::get('usr_id');
            $role = Cookie::get('role');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan di cookies.');
            }
            $status = "Aktif";
            $params = [
                $request->kke_id,
                $this->storeFile($request->file('pus_file'), 'uploads/pustaka'),
                $request->pus_judul,
                $request->pus_keterangan,
                $request->pus_kata_kunci,
                $this->storeFile($request->file('pus_gambar'), 'uploads/images'),
                $status,
                $usr_id
            ];

            // Tambahkan parameter yang tidak diperlukan dengan NULL hingga mencapai 50 parameter
            $params = array_merge($params, array_fill(count($params), 50 - count($params), null));

            // Log isi params untuk debugging
            error_log("Jumlah parameter yang dikirim: " . count($params)); // Harus 50
            error_log("Parameter yang dikirim: " . print_r($params, true));

            // Eksekusi stored procedure
            $result = DB::select('EXEC pknow_createPustaka ' . implode(', ', array_fill(0, 50, '?')), $params);

            return redirect()->route('daftar_pustaka', ['role' => $role])->with('success', 'Daftar pustaka berhasil ditambahkan.');
        } catch (\Exception $e) {
            error_log("Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function storeFile($file, $path)
    {
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($path), $fileName);
            return $path . '/' . $fileName;
        }
        return null;
    }

    public function edit(Request $request, $role, $id)
    {
        // Ambil data berdasarkan ID
        $data = DB::table('pknow_mspustaka')
            ->where('pus_id', $id)
            ->first();

        // Pastikan parameter role tidak memblokir akses
        if (!$role) {
            return redirect()->route('daftar_pustaka')->with('error', 'Role tidak valid');
        }

        // Ambil data kelompok keahlian untuk dropdown
        $kelompokKeahlian = DB::select('EXEC pknow_getTempDataKelompokKeahlian ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
            1,
            '',
            '[Nama Kelompok Keahlian] ASC',
            'Aktif',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('page.DaftarPustaka.KelolaDaftarPustaka.EditDaftarPustaka', compact('data', 'kelompokKeahlian'));
    }

    public function lihat(Request $request, $role, $id)
    {
        // Ambil data berdasarkan ID
        $data = DB::table('pknow_mspustaka')
            ->where('pus_id', $id)
            ->first();

        // Pastikan parameter role tidak memblokir akses
        if (!$role) {
            return redirect()->route('daftar_pustaka')->with('error', 'Role tidak valid');
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('page.DaftarPustaka.KelolaDaftarPustaka.LihatDaftarPustaka', compact('data'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'pus_judul' => 'required|max:30',
            'pus_kata_kunci' => 'required',
            'pus_keterangan' => 'required',
            'pus_file' => 'nullable|file|mimes:pdf,docx,xlsx,pptx,mp4|max:20480',
            'pus_gambar' => 'nullable|image|mimes:png|max:10240',
            'kke_id' => 'required',
        ]);

        try {
            // Ambil usr_id dari cookies
            $usr_id = Cookie::get('usr_id');
            $role = Cookie::get('role');
            if (!$usr_id) {
                throw new \Exception('User ID tidak ditemukan di cookies.');
            }

            // Ambil data pustaka lama
            $oldData = DB::table('pknow_mspustaka')->where('pus_id', $id)->first();

            // Tentukan file gambar yang akan digunakan
            $pus_gambar = $request->file('pus_gambar')
                ? $this->storeFile($request->file('pus_gambar'), 'uploads/images') // Gambar baru
                : $oldData->pus_gambar; // Gambar lama

            // Tentukan file pustaka yang akan digunakan
            $pus_file = $request->file('pus_file')
                ? $this->storeFile($request->file('pus_file'), 'uploads/pustaka') // File baru
                : $oldData->pus_file; // File lama

            $params = [
                $id,
                $request->pus_judul,
                $request->kke_id,
                $pus_file,
                $request->pus_keterangan,
                $request->pus_kata_kunci,
                $pus_gambar, // Gambar baru atau lama
                'Aktif',
                $usr_id,
                ...array_fill(9, 41, null),
            ];

            DB::select('EXEC pknow_editPustaka ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);
            return redirect()->route('daftar_pustaka', ['role' => $role])->with('success', 'Daftar pustaka berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }




    public function deleteDaftarPustaka($id)
    {
        try {
            // Jalankan stored procedure
            $result = DB::select(
                'EXEC pknow_deletePustaka ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                array_merge([$id], array_fill(1, 49, null))
            );

            // Cek hasil dari stored procedure
            if (!empty($result) && $result[0]->hasil === 'OK') {
                return response()->json(['success' => true, 'message' => 'Daftar Pustaka berhasil dihapus.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus Daftar Pustaka.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function setStatusPustaka(Request $request, $id)
    {
        try {
            // Ambil usr_id dari cookies untuk parameter modif_by
            $usr_id = Cookie::get('usr_id');
            if (!$usr_id) {
                return response()->json(['success' => false, 'message' => 'User ID tidak ditemukan di cookies.'], 400);
            }

            // Panggil stored procedure untuk update status
            $result = DB::select(
                'EXEC pknow_setStatusPustaka ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                array_merge([$id, $usr_id], array_fill(2, 48, null))
            );

            // Periksa apakah ada data yang dikembalikan
            if (!empty($result) && isset($result[0]->Status)) {
                $newStatus = $result[0]->Status;
                return response()->json(['success' => true, 'message' => "Status pustaka berhasil diubah menjadi {$newStatus}.", 'newStatus' => $newStatus], 200);
            }

            return response()->json(['success' => false, 'message' => 'Gagal mengubah status pustaka.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    public function search(Request $request, $role)
    {
        $dataFilterSort = [
            ['Value' => '[Judul] ASC', 'Text' => 'Judul Pustaka [↑]'],
            ['Value' => '[Judul] DESC', 'Text' => 'Judul Pustaka [↓]'],
        ];

        $dataFilterStatus = [
            ['Value' => 'Aktif', 'Text' => 'Aktif'],
            ['Value' => 'Tidak Aktif', 'Text' => 'Tidak Aktif'],
        ];

        try {
            $page = $request->input('page', 1);       
            $query = $request->input('query', '');    
            $sort = $request->input('sort', '[Judul] ASC');  
            $status = $request->input('status', '');  

            // Pastikan query diubah menjadi string kosong jika tidak ada input
            if ($query === null) {
                $query = '';
            }

            // Parameter untuk query stored procedure
            $params = [
                $page,           // @p1: Halaman
                $query,          // @p2: Filter "Nama Kelompok Keahlian"
                $sort,           // @p3: Urutan
                $status,         // @p4: Filter status
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
            ];

            // Panggil stored procedure dengan parameter
            $data = DB::select('EXEC pknow_getTempDataPustaka ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            // Return json data
            return response()->json($data);
        } catch (\Exception $e) {
            error_log("Exception occurred: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
