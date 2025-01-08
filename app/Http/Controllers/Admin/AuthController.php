<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerFrom(){
        return view('auth.register');
    }
    public function loginFrom(){
        return view('auth.login');
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => 0, // Default to non-admin user
        ]);

        return redirect()->route('login');
    }

    // Login user and issue token
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')
                             ->withErrors($validator)
                             ->withInput();
        }

        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Authentication passed, redirect to the dashboard
            return redirect()->route('admin.products.index');  // You can change '/home' to your desired route
        }

        // Authentication failed
        return redirect()->route('login')
                         ->withErrors(['email' => 'Invalid credentials'])
                         ->withInput();
    }

    // Logout the user and revoke the token
    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function adminDashboard(Request $request)
    {
        // Make sure the user is authenticated and an admin
        if (!$request->user() || $request->user()->is_admin == 0) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['message' => 'Welcome to the admin dashboard']);
    }
}
