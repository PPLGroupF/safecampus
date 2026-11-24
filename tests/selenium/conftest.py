"""
Konfigurasi bersama untuk semua tes Selenium SafeCampus.

Tes ini menguji aplikasi lewat browser Chrome sungguhan, persis seperti pengguna:
membuka halaman, mengisi form, menekan tombol, lalu memeriksa hasilnya.

Syarat sebelum menjalankan:
  1. Server Laravel sudah berjalan:  php artisan serve
  2. Basis data berisi data contoh:  php artisan migrate:fresh --seed

Pengaturan lewat environment variable (semuanya opsional):
  SC_BASE_URL            alamat aplikasi (bawaan http://127.0.0.1:8000)
  SC_HEADLESS            1 = browser tidak ditampilkan (otomatis 1 di GitHub Actions)
  SC_CHROME_BINARY       lokasi file Chrome jika tidak terpasang di lokasi standar
  SC_CHROMEDRIVER        lokasi chromedriver jika tidak ingin memakai Selenium Manager
  SC_DISABLE_BUILD_CHECK 1 = izinkan versi chromedriver berbeda dengan Chrome
"""
import os
import pathlib

import pytest
from selenium import webdriver

HERE = pathlib.Path(__file__).parent
SCREENSHOTS = HERE / "screenshots"
DOWNLOADS = pathlib.Path(os.environ.get("SC_DOWNLOAD_DIR", HERE / "downloads")).resolve()


def _flag(name: str, default: str = "0") -> bool:
    return os.environ.get(name, default).strip().lower() in ("1", "true", "yes")


@pytest.fixture(scope="session")
def base_url() -> str:
    return os.environ.get("SC_BASE_URL", "http://127.0.0.1:8000").rstrip("/")


@pytest.fixture(scope="session")
def download_dir() -> pathlib.Path:
    DOWNLOADS.mkdir(parents=True, exist_ok=True)
    return DOWNLOADS


def _buat_chrome(download_dir):
    options = webdriver.ChromeOptions()
    if _flag("SC_HEADLESS", "1" if os.environ.get("CI") else "0"):
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1400,1000")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    # Matikan popup "Simpan sandi?" dan peringatan kebocoran sandi dari Chrome.
    options.add_argument("--disable-features=PasswordLeakDetection,PasswordManagerOnboarding")
    options.add_experimental_option("prefs", {
        "credentials_enable_service": False,
        "profile.password_manager_enabled": False,
        "profile.password_manager_leak_detection": False,
        "download.default_directory": str(download_dir),
        "download.prompt_for_download": False,
    })
    if os.environ.get("SC_CHROME_BINARY"):
        options.binary_location = os.environ["SC_CHROME_BINARY"]

    service_args = ["--disable-build-check"] if _flag("SC_DISABLE_BUILD_CHECK") else None
    service = webdriver.ChromeService(executable_path=os.environ.get("SC_CHROMEDRIVER"), service_args=service_args)

    return webdriver.Chrome(options=options, service=service)


@pytest.fixture
def driver_factory(download_dir):
    """Membuka browser tambahan, misalnya untuk menguji dua pengguna login bersamaan."""
    browsers = []

    def buat():
        browser = _buat_chrome(download_dir)
        browsers.append(browser)
        return browser

    yield buat
    for browser in browsers:
        browser.quit()


@pytest.fixture
def driver(driver_factory):
    """Browser utama untuk setiap tes. Ditutup otomatis setelah tes selesai."""
    return driver_factory()


@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    """Simpan screenshot otomatis ketika sebuah tes gagal (folder tests/selenium/screenshots)."""
    outcome = yield
    report = outcome.get_result()
    if report.when == "call" and report.failed and "driver" in item.fixturenames:
        browser = item.funcargs.get("driver")
        if browser is not None:
            SCREENSHOTS.mkdir(exist_ok=True)
            browser.save_screenshot(str(SCREENSHOTS / f"{item.name}.png"))
