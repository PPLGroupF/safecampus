# Tes Selenium SafeCampus

Tes end-to-end yang menjalankan Chrome sungguhan untuk menguji fitur seperti pengguna.

## Menjalankan di laptop

1. Sekali saja: `pip install -r tests/selenium/requirements.txt`
2. Terminal 1: `php artisan migrate:fresh --seed` lalu `php artisan serve`
3. Terminal 2 (folder proyek): `pytest tests/selenium`

Menjalankan satu file saja: `pytest tests/selenium/test_smoke.py`

Browser tampil saat tes berjalan. Agar tidak tampil: set `SC_HEADLESS=1`
(PowerShell: `$env:SC_HEADLESS="1"`, Mac: `export SC_HEADLESS=1`).

## Struktur

- `conftest.py`: membuka dan menutup Chrome untuk setiap tes, screenshot otomatis saat gagal.
- `helpers.py`: fungsi bantu (login, isi form, buat laporan, buka laporan sebagai petugas).
- `test_*.py`: satu file untuk setiap PBI.

Di GitHub Actions, tes ini dijalankan otomatis oleh workflow **Selenium** (`.github/workflows/selenium.yml`).
