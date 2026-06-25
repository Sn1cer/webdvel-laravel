<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', 
            'judul' => 'nullable|string|max:255',
            'subjudul' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            
            $file->move(public_path('images/banners'), $nama_file);

            Banner::create([
                'gambar' => $nama_file,
                'judul' => $request->judul,
                'subjudul' => $request->subjudul,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Banner baru berhasil diunggah!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        $path = public_path('images/banners/' . $banner->gambar);
        if (File::exists($path)) {
            File::delete($path);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner berhasil dihapus!');
    }
}