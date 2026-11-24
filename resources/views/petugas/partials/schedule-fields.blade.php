{{-- Field form jadwal pendampingan (dipakai saat membuat dan mengubah jadwal). --}}
@php($schedule ??= null)
<div class="grid gap-3 sm:grid-cols-2">
    <div class="space-y-1.5">
        <label for="jenis" class="sc-label">Jenis pendampingan <span class="text-brand-600">*</span></label>
        <select id="jenis" name="jenis" class="sc-input" dusk="schedule-jenis">
            @foreach (\App\Models\Schedule::JENIS as $value => $label)
                <option value="{{ $value }}" @selected(old('jenis', $schedule?->jenis) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('jenis')<p class="text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="space-y-1.5">
        <label for="waktu" class="sc-label">Tanggal dan jam <span class="text-brand-600">*</span></label>
        <input id="waktu" type="datetime-local" name="waktu" class="sc-input" dusk="schedule-waktu"
               value="{{ old('waktu', $schedule?->waktu?->format('Y-m-d\TH:i')) }}">
        @error('waktu')<p class="text-xs font-medium text-red-600" dusk="error-waktu">{{ $message }}</p>@enderror
    </div>
    <div class="space-y-1.5 sm:col-span-2">
        <label for="lokasi" class="sc-label">Lokasi <span class="text-brand-600">*</span></label>
        <input id="lokasi" type="text" name="lokasi" maxlength="150" class="sc-input" placeholder="Contoh: Ruang Satgas PPKPT, Gedung Rektorat Lt. 2 atau Daring (Zoom)"
               value="{{ old('lokasi', $schedule?->lokasi) }}" dusk="schedule-lokasi">
        @error('lokasi')<p class="text-xs font-medium text-red-600" dusk="error-lokasi">{{ $message }}</p>@enderror
    </div>
    <div class="space-y-1.5 sm:col-span-2">
        <label for="catatan_jadwal" class="sc-label">Catatan untuk pelapor (opsional)</label>
        <textarea id="catatan_jadwal" name="catatan" rows="2" maxlength="500" class="sc-input" dusk="schedule-catatan">{{ old('catatan', $schedule?->catatan) }}</textarea>
        <p class="text-xs text-gray-500">Catatan ini dapat dibaca pelapor. Untuk catatan rahasia Satgas, gunakan Catatan Internal.</p>
        @error('catatan')<p class="text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
