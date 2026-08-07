<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('email', '!=', 'admin@dveljeans.com')->latest()->get();

        // Hitung statistik 
        foreach($customers as $customer) {
            $customer->total_orders = Order::where('user_id', $customer->id)->count();
            
            $customer->total_spent = Order::where('user_id', $customer->id)
                                          ->whereIn('status', ['Diproses', 'Dikirim'])
                                          ->sum('total_harga');
        }

        return view('admin.customers.index', compact('customers'));
    }
}