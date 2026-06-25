<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            // Insert into USERS table
            DB::table('USERS')->insert([
                'username'      => $request->username,
                'email'         => $request->email,
                'password_hash' => bcrypt($request->password),
                'role'          => $request->role,
                'is_active'     => 'Y',
            ]);

            // Retrieve the newly created user (Oracle trigger set the PK)
            $user = User::where('email', $request->email)->first();

            // If CUSTOMER role, also insert into CUSTOMER table
            if ($request->role === 'CUSTOMER') {
                Customer::create([
                    'user_id'        => $user->user_id,
                    'company_name'   => $request->company_name,
                    'contact_person' => $request->contact_person,
                    'phone'          => $request->phone,
                    'email'          => $request->email,
                    'address'        => $request->address,
                    'country'        => $request->country,
                ]);
            }

            DB::commit();

            Auth::login($user);

            return $this->redirectByRole($user);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed. Please try again. ' . $e->getMessage()]);
        }
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->is_active !== 'Y' || !Hash::check($request->password, $user->password_hash)) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid credentials or account deactivated.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirect user to the appropriate dashboard based on their role.
     */
    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'ADMIN'    => redirect('/admin/dashboard'),
            'OPERATOR' => redirect('/operator/dashboard'),
            'CUSTOMER' => redirect('/customer/dashboard'),
            default    => redirect('/login'),
        };
    }
}
