<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Models\Customer;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\Vehicle;
use App\Models\Container;
use App\Models\ContainerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    private function checkAuth()
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated.');
        }
    }

    private function checkOperatorOrAdmin()
    {
        $this->checkAuth();
        if (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAuth();

        $query = Shipment::query()
            ->join('CUSTOMER', 'SHIPMENT.customer_id', '=', 'CUSTOMER.customer_id')
            ->join('PORT as src', 'SHIPMENT.source_port_id', '=', 'src.port_id')
            ->join('PORT as dst', 'SHIPMENT.destination_port_id', '=', 'dst.port_id')
            ->select(
                'SHIPMENT.*',
                'CUSTOMER.company_name',
                'src.port_name as source_port',
                'dst.port_name as destination_port'
            );

        // Role restriction: Customer only sees their own
        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer) {
                return view('operator.shipments.index', ['shipments' => collect()]);
            }
            $query->where('SHIPMENT.customer_id', $customer->customer_id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('SHIPMENT.status', $request->status);
        }

        if ($request->filled('source_port_id')) {
            $query->where('SHIPMENT.source_port_id', $request->source_port_id);
        }

        if ($request->filled('destination_port_id')) {
            $query->where('SHIPMENT.destination_port_id', $request->destination_port_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('SHIPMENT.shipment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('SHIPMENT.shipment_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('UPPER(SHIPMENT.shipment_ref)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(CUSTOMER.company_name)'), 'LIKE', "%{$search}%");
            });
        }

        $shipments = $query->orderBy('SHIPMENT.created_at', 'desc')->get();

        // Additional filter parameters for dropdown lists
        $customers = Customer::orderBy('company_name', 'asc')->get();
        $ports = Port::where('status', 'ACTIVE')->orderBy('port_name', 'asc')->get();

        return view('operator.shipments.index', compact('shipments', 'customers', 'ports'));
    }

    public function create()
    {
        $this->checkAuth();
        if (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'CUSTOMER' && Auth::user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }

        $customers = Customer::orderBy('company_name', 'asc')->get();
        $ports = Port::where('status', 'ACTIVE')->orderBy('port_name', 'asc')->get();
        $vehicles = Vehicle::where('status', 'AVAILABLE')->orderBy('vehicle_number', 'asc')->get();
        
        // Multi-select containers: available only
        $containers = Container::where('status', 'AVAILABLE')->orderBy('container_number', 'asc')->get();

        // Retrieve customer for customer auto-fill
        $myCustomer = null;
        if (Auth::user()->role === 'CUSTOMER') {
            $myCustomer = Customer::where('user_id', Auth::user()->user_id)->first();
        }

        return view('operator.shipments.create', compact('customers', 'ports', 'vehicles', 'containers', 'myCustomer'));
    }

    public function store(StoreShipmentRequest $request)
    {
        DB::beginTransaction();

        try {
            // Resolve customer_id
            $customerId = null;
            if (Auth::user()->role === 'CUSTOMER') {
                $customer = Customer::where('user_id', Auth::user()->user_id)->firstOrFail();
                $customerId = $customer->customer_id;
            } else {
                $customerId = $request->customer_id;
            }

            // Lock vehicle and verify availability
            $vehicle = Vehicle::where('vehicle_id', $request->vehicle_id)
                ->where('status', 'AVAILABLE')
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                return redirect()->back()->withInput()->withErrors(['vehicle_id' => 'The selected vehicle is no longer available.']);
            }

            // Lock containers and verify availability
            $containerIds = $request->containers;
            $selectedContainers = Container::whereIn('container_id', $containerIds)
                ->where('status', 'AVAILABLE')
                ->lockForUpdate()
                ->get();

            if (count($selectedContainers) !== count($containerIds)) {
                return redirect()->back()->withInput()->withErrors(['containers' => 'One or more of the selected containers are no longer available.']);
            }

            // Bind values to local variables for PDO binding reference
            $sourcePort = (int) $request->source_port_id;
            $destPort = (int) $request->destination_port_id;
            $vehicleId = (int) $request->vehicle_id;
            $createdBy = (int) Auth::user()->user_id;
            $notes = $request->notes ?? '';
            $shipmentId = 0;

            // Execute book_shipment PROCEDURE using Yajra Oci8 PDO bindings
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("
                DECLARE
                    v_shipment_id NUMBER;
                BEGIN
                    book_shipment(
                        p_customer_id            => :customer_id,
                        p_source_port_id         => :source_port,
                        p_destination_port_id    => :dest_port,
                        p_vehicle_id             => :vehicle,
                        p_created_by             => :created_by,
                        p_expected_delivery_date => SYSDATE + 30,
                        p_notes                  => :notes,
                        p_shipment_id            => :shipment_id
                    );
                END;
            ");

            $stmt->bindParam(':customer_id', $customerId, \PDO::PARAM_INT);
            $stmt->bindParam(':source_port', $sourcePort, \PDO::PARAM_INT);
            $stmt->bindParam(':dest_port', $destPort, \PDO::PARAM_INT);
            $stmt->bindParam(':vehicle', $vehicleId, \PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $createdBy, \PDO::PARAM_INT);
            $stmt->bindParam(':notes', $notes, \PDO::PARAM_STR);
            $stmt->bindParam(':shipment_id', $shipmentId, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT, 38);

            $stmt->execute();

            if (!$shipmentId) {
                throw new \Exception("Procedure did not return a valid shipment ID.");
            }

            // Insert CONTAINER_ASSIGNMENT rows per selected container
            foreach ($containerIds as $containerId) {
                // Since UI seal number is removed, generate a unique random seal identifier automatically
                $sealNumber = 'SL-' . strtoupper(str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
                $loadedWeight = $request->loaded_weights[$containerId] ?? 0;

                ContainerAssignment::create([
                    'shipment_id'      => $shipmentId,
                    'container_id'     => $containerId,
                    'seal_number'      => $sealNumber,
                    'loaded_weight_kg' => $loadedWeight,
                    'assigned_at'      => now(),
                ]);

                // Update container status to IN_USE
                DB::table('CONTAINER')
                    ->where('container_id', $containerId)
                    ->update(['status' => 'IN_USE']);
            }

            // Update vehicle status to IN_USE
            $vehicle->status = 'IN_USE';
            $vehicle->save();

            // Calculate standard shipping rate automatically:
            // Base fare: 5,000 BDT
            // Per container fee: 12,000 BDT
            // Per kg of cargo loaded: 5 BDT
            $baseFare = 5000;
            $containerFee = 12000 * count($containerIds);
            $weightFee = 0;
            foreach ($containerIds as $containerId) {
                $weight = (float) ($request->loaded_weights[$containerId] ?? 0);
                $weightFee += ($weight * 5);
            }
            $amount = $baseFare + $containerFee + $weightFee;

            // Set initial method placeholder, which will be updated on checkout
            $method = 'CREDIT_CARD'; 
            $dueDate = now()->format('Y-m-d');
            $paymentId = 0;

            $stmtPay = $pdo->prepare("
                DECLARE
                    v_payment_id NUMBER;
                BEGIN
                    create_payment(
                        p_shipment_id    => :shipment_id,
                        p_customer_id    => :customer_id,
                        p_amount         => :amount,
                        p_payment_method => :method,
                        p_due_date       => TO_DATE(:due_date, 'YYYY-MM-DD'),
                        p_payment_id     => :payment_id
                    );
                END;
            ");

            $stmtPay->bindParam(':shipment_id', $shipmentId, \PDO::PARAM_INT);
            $stmtPay->bindParam(':customer_id', $customerId, \PDO::PARAM_INT);
            $stmtPay->bindParam(':amount', $amount);
            $stmtPay->bindParam(':method', $method, \PDO::PARAM_STR);
            $stmtPay->bindParam(':due_date', $dueDate, \PDO::PARAM_STR);
            $stmtPay->bindParam(':payment_id', $paymentId, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT, 38);

            $stmtPay->execute();

            if (!$paymentId) {
                throw new \Exception("Payment procedure did not return a valid ID.");
            }

            DB::commit();

            $ref = DB::table('SHIPMENT')->where('shipment_id', $shipmentId)->value('shipment_ref');

            if (Auth::user()->role === 'CUSTOMER') {
                return redirect()->route('payments.checkout', $paymentId)
                    ->with('success', "Shipment booking {$ref} created successfully. Please complete payment to confirm.");
            }

            $redirectUrl = Auth::user()->role === 'OPERATOR' ? '/operator/dashboard' : '/admin/dashboard';
            return redirect($redirectUrl)->with('success', "Shipment booking {$ref} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Booking failed: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $this->checkAuth();

        $shipment = Shipment::findOrFail($id);

        // Enforce customer scoping
        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer || $shipment->customer_id !== $customer->customer_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Fetch container assignments
        $assignments = ContainerAssignment::where('shipment_id', $id)
            ->join('CONTAINER', 'CONTAINER_ASSIGNMENT.container_id', '=', 'CONTAINER.container_id')
            ->select('CONTAINER_ASSIGNMENT.*', 'CONTAINER.container_number', 'CONTAINER.container_type')
            ->get();

        // Fetch payment details
        $payment = DB::table('PAYMENT')
            ->where('shipment_id', $id)
            ->first();

        // Fetch visual timeline using V_LIVE_TRACKING view
        $timeline = DB::table('V_LIVE_TRACKING')
            ->where('shipment_ref', $shipment->shipment_ref)
            ->orderBy('updated_at', 'asc')
            ->get();

        $ports = Port::where('status', 'ACTIVE')->orderBy('port_name', 'asc')->get();

        return view('operator.shipments.show', compact('shipment', 'assignments', 'payment', 'timeline', 'ports'));
    }


    public function cancel($id)
    {
        $this->checkAuth();
        if (!in_array(Auth::user()->role, ['OPERATOR', 'ADMIN'])) {
            abort(403, 'Unauthorized.');
        }

        $shipment = Shipment::findOrFail($id);

        if (!in_array($shipment->status, ['PENDING', 'BOOKED'])) {
            return redirect()->back()->with('error', 'Only PENDING or BOOKED shipments can be cancelled.');
        }

        DB::beginTransaction();
        try {
            // 1. Mark shipment CANCELLED
            DB::table('SHIPMENT')
                ->where('shipment_id', $id)
                ->update(['status' => 'CANCELLED']);

            // 2. Release vehicle
            if ($shipment->vehicle_id) {
                DB::table('VEHICLE')
                    ->where('vehicle_id', $shipment->vehicle_id)
                    ->update(['status' => 'AVAILABLE']);
            }

            // 3. Release containers
            $containerIds = DB::table('CONTAINER_ASSIGNMENT')
                ->where('shipment_id', $id)
                ->pluck('container_id');
            if ($containerIds->isNotEmpty()) {
                DB::table('CONTAINER')
                    ->whereIn('container_id', $containerIds)
                    ->update(['status' => 'AVAILABLE']);
            }

            // 4. Refund payment
            DB::table('PAYMENT')
                ->where('shipment_id', $id)
                ->update(['payment_status' => 'REFUNDED']);

            // 5. Log a tracking entry
            DB::table('TRACKING_LOG')->insert([
                'shipment_id' => $id,
                'port_id'     => null,
                'event_type'  => 'CANCELLED',
                'location'    => 'CANCELLED',
                'status'      => 'ON_TIME',
                'remarks'     => 'Shipment cancelled. Payment refunded.',
                'updated_at'  => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Shipment cancelled and payment marked as REFUNDED.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }
}
