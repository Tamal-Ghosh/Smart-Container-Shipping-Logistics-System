<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }
    }

    private function checkAdminOrOperator()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['ADMIN', 'OPERATOR'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdminOrOperator();

        $query = Vehicle::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('UPPER(vehicle_number)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(type)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(status)'), 'LIKE', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('vehicle_number', 'asc')->get();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:VEHICLE,vehicle_number',
            'type'           => 'required|string|in:VESSEL',
            'capacity_kg'    => 'required|numeric|min:0',
        ]);

        Vehicle::create([
            'vehicle_number' => strtoupper($request->vehicle_number),
            'type'           => $request->type,
            'capacity_kg'    => $request->capacity_kg,
            'status'         => 'AVAILABLE',
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vessel created successfully.');
    }

    public function edit($id)
    {
        $this->checkAdmin();
        $vehicle = Vehicle::findOrFail($id);
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:VEHICLE,vehicle_number,' . $id . ',vehicle_id',
            'type'           => 'required|string|in:VESSEL',
            'capacity_kg'    => 'required|numeric|min:0',
        ]);

        $vehicle->update([
            'vehicle_number' => strtoupper($request->vehicle_number),
            'type'           => $request->type,
            'capacity_kg'    => $request->capacity_kg,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vessel details updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $this->checkAdmin();
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:AVAILABLE,IN_USE,MAINTENANCE',
        ]);

        $currentStatus = $vehicle->status;
        $newStatus = $request->status;

        // Define valid transitions
        $allowedTransitions = [
            'AVAILABLE'   => ['IN_USE', 'MAINTENANCE'],
            'IN_USE'      => ['AVAILABLE', 'MAINTENANCE'],
            'MAINTENANCE' => ['AVAILABLE', 'IN_USE'],
        ];

        if ($currentStatus === $newStatus) {
            return redirect()->back()->with('info', 'Vessel status was already ' . $newStatus . '.');
        }

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return redirect()->back()->with('error', "Invalid status transition from {$currentStatus} to {$newStatus}.");
        }

        $vehicle->status = $newStatus;
        $vehicle->save();

        return redirect()->route('vehicles.index')->with('success', 'Vessel status transitioned to ' . $newStatus . ' successfully.');
    }

    public function destroy($id)
    {
        $this->checkAdmin();
        $vehicle = Vehicle::findOrFail($id);
        try {
            $vehicle->delete();
            return redirect()->route('vehicles.index')->with('success', 'Vessel deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('vehicles.index')->with('error', 'Cannot delete vessel because it has associated records.');
        }
    }
}
