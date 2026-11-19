<x-card title="Informasi & Bantuan" dusk="help-card">
    <ul class="space-y-4 text-sm">
        <li class="flex gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600"><x-icon name="phone" /></div>
            <div>
                <div class="font-semibold text-gray-900">Kondisi darurat</div>
                <p class="text-gray-600">Jika Anda dalam bahaya, segera hubungi keamanan kampus atau layanan darurat 112.</p>
            </div>
        </li>
        <li class="flex gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600"><x-icon name="building" /></div>
            <div>
                <div class="font-semibold text-gray-900">Posko Satgas PPKPT</div>
                <p class="text-gray-600">Datang langsung ke ruang pengaduan Satgas PPKPT di Gedung Bangkit, Telkom University.</p>
            </div>
        </li>
        <li class="flex gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"><x-icon name="book" /></div>
            <div>
                <div class="font-semibold text-gray-900">Panduan pelaporan</div>
                <p class="text-gray-600">Pelajari cara melapor dan hak Anda. <a href="{{ route('panduan') }}" class="font-medium text-brand-600 hover:text-brand-700">Buka panduan</a></p>
            </div>
        </li>
    </ul>
</x-card>
