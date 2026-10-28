<?php

/*
|--------------------------------------------------------------------------
| Menu sidebar per peran
|--------------------------------------------------------------------------
| Semua menu sudah didaftarkan sejak awal. Sebuah menu baru tampil
| setelah route-nya dibuat oleh PBI terkait, jadi file ini tidak perlu
| diubah saat fitur baru ditambahkan.
|
| route  : nama route tujuan
| active : pola nama route yang membuat menu terlihat aktif
*/

return [
    'mahasiswa' => [
        ['label' => 'Beranda', 'icon' => 'home', 'route' => 'mahasiswa.beranda', 'active' => 'mahasiswa.beranda'],
        ['label' => 'Buat Laporan', 'icon' => 'document-plus', 'route' => 'laporan.create', 'active' => 'laporan.create'],
        ['label' => 'Laporan Saya', 'icon' => 'document', 'route' => 'laporan.index', 'active' => ['laporan.index', 'laporan.show', 'laporan.edit']],
        ['label' => 'Panduan Pelaporan', 'icon' => 'book', 'route' => 'panduan', 'active' => 'panduan'],
        ['label' => 'Notifikasi', 'icon' => 'bell', 'route' => 'notifikasi.index', 'active' => 'notifikasi.*'],
        ['label' => 'Profil', 'icon' => 'user', 'route' => 'profil.edit', 'active' => 'profil.*'],
    ],

    'petugas' => [
        ['label' => 'Antrean Laporan', 'icon' => 'inbox', 'route' => 'petugas.antrean', 'active' => ['petugas.antrean', 'petugas.laporan.*']],
        ['label' => 'Notifikasi', 'icon' => 'bell', 'route' => 'notifikasi.index', 'active' => 'notifikasi.*'],
        ['label' => 'Profil', 'icon' => 'user', 'route' => 'profil.edit', 'active' => 'profil.*'],
    ],

    'admin' => [
        ['label' => 'Dashboard', 'icon' => 'chart', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Pengguna', 'icon' => 'users', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
        ['label' => 'Kampus', 'icon' => 'building', 'route' => 'admin.campuses.index', 'active' => 'admin.campuses.*'],
        ['label' => 'Unit / Fakultas', 'icon' => 'academic', 'route' => 'admin.units.index', 'active' => 'admin.units.*'],
        ['label' => 'Kategori', 'icon' => 'tag', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
        ['label' => 'Audit Log', 'icon' => 'clock', 'route' => 'admin.audit-logs.index', 'active' => 'admin.audit-logs.*'],
        ['label' => 'Ekspor Rekap', 'icon' => 'download', 'route' => 'admin.rekap.index', 'active' => 'admin.rekap.*'],
        ['label' => 'Notifikasi', 'icon' => 'bell', 'route' => 'notifikasi.index', 'active' => 'notifikasi.*'],
        ['label' => 'Profil', 'icon' => 'user', 'route' => 'profil.edit', 'active' => 'profil.*'],
    ],
];
