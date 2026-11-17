<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.beranda');
    }

    public function panduan(): View
    {
        return view('mahasiswa.panduan');
    }
}
