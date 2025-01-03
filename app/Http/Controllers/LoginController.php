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

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // session()->forget('showRoleSelectionModal');
        // session()->forget('roles');
    
        // Tangkap inputan user dari form
        $input = $request->input('username'); // contoh inputan user
        
        // Memanggil stored procedure dengan parameter
        $result = DB::select('EXEC sso_getAuthenticationKMS ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', [
            $input, null, null, null, null, null, null, null, null, null, 
            null, null, null, null, null, null, null, null, null, null, 
            null, null, null, null, null, null, null, null, null, null,
            null, null, null, null, null, null, null, null, null, null,
            null, null, null, null, null, null, null, null, null, null
        ]);
    
        // Periksa hasil query
        if (!empty($result)) {
            $roles = [];
            foreach ($result as $role) {
                $roles[] = [
                    'id' => isset($role->RoleID) ? $role->RoleID : null,  // Menghindari error jika kolom tidak ada
                    'name' => isset($role->Role) ? $role->Role : null, 
                    'pengguna' =>  isset($role->Nama) ? $role->Nama : null,
                    'username' => $input// Menghindari error jika kolom tidak ada
                ];
            }
    
            // Cek jika ada RoleID yang null dalam array roles
            $roleNull = collect($roles)->contains(function ($role) {
                return $role['id'] === null;
            });
    
            if ($roleNull) {
                session()->flash('error', 'Login gagal, data tidak ditemukan');
                return view('page.login.Index');
            }
    
            // Store roles in the session
            session(['roles' => $roles]);
    
            // Set flag in session to show role selection modal
            session(['showRoleSelectionModal' => true]);
    
            return view('page.login.Index')->with('roles', $roles);
        } else {
            session()->flash('error', 'Login gagal, data tidak ditemukan');
            return view('page.login.Index');
        }
    }
    

    public function clearRoleSession()
    {
        session()->forget('showRoleSelectionModal'); // Hapus flag modal
        session()->forget('roles'); // Hapus roles dari session

        return response()->json(['status' => 'success']);
    }


}