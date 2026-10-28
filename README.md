# SafeCampus

Sistem informasi pelaporan dan pemantauan kasus kekerasan berbasis gender, sebagai pengembangan lanjutan layanan Satgas PPKPT Telkom University. Tugas besar Proyek Perangkat Lunak, Kelompok F.

> Prototipe tugas kuliah. Semua data adalah data dummy.

## Teknologi

- PHP 8.2+, Laravel 11, Laravel Breeze (Blade)
- MySQL 8 (XAMPP)
- Tailwind CSS (Vite)
- Laravel Dusk untuk pengujian end-to-end

## Menjalankan di komputer lokal

```bash
composer install
npm install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Buat database `safecampus_ppl` di phpMyAdmin, lalu:

```bash
php artisan migrate --seed
npm run build                 # atau: npm run dev (saat mengembangkan)
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun dummy

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | admin@safecampus.test | password |
| Petugas Satgas | petugas@safecampus.test | password |
| Mahasiswa | mahasiswa@safecampus.test | password |

## Struktur yang perlu diketahui

- `routes/pbi/` — satu file route per PBI. Dimuat otomatis oleh `routes/web.php`, jadi tidak perlu mengubah `web.php`.
- `config/sidebar.php` — semua menu sidebar per peran. Menu tampil otomatis setelah route-nya ada.
- `resources/views/components/` — komponen UI bersama (`x-card`, `x-btn`, `x-badge`, `x-page-header`, dan lainnya).
- `database/seeders/DatabaseSeeder.php` — menjalankan seeder sesuai urutan dependensi; seeder yang belum ada dilewati.

## Alur kerja Git

1. `git pull` di branch `main` sebelum mulai.
2. Buat branch dari key Jira: `SAF-xxx-deskripsi-singkat`.
3. Commit dengan key Jira di awal pesan: `SAF-xxx deskripsi`.
4. Push, buat Pull Request ke `main`, tunggu review dari PM.
