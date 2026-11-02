@csrf

<div class="space-y-5">
    <x-form-field label="Nama kampus" name="nama_kampus" required hint="Contoh: Telkom University Bandung">
        <input type="text" id="nama_kampus" name="nama_kampus" value="{{ old('nama_kampus', $campus->nama_kampus) }}"
               maxlength="100" required class="sc-input" dusk="nama_kampus">
    </x-form-field>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-5">
        <x-btn variant="secondary" :href="route('admin.campuses.index')">Batal</x-btn>
        <x-btn dusk="save-campus">Simpan</x-btn>
    </div>
</div>
