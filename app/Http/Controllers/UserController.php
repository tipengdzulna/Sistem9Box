<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("FIELD(role,'super_admin','admin','operator')")->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:super_admin,admin,operator',
            'ue1' => 'required_if:role,operator|nullable|integer|min:1|max:14',
        ], ['ue1.required_if' => 'UE1 wajib dipilih untuk Operator.']);

        $v['password'] = Hash::make($v['password']);
        if ($v['role'] !== 'operator') $v['ue1'] = null;

        User::create($v);
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:super_admin,admin,operator',
            'ue1' => 'required_if:role,operator|nullable|integer|min:1|max:14',
        ], ['ue1.required_if' => 'UE1 wajib dipilih untuk Operator.']);

        if ($v['role'] !== 'operator') $v['ue1'] = null;
        if (!empty($v['password'])) {
            $v['password'] = Hash::make($v['password']);
        } else {
            unset($v['password']);
        }

        $user->update($v);
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
