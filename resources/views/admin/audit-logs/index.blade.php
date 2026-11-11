<x-app-layout>
    <x-slot name="title">Audit Log</x-slot>

    <x-page-header title="Audit Log" subtitle="Rekam jejak aktivitas pengguna. Log hanya dapat dilihat dan tidak memuat isi laporan maupun kronologi." />

    <x-card :padding="false">
        <form method="GET" class="grid gap-3 border-b border-gray-100 p-4 sm:grid-cols-2 lg:grid-cols-6">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari aksi atau kode tiket..." class="sc-input lg:col-span-2" dusk="filter-q">

            <select name="user_id" class="sc-input" dusk="filter-user">
                <option value="">Semua pengguna</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->nama }} ({{ $user->roleLabel() }})</option>
                @endforeach
            </select>

            <select name="modul" class="sc-input" dusk="filter-modul">
                <option value="">Semua modul</option>
                @foreach ($modules as $key => $label)
                    <option value="{{ $key }}" @selected(request('modul') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="dari" value="{{ request('dari') }}" class="sc-input" aria-label="Dari tanggal" dusk="filter-dari">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="sc-input" aria-label="Sampai tanggal" dusk="filter-sampai">

            <div class="flex gap-2 lg:col-span-6 lg:justify-end">
                <select name="status" class="sc-input sm:w-40" dusk="filter-status">
                    <option value="">Semua status</option>
                    <option value="berhasil" @selected(request('status') === 'berhasil')>Berhasil</option>
                    <option value="gagal" @selected(request('status') === 'gagal')>Gagal</option>
                </select>
                <x-btn variant="secondary" :href="route('admin.audit-logs.index')">Reset</x-btn>
                <x-btn dusk="apply-filter">Terapkan</x-btn>
            </div>
        </form>

        @if ($errors->any())
            <div class="border-b border-gray-100 px-4 py-3 text-sm text-red-600">{{ $errors->first() }}</div>
        @endif

        @if ($logs->isEmpty())
            <x-empty-state title="Tidak ada aktivitas" icon="clock">Belum ada aktivitas yang cocok dengan filter.</x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="sc-table" dusk="audit-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Kode Tiket</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap text-gray-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    @if ($log->user)
                                        <div class="font-medium text-gray-900">{{ $log->user->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->peran ? \App\Models\User::ROLES[$log->peran] : '' }}</div>
                                    @elseif ($log->peran === 'mahasiswa')
                                        <div class="font-medium text-gray-900">Pelapor (anonim)</div>
                                        <div class="text-xs text-gray-500">Mahasiswa</div>
                                    @else
                                        <span class="text-gray-400">Tidak dikenal</span>
                                    @endif
                                </td>
                                <td class="text-gray-900">{{ $log->aksi }}</td>
                                <td><x-badge>{{ $log->modulLabel() }}</x-badge></td>
                                <td class="font-mono text-xs">{{ $log->kode_tiket ?? '-' }}</td>
                                <td>
                                    <x-badge :color="$log->status === 'berhasil' ? 'green' : 'red'">{{ ucfirst($log->status) }}</x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">{{ $logs->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
