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

        $recentShipments = DB::table('SHIPMENT')
            ->join('CUSTOMER', 'SHIPMENT.customer_id', '=', 'CUSTOMER.customer_id')
            ->join('PORT as src', 'SHIPMENT.source_port_id', '=', 'src.port_id')
            ->join('PORT as dst', 'SHIPMENT.destination_port_id', '=', 'dst.port_id')
            ->select(
                'SHIPMENT.*',
                'CUSTOMER.company_name',
                'src.port_name as source_port',
                'dst.port_name as destination_port'
            )
            ->orderBy('SHIPMENT.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentShipments'));
    }
}
