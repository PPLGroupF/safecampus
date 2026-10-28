<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Route per PBI
|--------------------------------------------------------------------------
| Setiap PBI memiliki file route sendiri di folder routes/pbi/.
| File-file tersebut dimuat otomatis, sehingga tidak ada yang perlu
| mengubah file web.php ini saat menambah fitur baru.
*/
foreach (glob(__DIR__.'/pbi/*.php') as $routeFile) {
    require $routeFile;
}
