<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Profil selalu diambil dari akun yang sedang login ($request->user()),
 * sehingga tidak ada parameter ID yang bisa dipakai untuk membuka profil orang lain.
 * Hanya kolom nama dan kata sandi yang dapat diubah sendiri.
 */
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

        // Hanya kolom yang divalidasi yang disimpan; peran, email, NIM, dan unit diabaikan.
        $request->user()->update(['nama' => $data['nama']]);
        AuditLogger::record('Mengubah profil', 'profil');

        return redirect()->route('profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);
        AuditLogger::record('Mengganti kata sandi', 'profil');

        return redirect()->route('profil.edit')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
