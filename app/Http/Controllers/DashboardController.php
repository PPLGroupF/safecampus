<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Mengarahkan pengguna ke halaman utama sesuai perannya.
 * Jika halaman tujuan belum dibuat, tampilkan halaman sambutan umum.
 */
class DashboardController extends Controller
{
    private const HOME_ROUTES = [
        'mahasiswa' => 'mahasiswa.beranda',
        'petugas' => 'petugas.antrean',
        'admin' => 'admin.dashboard',
    ];

    public function __invoke(Request $request)
    {
        $target = self::HOME_ROUTES[$request->user()->role] ?? null;

        if ($target && Route::has($target)) {
            return redirect()->route($target);
        }

        return view('dashboard');
    }
}
