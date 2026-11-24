"""Tes dasar untuk memastikan kerangka Selenium berjalan: halaman login terbuka dan login berhasil."""
from helpers import AKUN, buka, el, lihat, login, teks


def test_halaman_login_dapat_dibuka(driver, base_url):
    buka(driver, base_url, "/login")
    assert el(driver, "login-email").is_displayed()
    assert el(driver, "login-password").is_displayed()
    assert "SafeCampus" in driver.title


def test_admin_dapat_login(driver, base_url):
    login(driver, base_url, AKUN["admin"])
    lihat(driver, "Admin SafeCampus", "current-user-name")
    assert "/login" not in driver.current_url
    assert "Admin" in teks(driver, "current-user-name") or "Admin" in teks(driver)
