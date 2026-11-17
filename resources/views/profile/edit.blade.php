<x-app-layout>
    <x-slot name="title">Profil</x-slot>

    <x-page-header title="Profil Saya" subtitle="Lihat data akun Anda dan perbarui nama atau kata sandi." />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-1">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 text-2xl font-bold text-brand-700">
                    {{ strtoupper(collect(explode(' ', $user->nama))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('')) }}
                </div>
                <div class="mt-3 text-lg font-semibold text-gray-900" dusk="profile-name">{{ $user->nama }}</div>
                <x-badge class="mt-1" :color="['mahasiswa' => 'red', 'petugas' => 'amber', 'admin' => 'blue'][$user->role]">{{ $user->roleLabel() }}</x-badge>
            </div>

            <dl class="mt-6 space-y-4 border-t border-gray-100 pt-6 text-sm" dusk="profile-details">
                @if ($user->isMahasiswa())
                    <div>
                        <dt class="text-gray-500">NIM</dt>
                        <dd class="font-medium text-gray-900">{{ $user->nim ?? '-' }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500">Email</dt>
                    <dd class="font-medium text-gray-900 break-all">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Unit / Fakultas</dt>
                    <dd class="font-medium text-gray-900">{{ $user->unit?->nama_unit ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kampus</dt>
                    <dd class="font-medium text-gray-900">{{ $user->unit?->campus?->nama_kampus ?? '-' }}</dd>
                </div>
            </dl>

            <p class="mt-6 rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                Email, NIM, unit, dan peran hanya dapat diubah oleh admin. Hubungi admin jika data tersebut keliru.
            </p>
        </x-card>

        <div class="space-y-6 lg:col-span-2">
            <x-card title="Data Akun" subtitle="Nama ini dipakai saat Anda melapor dengan mode Teridentifikasi.">
                <form method="POST" action="{{ route('profil.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-form-field label="Nama lengkap" name="nama" required>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" maxlength="255" required class="sc-input" dusk="nama">
                    </x-form-field>

                    <div class="flex justify-end">
                        <x-btn dusk="save-profile">Simpan Perubahan</x-btn>
                    </div>
                </form>
            </x-card>

            <x-card title="Ubah Kata Sandi" subtitle="Gunakan minimal 8 karakter dan jangan pakai kata sandi akun lain.">
                <form method="POST" action="{{ route('profil.password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-form-field label="Kata sandi saat ini" name="current_password" required>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="sc-input" dusk="current_password">
                    </x-form-field>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-form-field label="Kata sandi baru" name="password" required>
                            <input type="password" id="password" name="password" minlength="8" required autocomplete="new-password" class="sc-input" dusk="password">
                        </x-form-field>

                        <x-form-field label="Ulangi kata sandi baru" name="password_confirmation" required>
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="sc-input" dusk="password_confirmation">
                        </x-form-field>
                    </div>

                    <div class="flex justify-end">
                        <x-btn dusk="save-password">Perbarui Kata Sandi</x-btn>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
