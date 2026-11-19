<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('unit.campus'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($data);

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('profil.edit')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
