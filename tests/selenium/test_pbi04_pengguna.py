"""Regresi PBI-04 Kelola akun pengguna (admin): tambah mahasiswa, NIM wajib dan angka, ubah, nonaktifkan akun."""
import random

from selenium.webdriver.common.by import By

from helpers import (AKUN, buka, el, isi, klik, klik_dan_tunggu, lihat, login, pilih, teks, terima_konfirmasi, unik)


def _form_mahasiswa(driver, base_url, nama, email, nim):
    buka(driver, base_url, "/admin/pengguna/tambah")
    isi(driver, "nama", nama)
    isi(driver, "email", email)
    pilih(driver, "role", value="mahasiswa")
    isi(driver, "nim", nim)
    pilih(driver, "unit_id", index=1)
    isi(driver, "password", "rahasia123")


def _data_baru():
    nama = unik("Mahasiswa Uji")
    return nama, nama.lower().replace(" ", ".") + "@safecampus.test", "12022" + str(random.randint(10000, 99999))


def _id_pengguna(driver, base_url, email):
    buka(driver, base_url, "/admin/pengguna?q=" + email)
    return el(driver, "user-table").find_element(By.CSS_SELECTOR, '[dusk^="user-row-"]').get_attribute("dusk").split("-")[-1]


def test_admin_menambah_dan_mengubah_akun_mahasiswa(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    nama, email, nim = _data_baru()
    _form_mahasiswa(driver, base_url, nama, email, nim)
    klik_dan_tunggu(driver, "save-user")
    lihat(driver, "Akun berhasil ditambahkan.")

    pengguna = _id_pengguna(driver, base_url, email)
    lihat(driver, nim, "user-table")
    klik_dan_tunggu(driver, f"edit-user-{pengguna}")
    isi(driver, "nama", nama + " Edit")
    klik_dan_tunggu(driver, "save-user")
    lihat(driver, "Akun berhasil diperbarui.")


def test_nim_wajib_dan_hanya_angka_untuk_mahasiswa(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    nama, email, _ = _data_baru()
    _form_mahasiswa(driver, base_url, nama, email, "12A45")
    klik_dan_tunggu(driver, "save-user")
    lihat(driver, "NIM hanya boleh berisi angka.", "error-nim")

    _form_mahasiswa(driver, base_url, nama, email, "")
    driver.execute_script("document.getElementById('nim').removeAttribute('required');")
    klik_dan_tunggu(driver, "save-user")
    assert "NIM" in teks(driver, "error-nim")


def test_akun_nonaktif_tidak_dapat_login(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    nama, email, nim = _data_baru()
    _form_mahasiswa(driver, base_url, nama, email, nim)
    klik_dan_tunggu(driver, "save-user")

    pengguna = _id_pengguna(driver, base_url, email)
    klik(driver, f"toggle-user-{pengguna}")
    terima_konfirmasi(driver)
    lihat(driver, "Akun dinonaktifkan.")

    login(driver, base_url, email, "rahasia123")
    lihat(driver, "Akun Anda sedang dinonaktifkan.")
    assert "/login" in driver.current_url
