<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuditModeRedirect
{
    private array $modules = [
        'mincing',
        'metal',
        'checklistmagnettrap',
        'pemeriksaan-kekuatan-magnet-trap',
        'stuffing',
        'labelisasi_pvdc',
        'pvdc',
        'wire',
        'washing',
        'pemasakan',
        'chamber',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $name = $request->route()?->getName();

        if (!$user || !$name) {
            return $next($request);
        }

        $auditView = $user->isAuditView();

        foreach ($this->modules as $m) {
            if ($name === "$m.index" && $auditView) {
                return redirect()->route("$m.audit");
            }

            if ($name === "$m.audit" && !$auditView) {
                return redirect()->route("$m.index");
            }
        }

        return $next($request);
    }
}