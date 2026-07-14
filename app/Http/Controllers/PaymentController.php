<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
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

        $query = DB::table('V_PAYMENT_REPORT')
            ->join('PAYMENT', 'V_PAYMENT_REPORT.payment_id', '=', 'PAYMENT.payment_id')
            ->select('V_PAYMENT_REPORT.*', 'PAYMENT.customer_id');

        // Customer scoping
        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer) {
                return view('payments.index', ['payments' => collect(), 'revenue' => 0]);
            }
            $query->where('PAYMENT.customer_id', $customer->customer_id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('V_PAYMENT_REPORT.payment_status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('V_PAYMENT_REPORT.payment_method', $request->method);
        }

        if ($request->filled('amount_min')) {
            $query->where('V_PAYMENT_REPORT.amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('V_PAYMENT_REPORT.amount', '<=', $request->amount_max);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('V_PAYMENT_REPORT.due_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('V_PAYMENT_REPORT.due_date', '<=', $request->end_date);
        }

        $payments = $query->orderBy('V_PAYMENT_REPORT.payment_id', 'desc')->get();

        // Calculate Revenue using PL/SQL function (Admin / Operator only)
        $revenue = null;
        if (Auth::user()->role !== 'CUSTOMER' && $request->filled('rev_start') && $request->filled('rev_end')) {
            try {
                $result = DB::select("
                    SELECT get_revenue(
                        TO_DATE(:start_date, 'YYYY-MM-DD'), 
                        TO_DATE(:end_date, 'YYYY-MM-DD')
                    ) AS total_rev 
                    FROM DUAL
                ", [
                    'start_date' => $request->rev_start,
                    'end_date'   => $request->rev_end
                ]);
                $revenue = $result[0]->total_rev ?? 0;
            } catch (\Exception $e) {
                $revenue = 0;
            }
        }

        return view('payments.index', compact('payments', 'revenue'));
    }

    public function create()
    {
        $this->checkOperatorOrAdmin();

        // List shipments that do not have completed payments, or list all shipments
        $shipments = Shipment::with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.create', compact('shipments'));
    }

    public function store(Request $request)
    {
        $this->checkOperatorOrAdmin();

        $request->validate([
            'shipment_id'    => 'required|integer|exists:SHIPMENT,shipment_id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:CREDIT_CARD,BANK_TRANSFER,CASH,MOBILE_BANKING',
            'due_date'       => 'required|date|after_or_equal:today',
        ]);

        $shipment = Shipment::findOrFail($request->shipment_id);

        DB::beginTransaction();
        try {
            $shipmentId = (int) $request->shipment_id;
            $customerId = (int) $shipment->customer_id;
            $amount = (float) $request->amount;
            $method = $request->payment_method;
            $dueDate = $request->due_date;
            $paymentId = 0;

            // Execute create_payment procedure using Yajra Oci8 PDO bindings
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("
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

            $stmt->bindParam(':shipment_id', $shipmentId, \PDO::PARAM_INT);
            $stmt->bindParam(':customer_id', $customerId, \PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':method', $method, \PDO::PARAM_STR);
            $stmt->bindParam(':due_date', $dueDate, \PDO::PARAM_STR);
            $stmt->bindParam(':payment_id', $paymentId, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT, 38);

            $stmt->execute();

            if (!$paymentId) {
                throw new \Exception("Payment procedure did not return a valid ID.");
            }

            DB::commit();
            return redirect()->route('payments.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create payment: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $this->checkAuth();

        $payment = Payment::with(['shipment.sourcePort', 'shipment.destinationPort', 'customer'])->findOrFail($id);

        // Enforce customer scoping
        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer || $payment->customer_id !== $customer->customer_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $this->checkAuth();

        $payment = Payment::findOrFail($id);
        $action = $request->action; // 'pay', 'fail', 'refund'

        if ($action === 'pay') {
            if (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'ADMIN') {
                abort(403, 'Unauthorized action.');
            }
            $payment->payment_status = 'COMPLETED';
            $payment->payment_date = now();
            $payment->transaction_ref = 'TXN-' . strtoupper(str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT));
            $payment->save();

            return redirect()->back()->with('success', 'Payment status updated to COMPLETED.');
        }

        if ($action === 'fail') {
            if (Auth::user()->role !== 'OPERATOR' && Auth::user()->role !== 'ADMIN') {
                abort(403, 'Unauthorized action.');
            }
            $payment->payment_status = 'FAILED';
            $payment->save();

            return redirect()->back()->with('success', 'Payment status updated to FAILED.');
        }

        if ($action === 'refund') {
            if (Auth::user()->role !== 'ADMIN') {
                abort(403, 'Unauthorized action. Only Admins can issue refunds.');
            }
            $payment->payment_status = 'REFUNDED';
            $payment->save();

            return redirect()->back()->with('success', 'Payment refunded successfully.');
        }

        return redirect()->back()->with('error', 'Invalid payment update action.');
    }

    public function checkout($id)
    {
        $this->checkAuth();

        $payment = Payment::with(['shipment.sourcePort', 'shipment.destinationPort', 'customer'])->findOrFail($id);

        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer || $payment->customer_id !== $customer->customer_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($payment->payment_status === 'COMPLETED' || $payment->payment_status === 'REFUNDED') {
            return redirect()->route('payments.show', $id)->with('error', 'This invoice is already paid or refunded.');
        }

        return view('payments.checkout', compact('payment'));
    }

    public function processCheckout(Request $request, $id)
    {
        $this->checkAuth();

        $payment = Payment::findOrFail($id);

        if (Auth::user()->role === 'CUSTOMER') {
            $customer = Customer::where('user_id', Auth::user()->user_id)->first();
            if (!$customer || $payment->customer_id !== $customer->customer_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($request->payment_method === 'MOBILE_BANKING') {
            $request->validate([
                'payment_method' => 'required|string',
                'provider' => 'required|string|in:bkash,nagad,rocket',
                'phone_number' => 'required|string|min:11|max:15',
                'otp' => 'required|string|size:6',
                'pin' => 'required|string|min:4|max:5',
            ]);
            $methodPrefix = strtoupper($request->provider);
            $paymentMethod = 'MOBILE_BANKING';
        } else {
            $request->validate([
                'payment_method' => 'required|string|in:CREDIT_CARD',
                'card_name' => 'required|string|min:3',
                'card_number' => 'required|string|min:16|max:19',
                'card_expiry' => 'required|string|regex:/^\d{2}\/\d{2}$/',
                'card_cvv' => 'required|string|digits:3',
            ]);
            $methodPrefix = 'CARD';
            $paymentMethod = 'CREDIT_CARD';
        }

        $payment->payment_status = 'COMPLETED';
        $payment->payment_date = now();
        $payment->payment_method = $paymentMethod;
        $payment->transaction_ref = 'TXN-' . $methodPrefix . '-' . strtoupper(str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT));
        $payment->save();

        return redirect()->route('payments.show', $id)->with('success', 'Mock payment processed successfully via ' . ($request->provider ?? 'Credit/Debit Card') . '.');
    }
}
