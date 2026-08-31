<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['login_error' => 'Username atau password salah'])
                ->withInput(); // biar username tetap terisi
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // logoutSSO
    public function logout(Request $request)
    {
        $user = $request->user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            try {
                Http::withToken(config('services.employee_api.sso_secret'))
                    ->timeout(5)
                    ->post(config('services.employee_api.url') . '/sso/report-logout', [
                        'user_uuid' => $user->uuid,
                        'project_uuid' => config('services.employee_api.this_project_uuid'),
                    ]);
            } catch (\Throwable $e) {
            }
        }

        return redirect(config('services.employee_api.portal_url'));
    }

    // obsolete after sso implemented
    // public function logout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect()->route('login');
    // }
}
