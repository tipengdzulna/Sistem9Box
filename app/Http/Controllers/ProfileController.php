<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'current_password'  => 'nullable|required_with:password|string',
            'password'          => 'nullable|string|min:6|confirmed',
        ], [
            'current_password.required_with' => 'Password lama wajib diisi jika ingin mengubah password.',
        ]);

        // Verify current password if changing password
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
