<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Petugas;
use App\Models\Penumpang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userscontroller extends Controller
{
    public function index()
    {
        $admins = Admin::where('role', '!=', 'umum')->get();
        $petugas = Petugas::all();
        $penumpang = Penumpang::whereNotNull('username')
            ->where('username', '!=', '')
            ->get();

        return view('admin.users', [
            'admins' => $admins,
            'petugas' => $petugas,
            'penumpang' => $penumpang,
        ]);
    }


    public function create()
    {
        return view('admin.createusers');
    }

    public function store(Request $request)
    {
        if ($request->has('role')) {
            // Tambah admin
            $request->validate([
                'nama_admin' => 'required|max:50',
                'tanggal_lahir' => 'required|max:30|unique:admin,tanggal_lahir',
                'username' => 'required|max:30|unique:admin,username',
                'password' => 'required|min:6',
                'role' => 'required|in:padang,solok,sawah_lunto',
            ]);

            Admin::create([
                'nama_admin' => $request->nama_admin,
                'username' => $request->username,
                'tanggal_lahir' => $request->tanggal_lahir,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('users.index')->with('success', 'Admin berhasil ditambahkan.');
        } else {
            // Tambah petugas
            $request->validate([
                'nama_petugas' => 'required|max:50',
                'username' => 'required|max:30|unique:petugas,username',
                'password' => 'required|min:6',
            ]);

            Petugas::create([
                'nama_petugas' => $request->nama_petugas,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('users.index')->with('success', 'Petugas berhasil ditambahkan.');
        }
    }


    public function edit($id)
    {
        $admin = Admin::where('id_admin', $id)->first();
        if ($admin && $admin->role !== 'umum') {
            return view('admin.editusers', [
                'user' => $admin,
                'tipe' => 'admin'
            ]);
        }

        $petugas = Petugas::where('id_petugas', $id)->first();
        if ($petugas) {
            return view('admin.editusers', [
                'user' => $petugas,
                'tipe' => 'petugas'
            ]);
        }

        abort(404, 'Pengguna tidak ditemukan.');
    }


    public function update(Request $request, $id)
    {
        $admin = Admin::where('id_admin', $id)->first();
        if ($admin && $admin->role !== 'umum') {
            $request->validate([
                'nama_admin' => 'required|max:50',
                'username' => 'required|max:30|unique:admin,username,' . $id . ',id_admin',
                'role' => 'required|in:padang,solok,sawah_lunto',
                'password' => 'nullable|min:6',
            ]);

            $admin->nama_admin = $request->nama_admin;
            $admin->username = $request->username;
            $admin->role = $request->role;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();

            return redirect()->route('users.index')->with('success', 'Data Admin Berhasil Diupdate.');
        }

        $petugas = Petugas::where('id_petugas', $id)->first();
        if (!$petugas) {
            abort(404, 'Petugas tidak ditemukan.');
        }

        $request->validate([
            'nama_petugas' => 'required|max:50',
            'username' => 'required|max:30|unique:petugas,username,' . $id . ',id_petugas',
            'password' => 'nullable|min:6',
        ]);

        $petugas->nama_petugas = $request->nama_petugas;
        $petugas->username = $request->username;

        if ($request->filled('password')) {
            $petugas->password = Hash::make($request->password);
        }

        $petugas->save();

        return redirect()->route('users.index')->with('success', 'Data Petugas Berhasil Diupdate.');
    }

    public function destroy($id)
    {
        $tipe = request('tipe');

        if ($tipe === 'admin') {
            $admin = Admin::where('id_admin', $id)->first();
            if ($admin && $admin->role !== 'umum') {
                $admin->delete();
                return redirect()->route('users.index')->with('success', 'Data Admin berhasil dihapus.');
            }
            return redirect()->route('users.index')->with('error', 'Data Admin tidak ditemukan.');
        }

        if ($tipe === 'petugas') {
            $petugas = Petugas::where('id_petugas', $id)->first();
            if ($petugas) {
                $petugas->delete();
                return redirect()->route('users.index')->with('success', 'Data Petugas berhasil dihapus.');
            }
            return redirect()->route('users.index')->with('error', 'Data Petugas tidak ditemukan.');
        }

        if ($tipe === 'penumpang') {
            $penumpang = penumpang::where('id', $id)->first();
            if ($penumpang) {
                $penumpang->delete();
                return redirect()->route('users.index')->with('success', 'Data penumpang berhasil dihapus.');
            }
            return redirect()->route('users.index')->with('error', 'Data penumpang tidak ditemukan.');
        }

        abort(404, 'Data pengguna tidak ditemukan.');
    }
}
