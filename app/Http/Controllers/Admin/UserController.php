<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pengelolaan akun oleh admin. Fitur ini sengaja tidak menampilkan data laporan apa pun.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('unit')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(fn ($inner) => $inner->where('nama', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->role))
            ->when($request->status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($request->status === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('is_active')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'total' => User::count(),
            'mahasiswa' => User::where('role', User::ROLE_MAHASISWA)->count(),
            'petugas' => User::where('role', User::ROLE_PETUGAS)->count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(['role' => User::ROLE_MAHASISWA]),
            'units' => $this->unitOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        // Kata sandi awal disimpan dalam bentuk hash, tidak pernah dalam teks asli.
        $data['password'] = Hash::make($data['password']);

        User::create($data + ['is_active' => true]);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'units' => $this->unitOptions($user),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        // Admin tidak boleh menurunkan perannya sendiri agar tidak terkunci dari sistem.
        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Akun tidak dihapus permanen agar riwayat laporan dan audit tetap utuh.
     */
    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $message = $user->is_active ? 'Akun diaktifkan kembali.' : 'Akun dinonaktifkan.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    private function unitOptions(?User $user = null)
    {
        return Unit::query()
            ->with('campus')
            ->where(fn ($query) => $query->where('is_active', true)->when($user?->unit_id, fn ($q) => $q->orWhere('id', $user->unit_id)))
            ->orderBy('nama_unit')
            ->get();
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $isMahasiswa = $request->input('role') === User::ROLE_MAHASISWA;

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'nim' => [
                Rule::requiredIf($isMahasiswa), 'nullable', 'string', 'max:20', 'regex:/^[0-9]+$/',
                Rule::unique('users', 'nim')->ignore($user?->id),
            ],
            'unit_id' => [Rule::requiredIf($isMahasiswa), 'nullable', Rule::exists('units', 'id')],
            'password' => [$user ? 'prohibited' : 'required', 'string', 'min:8'],
        ], [
            'nim.regex' => 'NIM hanya boleh berisi angka.',
        ]);

        // NIM khusus mahasiswa.
        if (! $isMahasiswa) {
            $data['nim'] = null;
        }

        return $data;
    }
}
