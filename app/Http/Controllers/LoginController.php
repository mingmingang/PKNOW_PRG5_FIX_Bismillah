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
       
        $input = $request->input('username'); 

        $inputCaptcha = $request->input('captcha');
        Log::info("Input Captcha: $inputCaptcha");
        $sessionCaptcha = session('captcha');
        Log::info("Session Captcha: $sessionCaptcha");

        // Validasi captcha
        if ($inputCaptcha !== $sessionCaptcha) {
            return back()->with('error', 'Captcha tidak valid.');
        }
        // contoh inputan user
        
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
            session(['showRoleSelectionModal' => true]);
            session()->forget('captcha');


            Cookie::queue('usr_id', $roles[0]['username'], 60 * 24);
            Cookie::queue('role', $roles[0]['name'], 60 * 24);
            Cookie::queue('pengguna', $roles[0]['pengguna'], 60 * 24);
    
            return view('page.login.Index')->with('roles', $roles);
        } else {
            session()->flash('error', 'Login gagal, data tidak ditemukan');
            return view('page.login.Index');
        }
    }
    

    public function clearRoleSession(Request $request)
    {
        // Hapus data session
        session()->forget('showRoleSelectionModal');
        session()->forget('roles');
    
        // Hapus cookie yang berkaitan dengan pengguna
        // Cookie::queue(Cookie::forget('usr_id'));
        // Cookie::queue(Cookie::forget('role'));
        // Cookie::queue(Cookie::forget('pengguna'));
    
        return response()->json(['status' => 'success']);
    }



}