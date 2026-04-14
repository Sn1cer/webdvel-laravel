<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // dari database
        $products = Product::latest()->get();
        
        // send to (welcome.blade.php)
        return view('welcome', compact('products'));
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product-detail', compact('product'));
    }
}
