@csrf

<div class="space-y-5">
    <x-form-field label="Kampus" name="campus_id" required>
        <select id="campus_id" name="campus_id" required class="sc-input" dusk="campus_id">
            <option value="">Pilih kampus</option>
            @foreach ($campuses as $campus)
                <option value="{{ $campus->id }}" @selected(old('campus_id', $unit->campus_id) == $campus->id)>
                    {{ $campus->nama_kampus }}{{ $campus->is_active ? '' : ' (nonaktif)' }}
                </option>
            @endforeach
        </select>
    </x-form-field>

    <x-form-field label="Nama unit / fakultas" name="nama_unit" required hint="Nama tidak boleh sama dengan unit lain di kampus yang sama.">
        <input type="text" id="nama_unit" name="nama_unit" value="{{ old('nama_unit', $unit->nama_unit) }}"
               maxlength="150" required class="sc-input" dusk="nama_unit">
    </x-form-field>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-5">
        <x-btn variant="secondary" :href="route('admin.units.index')">Batal</x-btn>
        <x-btn dusk="save-unit">Simpan</x-btn>
    </div>
</div>
