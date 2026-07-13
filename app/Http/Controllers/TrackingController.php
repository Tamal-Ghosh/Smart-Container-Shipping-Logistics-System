<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('ref')) {
            $ref = strtoupper(trim($request->ref));
            return redirect()->route('tracking.show', ['shipment_ref' => $ref]);
        }

        return view('tracking.index');
    }

    public function create()
    {
        if (!Auth::check() || (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch active shipments (not delivered or cancelled)
        $shipments = Shipment::whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('shipment_id', 'desc')
            ->get();

        $ports = Port::where('status', 'ACTIVE')->orderBy('port_name', 'asc')->get();

        return view('operator.tracking_log', compact('shipments', 'ports'));
    }

    public function show(Request $request, $shipment_ref)
    {
        $shipment = Shipment::where('shipment_ref', $shipment_ref)->first();

        if (!$shipment) {
            return redirect()->route('tracking.index')->with('error', "Shipment reference '{$shipment_ref}' was not found.");
        }

        // Enforce customer scoping
        if (Auth::check() && Auth::user()->role === 'CUSTOMER') {
            if ($shipment->customer->user_id !== Auth::user()->user_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Fetch logs from V_LIVE_TRACKING view
        $timeline = DB::table('V_LIVE_TRACKING')
            ->where('shipment_ref', $shipment_ref)
            ->orderBy('updated_at', 'asc')
            ->get();

        $ports = Port::where('status', 'ACTIVE')->orderBy('port_name', 'asc')->get();

        return view('tracking.show', compact('shipment', 'timeline', 'ports'));
    }

    public function store(Request $request)
    {
        if (!Auth::check() || (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'shipment_id' => 'required|integer|exists:SHIPMENT,shipment_id',
            'event_type'  => 'required|string|in:PENDING,BOOKED,IN_TRANSIT,AT_PORT,DELIVERED,CANCELLED',
            'location'    => 'nullable|string|max:100',
            'port_id'     => 'nullable|integer|exists:PORT,port_id',
            'status'      => 'required|string|in:ON_TIME,DELAYED,EARLY,HELD',
            'remarks'     => 'nullable|string|max:255',
        ]);

        // Only allow cancellation if shipment is PENDING or BOOKED
        if ($request->event_type === 'CANCELLED') {
            $shipment = Shipment::findOrFail($request->shipment_id);
            if (!in_array($shipment->status, ['PENDING', 'BOOKED'])) {
                return redirect()->back()->withErrors(['event_type' => 'Cancellation is only allowed when the shipment is in PENDING or BOOKED status.']);
            }
        }

        // Safely resolve values — empty string from disabled fields → sensible defaults
        $portId   = !empty($request->port_id) ? (int) $request->port_id : 0;  // 0 = no port, handled by NULLIF in SQL
        $location = !empty($request->location) ? trim($request->location) : ($request->event_type === 'CANCELLED' ? 'CANCELLED' : '');
        $remarks  = !empty($request->remarks)  ? trim($request->remarks)  : 'N/A';

        if (empty($location)) {
            return redirect()->back()->withErrors(['location' => 'Please select a current location or port.']);
        }

        try {
            // Use NULLIF(:port_id, 0) so PHP always binds an integer — avoids Oci8 null-NUMBER binding issues
            DB::statement("
                BEGIN 
                    add_tracking_event(
                        p_shipment_id => :shipment_id,
                        p_port_id     => NULLIF(:port_id, 0),
                        p_event_type  => :event_type,
                        p_location    => :location,
                        p_status      => :status,
                        p_remarks     => :remarks
                    ); 
                END;
            ", [
                'shipment_id' => (int) $request->shipment_id,
                'port_id'     => $portId,
                'event_type'  => $request->event_type,
                'location'    => $location,
                'status'      => $request->status,
                'remarks'     => $remarks,
            ]);

            // PHP-level safety net: ensure payment is refunded on cancellation
            if ($request->event_type === 'CANCELLED') {
                DB::table('PAYMENT')
                    ->where('shipment_id', $request->shipment_id)
                    ->update(['payment_status' => 'REFUNDED']);
            }

            return redirect()->back()->with('success', 'Tracking event logged and shipment status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to log event: ' . $e->getMessage());
        }
    }
}
