<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Container;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated.');
        }

        $q = strtoupper(trim($request->q ?? ''));

        if (empty($q)) {
            return view('search.results', [
                'shipments'  => collect(),
                'containers' => collect(),
                'customers'  => collect(),
                'query'      => ''
            ]);
        }

        // Query Shipments
        $shipmentsQuery = Shipment::query()
            ->join('CUSTOMER', 'SHIPMENT.customer_id', '=', 'CUSTOMER.customer_id')
            ->join('PORT as src', 'SHIPMENT.source_port_id', '=', 'src.port_id')
            ->join('PORT as dst', 'SHIPMENT.destination_port_id', '=', 'dst.port_id')
            ->select(
                'SHIPMENT.*', 
                'CUSTOMER.company_name', 
                'src.port_name as source_port', 
                'dst.port_name as destination_port'
            );

        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if ($customer) {
                $shipmentsQuery->where('SHIPMENT.customer_id', $customer->customer_id);
            } else {
                $shipmentsQuery->whereRaw('1=0');
            }
        }

        $shipments = $shipmentsQuery->where(function($sub) use ($q) {
            $sub->where(DB::raw('UPPER(SHIPMENT.shipment_ref)'), 'LIKE', "%{$q}%")
                ->orWhere(DB::raw('UPPER(CUSTOMER.company_name)'), 'LIKE', "%{$q}%");
        })->get();

        // Query Containers
        $containers = Container::where(DB::raw('UPPER(container_number)'), 'LIKE', "%{$q}%")->get();

        // Query Customers (Admin / Operator only)
        $customers = collect();
        if (Auth::user()->role === 'ADMIN' || Auth::user()->role === 'OPERATOR') {
            $customers = Customer::where(DB::raw('UPPER(company_name)'), 'LIKE', "%{$q}%")
                ->orWhere(DB::raw('UPPER(contact_person)'), 'LIKE', "%{$q}%")
                ->get();
        }

        // Exact Match Redirect logic
        $totalMatches = count($shipments) + count($containers) + count($customers);

        if ($totalMatches === 1) {
            if (count($shipments) === 1) {
                return redirect()->route('operator.shipments.show', ['id' => $shipments->first()->shipment_id]);
            }
            if (count($containers) === 1) {
                return redirect()->route('containers.show', ['id' => $containers->first()->container_id]);
            }
            if (count($customers) === 1) {
                return redirect()->route('operator.shipments.index', ['search' => $customers->first()->company_name]);
            }
        }

        return view('search.results', compact('shipments', 'containers', 'customers', 'q'));
    }
}
