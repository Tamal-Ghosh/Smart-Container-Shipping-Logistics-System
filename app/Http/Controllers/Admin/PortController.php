<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PortController extends Controller
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

        $query = Port::query();

        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('UPPER(port_name)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(port_code)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(location)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(country)'), 'LIKE', "%{$search}%");
            });
        }

        $ports = $query->orderBy('port_name', 'asc')->get();

        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.ports.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->merge([
            'port_code' => strtoupper(trim($request->port_code)),
        ]);

        $request->validate([
            'port_name' => 'required|string|max:100',
            'port_code' => ['required', 'string', 'max:10', Rule::unique('PORT', 'port_code')],
            'location'  => 'nullable|string|max:100',
            'country'   => 'nullable|string|max:100',
        ]);

        try {
            Port::create([
                'port_name' => $request->port_name,
                'port_code' => $request->port_code,
                'location'  => $request->location,
                'country'   => $request->country,
                'status'    => 'ACTIVE',
            ]);
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                return back()
                    ->withInput()
                    ->withErrors(['port_code' => 'This port code already exists.']);
            }

            throw $exception;
        }

        return redirect()->route('ports.index')->with('success', 'Port created successfully.');
    }

    public function edit($id)
    {
        $this->checkAdmin();
        $port = Port::findOrFail($id);
        return view('admin.ports.edit', compact('port'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $port = Port::findOrFail($id);

        $request->merge([
            'port_code' => strtoupper(trim($request->port_code)),
        ]);

        $request->validate([
            'port_name' => 'required|string|max:100',
            'port_code' => ['required', 'string', 'max:10', Rule::unique('PORT', 'port_code')->ignore($id, 'port_id')],
            'location'  => 'nullable|string|max:100',
            'country'   => 'nullable|string|max:100',
        ]);

        try {
            $port->update([
                'port_name' => $request->port_name,
                'port_code' => $request->port_code,
                'location'  => $request->location,
                'country'   => $request->country,
            ]);
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                return back()
                    ->withInput()
                    ->withErrors(['port_code' => 'This port code already exists.']);
            }

            throw $exception;
        }

        return redirect()->route('ports.index')->with('success', 'Port updated successfully.');
    }

    public function toggleActive($id)
    {
        $this->checkAdmin();
        $port = Port::findOrFail($id);
        $port->status = $port->status === 'ACTIVE' ? 'DEACTIVE' : 'ACTIVE';
        $port->save();

        return redirect()->back()->with('success', 'Port status updated.');
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return str_contains($exception->getMessage(), 'ORA-00001');
    }
}
