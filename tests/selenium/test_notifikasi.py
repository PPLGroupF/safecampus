"""SAF-455 Notifikasi: ikon lonceng, daftar notifikasi, dan tandai sudah dibaca."""
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC

from helpers import (AKUN, ada, buat_laporan, buka, buka_laporan_petugas, el, klik, klik_dan_tunggu,
                     lihat, login, terima_laporan, tunggu)


def _tandai_semua_dibaca(driver, base_url):
    buka(driver, base_url, "/notifikasi")
    if ada(driver, "read-all"):
        klik_dan_tunggu(driver, "read-all")


def test_perubahan_status_memunculkan_notifikasi_untuk_pelapor(driver, base_url):
    login(driver, base_url, AKUN["mahasiswa"])
    _tandai_semua_dibaca(driver, base_url)
    assert not ada(driver, "notification-count")
    kode = buat_laporan(driver, base_url)

    login(driver, base_url, AKUN["petugas"])
    buka_laporan_petugas(driver, base_url, kode)
    terima_laporan(driver)

    login(driver, base_url, AKUN["mahasiswa"])
    assert el(driver, "notification-count").text.strip() == "1"
    klik(driver, "notification-bell")
    lihat(driver, "Status laporan diperbarui", "notification-dropdown")
    lihat(driver, kode, "notification-dropdown")

    # Membuka notifikasi: diarahkan ke laporan terkait dan notifikasi menjadi sudah dibaca.
    tombol = el(driver, "notification-dropdown").find_element(By.CSS_SELECTOR, '[dusk^="dropdown-notification-"]')
    lama = driver.find_element(By.TAG_NAME, "html")
    tombol.click()
    tunggu(driver, 15).until(EC.staleness_of(lama))
    lihat(driver, kode, "ticket-code")
    lihat(driver, "Dalam Penanganan", "current-status")
    assert not ada(driver, "notification-count")


def test_tandai_semua_dibaca_dan_filter_belum_dibaca(driver, base_url):
    # Data contoh (seeder) sudah membuat notifikasi perubahan status untuk akun Aisyah.
    login(driver, base_url, AKUN["aisyah"])
    buka(driver, base_url, "/notifikasi")
    lihat(driver, "Status laporan diperbarui", "notification-list")
    klik_dan_tunggu(driver, "read-all")
    lihat(driver, "Semua notifikasi ditandai sudah dibaca.")
    assert not ada(driver, "notification-count")
    klik_dan_tunggu(driver, "filter-unread")
    lihat(driver, "Belum ada notifikasi")
