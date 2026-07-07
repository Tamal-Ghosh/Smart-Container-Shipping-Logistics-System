<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContainerController extends Controller
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

        $query = Container::query();

        if ($request->filled('type')) {
            $query->where('container_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('UPPER(container_number)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(container_type)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(status)'), 'LIKE', "%{$search}%");
            });
        }

        $containers = $query->orderBy('container_number', 'asc')->get();

        return view('admin.containers.index', compact('containers'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.containers.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'container_number' => 'required|string|max:50|unique:CONTAINER,container_number',
            'container_type'   => 'required|string|in:NORMAL,FREEZE,CHEMICAL',
        ]);

        Container::create([
            'container_number' => strtoupper($request->container_number),
            'container_type'   => $request->container_type,
            'status'           => 'AVAILABLE',
        ]);

        return redirect()->route('containers.index')->with('success', 'Container created successfully.');
    }

    public function show($id)
    {
        $this->checkAdminOrOperator();
        $container = Container::findOrFail($id);

        // Fetch assignment/utilisation history from V_CONTAINER_UTILISATION
        $history = DB::table('V_CONTAINER_UTILISATION')
            ->where('container_id', $id)
            ->orderBy('assigned_at', 'desc')
            ->get();

        return view('admin.containers.show', compact('container', 'history'));
    }

    public function edit($id)
    {
        $this->checkAdmin();
        $container = Container::findOrFail($id);
        return view('admin.containers.edit', compact('container'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $container = Container::findOrFail($id);

        $request->validate([
            'container_number' => 'required|string|max:50|unique:CONTAINER,container_number,' . $id . ',container_id',
            'container_type'   => 'required|string|in:NORMAL,FREEZE,CHEMICAL',
        ]);

        $container->update([
            'container_number' => strtoupper($request->container_number),
            'container_type'   => $request->container_type,
        ]);

        return redirect()->route('containers.index')->with('success', 'Container updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $this->checkAdmin();
        $container = Container::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:AVAILABLE,ASSIGNED,MAINTENANCE',
        ]);

        $currentStatus = $container->status;
        $newStatus = $request->status;

        $allowedTransitions = [
            'AVAILABLE'   => ['ASSIGNED', 'MAINTENANCE'],
            'ASSIGNED'    => ['AVAILABLE', 'MAINTENANCE'],
            'MAINTENANCE' => ['AVAILABLE', 'ASSIGNED'],
        ];

        if ($currentStatus === $newStatus) {
            return redirect()->back()->with('info', 'Container status was already ' . $newStatus . '.');
        }

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            return redirect()->back()->with('error', "Invalid transition from {$currentStatus} to {$newStatus}.");
        }

        $container->status = $newStatus;
        $container->save();

        return redirect()->route('containers.index')->with('success', 'Container status updated to ' . $newStatus . ' successfully.');
    }

    public function destroy($id)
    {
        $this->checkAdmin();
        $container = Container::findOrFail($id);
        try {
            $container->delete();
            return redirect()->route('containers.index')->with('success', 'Container deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('containers.index')->with('error', 'Cannot delete container because it has associated records.');
        }
    }
}
