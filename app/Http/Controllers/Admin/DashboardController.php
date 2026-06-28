<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'ADMIN') {
            return redirect('/login');
        }

        $stats = DB::table('V_ADMIN_DASHBOARD')->first();

        $recentShipments = DB::table('V_RECENT_SHIPMENTS')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentShipments'));
    }
}
