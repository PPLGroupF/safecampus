@csrf

<div class="space-y-5">
    <x-form-field label="Nama kategori" name="nama_kategori" required hint="Contoh: Kekerasan Psikis">
        <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $category->nama_kategori) }}"
               maxlength="100" required class="sc-input" dusk="nama_kategori">
    </x-form-field>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-5">
        <x-btn variant="secondary" :href="route('admin.categories.index')">Batal</x-btn>
        <x-btn dusk="save-category">Simpan</x-btn>
    </div>
</div>
