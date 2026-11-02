<x-app-layout>
    <x-slot name="title">Data Kampus</x-slot>

    <x-page-header title="Data Kampus" subtitle="Kelola daftar kampus Telkom University. Kampus yang dinonaktifkan tidak muncul pada pilihan baru.">
        <x-slot name="actions">
            <x-btn :href="route('admin.campuses.create')" icon="plus" dusk="add-campus">Tambah Kampus</x-btn>
        </x-slot>
    </x-page-header>

    <x-card :padding="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama kampus..." class="sc-input pl-10" dusk="search">
            </div>
            <x-btn variant="secondary">Cari</x-btn>
        </form>

        @if ($campuses->isEmpty())
            <x-empty-state title="Belum ada kampus" icon="building">Tambahkan kampus pertama dengan tombol Tambah Kampus.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="campus-table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Nama Kampus</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($campuses as $campus)
                            <tr dusk="campus-row-{{ $campus->id }}">
                                <td class="text-gray-400">{{ $campuses->firstItem() + $loop->index }}</td>
                                <td class="font-medium text-gray-900">{{ $campus->nama_kampus }}</td>
                                <td>
                                    @if ($campus->is_active)
                                        <x-badge color="green">Aktif</x-badge>
                                    @else
                                        <x-badge color="slate">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-btn variant="secondary" size="sm" icon="pencil" :href="route('admin.campuses.edit', $campus)" dusk="edit-campus-{{ $campus->id }}">Ubah</x-btn>
                                        <form method="POST" action="{{ route('admin.campuses.toggle', $campus) }}"
                                              onsubmit="return confirm('{{ $campus->is_active ? 'Nonaktifkan' : 'Aktifkan' }} kampus ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <x-btn :variant="$campus->is_active ? 'danger' : 'secondary'" size="sm" :icon="$campus->is_active ? 'ban' : 'check'" dusk="toggle-campus-{{ $campus->id }}">
                                                {{ $campus->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </x-btn>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $campuses->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
