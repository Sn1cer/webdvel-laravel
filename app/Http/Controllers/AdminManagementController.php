<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    public function index()
    {
        // Mengambil semua user kecuali Owner yang sedang login
        $users = User::where('id', '!=', auth()->id())->get();
        
        return view('admin.user-management.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,customer',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'Role user ' . $user->name . ' berhasil diperbarui.');
    }
}