<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'CUSTOMER') {
            return redirect('/login');
        }

        $customerId = DB::table('CUSTOMER')
            ->where('user_id', Auth::id())
            ->value('customer_id');

        if (!$customerId) {
            Auth::logout();
            return redirect('/login')->withErrors(['error' => 'Customer profile not found.']);
        }

        $shipments = DB::table('V_CUSTOMER_DASHBOARD')
            ->where('customer_id', $customerId)
            ->get();

        $shipmentCount = 0;
        try {
            $result = DB::select("SELECT get_shipment_count(?) AS count FROM DUAL", [$customerId]);
            $shipmentCount = $result[0]->count ?? 0;
        } catch (\Exception $e) {}

        $payments = [
            'paid' => 0,
            'pending' => 0,
            'total' => 0
        ];

        foreach ($shipments as $s) {
            if ($s->payment_amount > 0) {
                $payments['total'] += $s->payment_amount;
                if (strtoupper($s->payment_status) === 'PAID' || strtoupper($s->payment_status) === 'COMPLETED') {
                    $payments['paid'] += $s->payment_amount;
                } else {
                    $payments['pending'] += $s->payment_amount;
                }
            }
        }

        $latestShipment = DB::table('SHIPMENT')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        $latestTracking = null;
        if ($latestShipment) {
            $latestTracking = DB::table('TRACKING_LOG')
                ->where('shipment_id', $latestShipment->shipment_id)
                ->orderBy('updated_at', 'desc')
                ->first();
        }

        return view('customer.dashboard', compact(
            'shipments',
            'shipmentCount',
            'payments',
            'latestShipment',
            'latestTracking'
        ));
    }
}
