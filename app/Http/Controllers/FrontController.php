<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Banner;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // dari database
        $products = Product::latest()->get();
        
        // send to (welcome.blade.php)
        $banners = Banner::where('is_active', true)->latest()->get();
        return view('welcome', compact('products', 'banners'));
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product-detail', compact('product'));
    }
}
