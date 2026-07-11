<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('search')) {
            $search = strtoupper($request->search);
            $query->where(function($q) use ($search) {
                $q->where(DB::raw('UPPER(username)'), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw('UPPER(email)'), 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'username' => 'required|string|max:50|unique:USERS,username',
            'email'    => 'required|email|max:100|unique:USERS,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::table('USERS')->insert([
            'username'      => $request->username,
            'email'         => $request->email,
            'password_hash' => bcrypt($request->password),
            'role'          => 'OPERATOR',
            'is_active'     => 'Y',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Operator account created successfully.');
    }

    public function toggleActive($id)
    {
        $this->checkAdmin();

        if (Auth::user()->user_id == $id) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user = User::findOrFail($id);

        if ($user->is_active === 'Y') {
            // Call PL/SQL procedure to deactivate
            DB::statement("BEGIN deactivate_user(p_user_id => :user_id); END;", ['user_id' => $id]);
            $msg = "User {$user->username} has been deactivated.";
        } else {
            // Reactivate directly
            $user->is_active = 'Y';
            $user->save();
            $msg = "User {$user->username} has been reactivated.";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $this->checkAdmin();

        if (Auth::user()->user_id == $id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete user because they have associated records.');
        }
    }
}
