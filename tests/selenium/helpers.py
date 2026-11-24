"""
Fungsi bantu yang dipakai bersama oleh semua tes Selenium.

Elemen halaman dicari lewat atribut dusk="..." yang sudah ada di tampilan Blade
(atribut yang sama dipakai oleh tes Laravel Dusk), sehingga tes tidak mudah rusak
ketika teks atau gaya tampilan berubah.
"""
import time

from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select, WebDriverWait

PASSWORD = "password"

# Akun dari database/seeders/UserSeeder.php (kata sandi semua akun: password)
AKUN = {
    "admin": "admin@safecampus.test",
    "petugas": "petugas@safecampus.test",
    "dewi": "dewi.petugas@safecampus.test",
    "rizky": "rizky.petugas@safecampus.test",
    "mahasiswa": "mahasiswa@safecampus.test",
    "aisyah": "aisyah@safecampus.test",
    "fahri": "fahri@safecampus.test",
}

KRONOLOGI = "Data dummy Selenium: pada hari Senin pukul 10.00 di ruang kelas saya mengalami ejekan berulang dari teman."


def dusk(name):
    """Locator untuk elemen dengan atribut dusk="name"."""
    return (By.CSS_SELECTOR, f'[dusk="{name}"]')


def tunggu(driver, timeout=10):
    return WebDriverWait(driver, timeout)


def el(driver, name, timeout=10):
    """Tunggu sampai elemen dusk="name" terlihat, lalu kembalikan elemennya."""
    return tunggu(driver, timeout).until(EC.visibility_of_element_located(dusk(name)))


def ada(driver, name):
    """True jika elemen dusk="name" ada di halaman (tanpa menunggu)."""
    return len(driver.find_elements(*dusk(name))) > 0


def isi(driver, name, teks):
    field = el(driver, name)
    field.clear()
    field.send_keys(teks)
    return field


def pilih(driver, name, value=None, index=None):
    select = Select(el(driver, name))
    if value is not None:
        select.select_by_value(str(value))
    else:
        select.select_by_index(1 if index is None else index)
    return select


def klik(driver, name):
    tombol = tunggu(driver).until(EC.element_to_be_clickable(dusk(name)))
    driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", tombol)
    tombol.click()


def klik_dan_tunggu(driver, name, timeout=15):
    """Klik tombol yang mengirim form atau pindah halaman, lalu tunggu halaman baru selesai dimuat."""
    lama = driver.find_element(By.TAG_NAME, "html")
    klik(driver, name)
    tunggu(driver, timeout).until(EC.staleness_of(lama))
    tunggu(driver, timeout).until(lambda d: d.execute_script("return document.readyState") == "complete")


def terima_konfirmasi(driver):
    """Tekan OK pada dialog konfirmasi browser (confirm) jika muncul."""
    try:
        tunggu(driver, 3).until(EC.alert_is_present()).accept()
    except Exception:
        pass


def teks(driver, name=None):
    """Teks yang terlihat di seluruh halaman, atau di elemen dusk="name"."""
    if name:
        return el(driver, name).text
    return driver.find_element(By.TAG_NAME, "body").text


def lihat(driver, kalimat, name=None, timeout=10):
    """Pastikan kalimat tampil di halaman (atau di elemen dusk="name")."""
    locator = dusk(name) if name else (By.TAG_NAME, "body")
    tunggu(driver, timeout).until(EC.text_to_be_present_in_element(locator, kalimat))


def buka(driver, base_url, path):
    driver.get(base_url + path)
    tunggu(driver).until(lambda d: d.execute_script("return document.readyState") == "complete")


def unik(prefix):
    """Teks unik supaya data dari tes yang dijalankan berulang tidak bentrok."""
    return f"{prefix} {int(time.time() * 1000) % 100000000}"


def login(driver, base_url, email, password=PASSWORD):
    """Masuk sebagai pengguna tertentu. Cookie lama dihapus dulu supaya sesi sebelumnya tidak terbawa."""
    driver.delete_all_cookies()
    buka(driver, base_url, "/login")
    isi(driver, "login-email", email)
    isi(driver, "login-password", password)
    klik_dan_tunggu(driver, "login-button")


def logout(driver):
    klik_dan_tunggu(driver, "logout-button")


def buat_laporan(driver, base_url, mode="teridentifikasi", kronologi=KRONOLOGI, peran="mahasiswa"):
    """Mahasiswa (sudah login) mengisi formulir laporan sampai terkirim. Mengembalikan kode tiket."""
    buka(driver, base_url, "/laporan/buat")
    pilih(driver, "category_id", index=1)
    isi(driver, "kronologi", kronologi)
    klik(driver, "next-step")
    klik(driver, "next-step")
    pilih(driver, "terlapor_peran", value=peran)
    isi(driver, "terlapor_keterangan", "Data dummy: teman satu kelas.")
    klik(driver, "next-step")
    klik(driver, "mode-" + mode)
    klik_dan_tunggu(driver, "submit-report")
    lihat(driver, "Laporan berhasil dikirim.")
    return el(driver, "ticket-code").text.strip()


def buka_laporan_petugas(driver, base_url, kode):
    """Petugas (sudah login) mencari laporan berdasarkan kode tiket di antrean, lalu membuka detailnya."""
    buka(driver, base_url, "/petugas/antrean?status=&q=" + kode)
    tombol = tunggu(driver).until(EC.element_to_be_clickable((By.CSS_SELECTOR, '[dusk^="open-"]')))
    lama = driver.find_element(By.TAG_NAME, "html")
    tombol.click()
    tunggu(driver, 15).until(EC.staleness_of(lama))
    lihat(driver, kode, "ticket-code")


def buka_laporan_pelapor(driver, base_url, kode):
    """Mahasiswa (sudah login) membuka detail laporannya sendiri dari menu Laporan Saya."""
    buka(driver, base_url, "/laporan?q=" + kode)
    tombol = tunggu(driver).until(EC.element_to_be_clickable((By.CSS_SELECTOR, '[dusk^="detail-"]')))
    lama = driver.find_element(By.TAG_NAME, "html")
    tombol.click()
    tunggu(driver, 15).until(EC.staleness_of(lama))
    lihat(driver, kode, "ticket-code")


def terima_laporan(driver, catatan="Laporan valid dan akan ditindaklanjuti."):
    """Petugas menerima laporan yang sedang dibuka (status menjadi Dalam Penanganan)."""
    klik(driver, "choose-terima")
    isi(driver, "verification-note", catatan)
    klik_dan_tunggu(driver, "submit-verification")
    lihat(driver, "Dalam Penanganan", "current-status")
