<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuditModeController extends Controller
{
    public function toggle()
    {
        $user = Auth::user();

        $user->update(['mode_audit' => !$user->mode_audit]);

        return back();
    }
}