<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'OPERATOR') {
            return redirect('/login');
        }

        $pendingShipments = DB::table('SHIPMENT')
            ->join('CUSTOMER', 'SHIPMENT.customer_id', '=', 'CUSTOMER.customer_id')
            ->join('PORT as src', 'SHIPMENT.source_port_id', '=', 'src.port_id')
            ->join('PORT as dst', 'SHIPMENT.destination_port_id', '=', 'dst.port_id')
            ->select(
                'SHIPMENT.*',
                'CUSTOMER.company_name',
                'src.port_name as source_port',
                'dst.port_name as destination_port'
            )
            ->whereIn('SHIPMENT.status', ['PENDING', 'BOOKED'])
            ->get();

        $availableContainers = DB::table('CONTAINER')
            ->where('status', 'AVAILABLE')
            ->count();

        $availableVehicles = DB::table('VEHICLE')
            ->where('status', 'AVAILABLE')
            ->count();

        $activeContainers = 0;
        try {
            $result = DB::select("SELECT get_active_containers() AS count FROM DUAL");
            $activeContainers = $result[0]->count ?? 0;
        } catch (\Exception $e) {}

        $todayEvents = DB::table('TRACKING_LOG')
            ->join('SHIPMENT', 'TRACKING_LOG.shipment_id', '=', 'SHIPMENT.shipment_id')
            ->select('TRACKING_LOG.*', 'SHIPMENT.shipment_ref')
            ->whereDate('TRACKING_LOG.updated_at', Carbon::today())
            ->orderBy('TRACKING_LOG.updated_at', 'desc')
            ->get();

        return view('operator.dashboard', compact(
            'pendingShipments',
            'availableContainers',
            'availableVehicles',
            'activeContainers',
            'todayEvents'
        ));
    }
}
