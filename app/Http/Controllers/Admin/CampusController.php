<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampusController extends Controller
{
    public function index(Request $request): View
    {
        $campuses = Campus::query()
            ->when($request->filled('q'), fn ($query) => $query->where('nama_kampus', 'like', '%'.$request->q.'%'))
            ->orderByDesc('is_active')
            ->orderBy('nama_kampus')
            ->paginate(10)
            ->withQueryString();

        return view('admin.campuses.index', compact('campuses'));
    }

    public function create(): View
    {
        return view('admin.campuses.create', ['campus' => new Campus()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Campus::create($this->validated($request));

        return redirect()->route('admin.campuses.index')->with('success', 'Kampus berhasil ditambahkan.');
    }

    public function edit(Campus $campus): View
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(Request $request, Campus $campus): RedirectResponse
    {
        $campus->update($this->validated($request, $campus));

        return redirect()->route('admin.campuses.index')->with('success', 'Kampus berhasil diperbarui.');
    }

    /**
     * Data kampus tidak dihapus permanen, hanya diaktifkan atau dinonaktifkan.
     */
    public function toggle(Campus $campus): RedirectResponse
    {
        if ($campus->is_active && $campus->hasActiveUnits()) {
            return redirect()->route('admin.campuses.index')
                ->with('error', 'Kampus tidak dapat dinonaktifkan karena masih memiliki unit/fakultas aktif.');
        }

        $campus->update(['is_active' => ! $campus->is_active]);

        $message = $campus->is_active ? 'Kampus diaktifkan kembali.' : 'Kampus dinonaktifkan.';

        return redirect()->route('admin.campuses.index')->with('success', $message);
    }

    private function validated(Request $request, ?Campus $campus = null): array
    {
        return $request->validate([
            'nama_kampus' => [
                'required', 'string', 'max:100',
                Rule::unique('campuses', 'nama_kampus')->ignore($campus?->id),
            ],
        ]);
    }
}
