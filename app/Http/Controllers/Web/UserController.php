<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // HALAMAN KELOLA USER
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->latest()->get();
        return view('admin.user.index', compact('users'));
    }

    // TAMBAH USER
    public function store(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|max:50|unique:users,username',
            'no_hp'         => 'required|numeric|digits_between:10,15',
            'role'          => 'required|in:admin,staff',
            'tanggal_lahir' => 'required|date',
            'password'      => 'required|min:6',
        ]);

        User::create([
            'username'      => $request->username,
            'no_hp'         => $request->no_hp,
            'role'          => $request->role,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password'      => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::whereIn('role', ['admin', 'staff'])->findOrFail($id);

        $request->validate([
            'username'      => 'required|string|max:50|unique:users,username,' . $id,
            'no_hp'         => 'required|numeric|digits_between:10,15',
            'role'          => 'required|in:admin,staff',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $user->username = $request->username;
        $user->no_hp = $request->no_hp;
        $user->role = $request->role;
        $user->tanggal_lahir = $request->tanggal_lahir ?? $user->tanggal_lahir;
        
        $user->save();

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    // HAPUS USER
    public function destroy($id)
    {
        $user = User::whereIn('role', ['admin', 'staff'])->findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }
}