<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Method untuk login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba autentikasi
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Hapus semua token lama pengguna
            $user->tokens()->delete();

            // Buat token baru
            $token = $user->createToken('auth_token')->plainTextToken;
            $onlyToken = explode('|', $token)[1];
            // Response JSON
            return response()->json([
                'message' => 'Login successful',
                'token' => $onlyToken,
                'user' => $user,
            ]);
        }

        // Jika autentikasi gagal
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Method untuk logout (sudah ada sebelumnya)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    // Method untuk refresh token (opsional, sudah ada sebelumnya)
    public function refreshToken(Request $request)
    {
        // Hapus token lama
        $request->user()->currentAccessToken()->delete();

        // Buat token baru
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        // Response JSON
        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
            $user = Auth::user();
            return response()->json([
                'message' => 'My data',
                'token' => $request->user()->currentAccessToken()->token,
                'user' => $user,
            ]);
    }
    public function register(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Buat token untuk pengguna baru
        $token = $user->createToken('auth_token')->plainTextToken;
        $onlyToken = explode('|', $token)[1];

        // Response JSON
        return response()->json([
            'message' => 'Registration successful',
            'token' => $onlyToken,
            'user' => $user,
        ]);
    }
}