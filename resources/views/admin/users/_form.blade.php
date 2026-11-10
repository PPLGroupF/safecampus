@csrf

<div x-data="{ role: @js(old('role', $user->role ?? 'mahasiswa')) }" class="space-y-5">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-form-field label="Nama lengkap" name="nama" required class="sm:col-span-2">
            <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" maxlength="255" required class="sc-input" dusk="nama">
        </x-form-field>

        <x-form-field label="Email" name="email" required>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required class="sc-input" dusk="email">
        </x-form-field>

        <x-form-field label="Peran" name="role" required>
            <select id="role" name="role" x-model="role" required class="sc-input" dusk="role">
                @foreach (\App\Models\User::ROLES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </x-form-field>

        <x-form-field label="NIM" name="nim" required x-show="role === 'mahasiswa'" hint="Wajib dan unik untuk akun mahasiswa.">
            <input type="text" id="nim" name="nim" value="{{ old('nim', $user->nim) }}" maxlength="20" class="sc-input" dusk="nim" :disabled="role !== 'mahasiswa'">
        </x-form-field>

        <x-form-field label="Unit / Fakultas" name="unit_id" x-bind:class="role === 'mahasiswa' ? '' : 'sm:col-span-2'" hint="Wajib untuk mahasiswa. Dipakai sebagai unit asal pelapor.">
            <select id="unit_id" name="unit_id" class="sc-input" dusk="unit_id">
                <option value="">Tidak ada</option>
                @foreach ($units->groupBy(fn ($unit) => $unit->campus->nama_kampus) as $campus => $group)
                    <optgroup label="{{ $campus }}">
                        @foreach ($group as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id', $user->unit_id) == $unit->id)>{{ $unit->nama_unit }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </x-form-field>

        @if (! $user->exists)
            <x-form-field label="Kata sandi awal" name="password" required class="sm:col-span-2" hint="Minimal 8 karakter. Sampaikan ke pengguna dan minta ia menggantinya di halaman profil.">
                <input type="password" id="password" name="password" minlength="8" required autocomplete="new-password" class="sc-input" dusk="password">
            </x-form-field>
        @endif
    </div>

    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-5">
        <x-btn variant="secondary" :href="route('admin.users.index')">Batal</x-btn>
        <x-btn dusk="save-user">Simpan</x-btn>
    </div>
</div>
