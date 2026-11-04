<x-app-layout>
    <x-slot name="title">Unit / Fakultas</x-slot>

    <x-page-header title="Unit / Fakultas" subtitle="Kelola unit dan fakultas per kampus. Data ini dipakai untuk mengelompokkan asal pelapor pada rekap statistik.">
        <x-slot name="actions">
            <x-btn :href="route('admin.units.create')" icon="plus" dusk="add-unit">Tambah Unit</x-btn>
        </x-slot>
    </x-page-header>

    <x-card :padding="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama unit/fakultas..." class="sc-input pl-10" dusk="search">
            </div>
            <select name="campus_id" class="sc-input sm:w-64" dusk="filter-campus">
                <option value="">Semua kampus</option>
                @foreach ($campuses as $campus)
                    <option value="{{ $campus->id }}" @selected(request('campus_id') == $campus->id)>{{ $campus->nama_kampus }}</option>
                @endforeach
            </select>
            <x-btn variant="secondary">Terapkan</x-btn>
        </form>

        @if ($units->isEmpty())
            <x-empty-state title="Belum ada unit/fakultas" icon="academic">Tambahkan unit dengan tombol Tambah Unit.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="unit-table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Nama Unit / Fakultas</th>
                            <th>Kampus</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($units as $unit)
                            <tr dusk="unit-row-{{ $unit->id }}">
                                <td class="text-gray-400">{{ $units->firstItem() + $loop->index }}</td>
                                <td class="font-medium text-gray-900">{{ $unit->nama_unit }}</td>
                                <td>{{ $unit->campus->nama_kampus }}</td>
                                <td>
                                    @if ($unit->is_active)
                                        <x-badge color="green">Aktif</x-badge>
                                    @else
                                        <x-badge color="slate">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-btn variant="secondary" size="sm" icon="pencil" :href="route('admin.units.edit', $unit)" dusk="edit-unit-{{ $unit->id }}">Ubah</x-btn>
                                        <form method="POST" action="{{ route('admin.units.toggle', $unit) }}"
                                              onsubmit="return confirm('{{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }} unit ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <x-btn :variant="$unit->is_active ? 'danger' : 'secondary'" size="sm" :icon="$unit->is_active ? 'ban' : 'check'" dusk="toggle-unit-{{ $unit->id }}">
                                                {{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </x-btn>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $units->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
