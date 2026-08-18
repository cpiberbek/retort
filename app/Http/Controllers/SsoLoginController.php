<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SsoLoginController extends Controller
{
    public function login(Request $request)
    {
        $ticket = $request->ticket;

        if (!$ticket || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $ticket)) {
            return redirect('/login')->withErrors([
                'sso' => 'Tautan login otomatis tidak valid.',
            ]);
        }

        $response = Http::withToken(config('services.employee_api.sso_secret'))
            ->timeout(10)
            ->post(config('services.employee_api.url') . '/sso/verify', [
                'ticket' => $ticket,
                'project_uuid' => config('services.employee_api.this_project_uuid'),
            ]);

        if ($response->status() === 403) {
            return redirect('/login')->withErrors([
                'sso' => 'Anda tidak memiliki akses ke sistem ini.',
            ]);
        }

        if ($response->failed()) {
            return redirect('/login')->withErrors([
                'sso' => 'Sesi login otomatis tidak valid atau sudah kedaluwarsa. Silakan login manual.',
            ]);
        }

        $remoteUser = $response->json('user');

        if (empty($remoteUser['uuid'])) {
            return redirect('/login')->withErrors([
                'sso' => 'Sesi login otomatis tidak valid.',
            ]);
        }

        $user = User::where('uuid', $remoteUser['uuid'])->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'sso' => 'Akun tidak ditemukan di sistem ini.',
            ]);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/Dashboard');
    }
}