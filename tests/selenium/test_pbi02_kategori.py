"""Regresi PBI-02 Kelola kategori kekerasan (admin): tambah, nama unik, ubah, nonaktifkan, dan hak akses."""
from selenium.webdriver.common.by import By

from helpers import (AKUN, buka, el, isi, klik, klik_dan_tunggu, lihat, login, teks, terima_konfirmasi, unik)


def _tambah_kategori(driver, base_url, nama):
    buka(driver, base_url, "/admin/kategori")
    klik_dan_tunggu(driver, "add-category")
    isi(driver, "nama_kategori", nama)
    klik_dan_tunggu(driver, "save-category")


def _id_kategori(driver, base_url, nama):
    buka(driver, base_url, "/admin/kategori?q=" + nama)
    baris = el(driver, "category-table").find_element(By.CSS_SELECTOR, '[dusk^="category-row-"]')
    return baris.get_attribute("dusk").split("-")[-1]


def test_admin_menambah_dan_mengubah_kategori(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    nama = unik("Kategori Uji")
    _tambah_kategori(driver, base_url, nama)
    lihat(driver, "Kategori berhasil ditambahkan.")
    buka(driver, base_url, "/admin/kategori?q=" + nama)
    lihat(driver, nama, "category-table")

    klik_dan_tunggu(driver, f"edit-category-{_id_kategori(driver, base_url, nama)}")
    isi(driver, "nama_kategori", nama + " Baru")
    klik_dan_tunggu(driver, "save-category")
    lihat(driver, "Kategori berhasil diperbarui.")
    buka(driver, base_url, "/admin/kategori?q=" + nama)
    lihat(driver, nama + " Baru", "category-table")


def test_nama_kategori_harus_unik(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    _tambah_kategori(driver, base_url, "Perundungan")
    lihat(driver, "sudah digunakan", "error-nama_kategori")


def test_kategori_nonaktif_tidak_muncul_di_formulir_laporan(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    nama = unik("Kategori Sementara")
    _tambah_kategori(driver, base_url, nama)
    kategori_id = _id_kategori(driver, base_url, nama)
    klik(driver, f"toggle-category-{kategori_id}")
    terima_konfirmasi(driver)
    lihat(driver, "dinonaktifkan")
    buka(driver, base_url, "/admin/kategori?q=" + nama)
    lihat(driver, "Nonaktif", f"category-row-{kategori_id}")

    login(driver, base_url, AKUN["mahasiswa"])
    buka(driver, base_url, "/laporan/buat")
    pilihan = [o.text for o in el(driver, "category_id").find_elements(By.TAG_NAME, "option")]
    assert nama not in pilihan


def test_non_admin_tidak_dapat_membuka_halaman_kategori(driver, base_url):
    login(driver, base_url, AKUN["petugas"])
    buka(driver, base_url, "/admin/kategori")
    assert "403" in teks(driver)
