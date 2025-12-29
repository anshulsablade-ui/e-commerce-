<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed'
        ]);
        if (!Auth::attempt($validated)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return response()->json(['status' => 'success', 'message' => 'Login successful'], 200);
    }
}
