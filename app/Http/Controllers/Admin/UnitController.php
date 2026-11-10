<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Models\Campus;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $units = Unit::query()
            ->with('campus')
            ->when($request->filled('q'), fn ($query) => $query->where('nama_unit', 'like', '%'.$request->q.'%'))
            ->when($request->filled('campus_id'), fn ($query) => $query->where('campus_id', $request->campus_id))
            ->orderByDesc('is_active')
            ->orderBy('nama_unit')
            ->paginate(10)
            ->withQueryString();

        $campuses = Campus::orderBy('nama_kampus')->get();

        return view('admin.units.index', compact('units', 'campuses'));
    }

    public function create(): View
    {
        return view('admin.units.create', [
            'unit' => new Unit(),
            'campuses' => $this->campusOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $unit = Unit::create($this->validated($request));
        AuditLogger::record('Menambah unit/fakultas '.$unit->nama_unit, 'unit');

        return redirect()->route('admin.units.index')->with('success', 'Unit/fakultas berhasil ditambahkan.');
    }

    public function edit(Unit $unit): View
    {
        return view('admin.units.edit', [
            'unit' => $unit,
            'campuses' => $this->campusOptions($unit),
        ]);
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($this->validated($request, $unit));
        AuditLogger::record('Mengubah unit/fakultas '.$unit->nama_unit, 'unit');

        return redirect()->route('admin.units.index')->with('success', 'Unit/fakultas berhasil diperbarui.');
    }

    /**
     * Unit tidak dihapus permanen agar riwayat laporan tetap utuh.
     */
    public function toggle(Unit $unit): RedirectResponse
    {
        if ($unit->is_active && $unit->hasActiveUsers()) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Unit/fakultas tidak dapat dinonaktifkan karena masih memiliki akun pengguna aktif.');
        }

        if (! $unit->is_active && ! $unit->campus->is_active) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Aktifkan kampus '.$unit->campus->nama_kampus.' terlebih dahulu.');
        }

        $unit->update(['is_active' => ! $unit->is_active]);
        AuditLogger::record(($unit->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' unit/fakultas '.$unit->nama_unit, 'unit');

        $message = $unit->is_active ? 'Unit/fakultas diaktifkan kembali.' : 'Unit/fakultas dinonaktifkan.';

        return redirect()->route('admin.units.index')->with('success', $message);
    }

    /**
     * Kampus aktif, ditambah kampus milik unit yang sedang diubah (walaupun nonaktif).
     */
    private function campusOptions(?Unit $unit = null)
    {
        return Campus::query()
            ->where('is_active', true)
            ->when($unit, fn ($query) => $query->orWhere('id', $unit->campus_id))
            ->orderBy('nama_kampus')
            ->get();
    }

    private function validated(Request $request, ?Unit $unit = null): array
    {
        return $request->validate([
            'campus_id' => [
                'required',
                Rule::exists('campuses', 'id')->where(function ($query) use ($unit) {
                    $query->where(function ($inner) use ($unit) {
                        $inner->where('is_active', true);
                        if ($unit) {
                            $inner->orWhere('id', $unit->campus_id);
                        }
                    });
                }),
            ],
            'nama_unit' => [
                'required', 'string', 'max:150',
                Rule::unique('units', 'nama_unit')
                    ->where('campus_id', $request->input('campus_id'))
                    ->ignore($unit?->id),
            ],
        ]);
    }
}
