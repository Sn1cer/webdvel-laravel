@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Manajemen Akses Pengguna</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-6 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama</th>
                    <th class="py-3 px-6 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="py-3 px-6 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status Akses (Role)</th>
                    <th class="py-3 px-6 bg-gray-200 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi (Ubah Hak Akses)</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($users as $user)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $user->name }}</td>
                    <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                    <td class="py-3 px-6 text-left">
                        @if ($user->role === 'admin')
                            <span class="bg-blue-200 text-blue-700 py-1 px-3 rounded-full text-xs font-bold">Admin</span>
                        @else
                            <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold">Pelanggan</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-center">
                        <form action="{{ route('admin.user-management.update-role', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            @if ($user->role === 'customer')
                                <input type="hidden" name="role" value="admin">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm transition duration-150">
                                    Jadikan Admin
                                </button>
                            @else
                                <input type="hidden" name="role" value="customer">
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition duration-150" onclick="return confirm('Yakin ingin mencabut akses admin pengguna ini?')">
                                    Cabut Akses (Jadikan Pelanggan)
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Tidak ada data pengguna yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection