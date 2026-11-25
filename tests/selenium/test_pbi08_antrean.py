"""Regresi PBI-08 Antrean dan detail laporan (petugas): daftar, filter, detail terdekripsi, identitas anonim, hak akses."""
from selenium.webdriver.common.by import By

from helpers import (AKUN, buat_laporan, buka, buka_laporan_petugas, el, klik_dan_tunggu, lihat, login, pilih, teks)


def test_antrean_menampilkan_laporan_dan_dapat_difilter(driver, base_url):
    login(driver, base_url, AKUN["mahasiswa"])
    kode = buat_laporan(driver, base_url)

    login(driver, base_url, AKUN["petugas"])
    buka(driver, base_url, "/petugas/antrean")
    el(driver, "stat-baru")
    # Antrean berhalaman, jadi laporan baru dicari lewat kotak pencarian kode tiket.
    buka(driver, base_url, "/petugas/antrean?q=" + kode)
    lihat(driver, kode, "queue-table")

    buka(driver, base_url, "/petugas/antrean?q=" + kode)
    pilih(driver, "filter-status", value="selesai")
    klik_dan_tunggu(driver, "apply-filter")
    assert kode not in teks(driver)


def test_detail_menampilkan_kronologi_dan_identitas_laporan_teridentifikasi(driver, base_url):
    login(driver, base_url, AKUN["mahasiswa"])
    kode = buat_laporan(driver, base_url, mode="teridentifikasi")

    login(driver, base_url, AKUN["petugas"])
    buka_laporan_petugas(driver, base_url, kode)
    lihat(driver, "Data dummy Selenium", "kronologi")
    lihat(driver, "Mahasiswa Pelapor", "reporter-name")


def test_identitas_pelapor_anonim_disembunyikan(driver, base_url):
    login(driver, base_url, AKUN["mahasiswa"])
    kode = buat_laporan(driver, base_url, mode="anonim")

    login(driver, base_url, AKUN["petugas"])
    buka_laporan_petugas(driver, base_url, kode)
    lihat(driver, "Pelapor Anonim", "reporter-name")
    info = teks(driver, "reporter-info")
    assert "1202240001" not in teks(driver) and "mahasiswa@safecampus.test" not in info


def test_admin_tidak_dapat_membuka_isi_laporan(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    buka(driver, base_url, "/petugas/laporan/1")
    assert "403" in teks(driver)
