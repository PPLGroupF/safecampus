<x-app-layout>
    <x-slot name="title">Kategori Kekerasan</x-slot>

    <x-page-header title="Kategori Kekerasan" subtitle="Kelola kategori kekerasan yang dapat dipilih pelapor. Kategori nonaktif tidak muncul pada formulir laporan, tetapi laporan lama tetap terhubung.">
        <x-slot name="actions">
            <x-btn :href="route('admin.categories.create')" icon="plus" dusk="add-category">Tambah Kategori</x-btn>
        </x-slot>
    </x-page-header>

    <x-card :padding="false">
        <form method="GET" class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama kategori..." class="sc-input pl-10" dusk="search">
            </div>
            <x-btn variant="secondary">Cari</x-btn>
        </form>

        @if ($categories->isEmpty())
            <x-empty-state title="Belum ada kategori" icon="tag">Tambahkan kategori pertama dengan tombol Tambah Kategori.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="category-table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Nama Kategori</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($categories as $category)
                            <tr dusk="category-row-{{ $category->id }}">
                                <td class="text-gray-400">{{ $categories->firstItem() + $loop->index }}</td>
                                <td class="font-medium text-gray-900">{{ $category->nama_kategori }}</td>
                                <td>
                                    @if ($category->is_active)
                                        <x-badge color="green">Aktif</x-badge>
                                    @else
                                        <x-badge color="slate">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-btn variant="secondary" size="sm" icon="pencil" :href="route('admin.categories.edit', $category)" dusk="edit-category-{{ $category->id }}">Ubah</x-btn>
                                        <form method="POST" action="{{ route('admin.categories.toggle', $category) }}"
                                              onsubmit="return confirm('{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }} kategori ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <x-btn :variant="$category->is_active ? 'danger' : 'secondary'" size="sm" :icon="$category->is_active ? 'ban' : 'check'" dusk="toggle-category-{{ $category->id }}">
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </x-btn>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $categories->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
