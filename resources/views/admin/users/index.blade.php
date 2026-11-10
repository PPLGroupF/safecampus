<x-app-layout>
    <x-slot name="title">Pengguna</x-slot>

    <x-page-header title="Manajemen Pengguna" subtitle="Kelola akun mahasiswa, petugas Satgas, dan admin. Akun tidak dihapus, hanya dinonaktifkan.">
        <x-slot name="actions">
            <x-btn :href="route('admin.users.create')" icon="plus" dusk="add-user">Tambah Pengguna</x-btn>
        </x-slot>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total pengguna" :value="$counts['total']" icon="users" color="gray" />
        <x-stat-card label="Mahasiswa" :value="$counts['mahasiswa']" icon="academic" color="red" />
        <x-stat-card label="Petugas Satgas" :value="$counts['petugas']" icon="shield" color="amber" />
        <x-stat-card label="Admin" :value="$counts['admin']" icon="lock" color="blue" />
    </div>

    <x-card :padding="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 lg:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, atau email..." class="sc-input pl-10" dusk="search">
            </div>
            <select name="role" class="sc-input lg:w-48" dusk="filter-role">
                <option value="">Semua peran</option>
                @foreach (\App\Models\User::ROLES as $key => $label)
                    <option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="sc-input lg:w-40" dusk="filter-status">
                <option value="">Semua status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
            <x-btn variant="secondary" dusk="apply-filter">Terapkan</x-btn>
        </form>

        @if ($users->isEmpty())
            <x-empty-state title="Tidak ada pengguna" icon="users">Tidak ada akun yang cocok dengan pencarian.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="user-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Peran</th>
                            <th>Unit / Fakultas</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr dusk="user-row-{{ $user->id }}">
                                <td>
                                    <div class="font-medium text-gray-900">{{ $user->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->nim ? $user->nim.' · ' : '' }}{{ $user->email }}</div>
                                </td>
                                <td>
                                    <x-badge :color="['mahasiswa' => 'red', 'petugas' => 'amber', 'admin' => 'blue'][$user->role]">{{ $user->roleLabel() }}</x-badge>
                                </td>
                                <td class="text-gray-600">{{ $user->unit?->nama_unit ?? '-' }}</td>
                                <td>
                                    @if ($user->is_active)
                                        <x-badge color="green">Aktif</x-badge>
                                    @else
                                        <x-badge color="slate">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-btn variant="secondary" size="sm" icon="pencil" :href="route('admin.users.edit', $user)" dusk="edit-user-{{ $user->id }}">Ubah</x-btn>
                                        @unless ($user->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}"
                                                  onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <x-btn :variant="$user->is_active ? 'danger' : 'secondary'" size="sm" :icon="$user->is_active ? 'ban' : 'check'" dusk="toggle-user-{{ $user->id }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </x-btn>
                                            </form>
                                        @endunless
                                        @if (Route::has('admin.users.security'))
                                            <x-btn variant="ghost" size="sm" icon="lock" :href="route('admin.users.security', $user)" dusk="security-user-{{ $user->id }}">Keamanan</x-btn>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $users->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
