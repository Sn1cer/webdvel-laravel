<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Ambil akun user, KECUALI akun Admin
        $customers = User::where('email', '!=', 'admin@dveljeans.com')->latest()->get();

        // Hitung statistik belanja untuk masing-masing pelanggan
        foreach($customers as $customer) {
            // Hitung total
            $customer->total_orders = Order::where('user_id', $customer->id)->count();
            
            // Hitung total uang 
            $customer->total_spent = Order::where('user_id', $customer->id)
                                          ->whereIn('status', ['Diproses', 'Dikirim'])
                                          ->sum('total_harga');
        }

        return view('admin.customers.index', compact('customers'));
    }
}