<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 

class AdminManagementController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
                     ->where('role', 'admin')
                     ->get();
        
        return view('admin.user-management.index', compact('users'));
    }

    // Fungsi baru untuk mendaftarkan Admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => 'admin', 
        ]);

        return redirect()->back()->with('success', 'Akun Admin baru bernama ' . $request->name . ' berhasil didaftarkan dan aktif!');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,customer',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        $pesan = $request->role === 'customer' 
                 ? 'Hak akses Admin untuk ' . $user->name . ' berhasil dicabut. Akun telah dikembalikan menjadi Pelanggan.' 
                 : 'Role user ' . $user->name . ' berhasil diperbarui.';

        return back()->with('success', $pesan);
    }
}