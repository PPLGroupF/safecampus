<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer'],
            'modul' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:berhasil,gagal'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $logs = AuditLog::query()
            ->with('user:id,nama,email')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(function ($inner) use ($q) {
                $inner->where('aksi', 'like', "%{$q}%")->orWhere('kode_tiket', 'like', "%{$q}%");
            }))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['modul'] ?? null, fn ($query, $modul) => $query->where('modul', $modul))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['dari'] ?? null, fn ($query, $dari) => $query->whereDate('created_at', '>=', $dari))
            ->when($filters['sampai'] ?? null, fn ($query, $sampai) => $query->whereDate('created_at', '<=', $sampai))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('nama')->get(['id', 'nama', 'role']);
        $modules = AuditLog::MODULES;

        return view('admin.audit-logs.index', compact('logs', 'users', 'modules'));
    }
}
