<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        // Server lokal (php artisan serve) di Windows cukup lambat, jadi waktu tunggu diperpanjang.
        Browser::$waitSeconds = 15;

        // Jika ChromeDriver sudah berjalan (misalnya dijalankan manual di terminal lain),
        // pakai yang sudah ada. Jika belum, Dusk menjalankannya sendiri di port 9515.
        if (! static::runningInSail() && ! static::chromeDriverIsRunning()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Hapus cookie sebelum setiap tes agar sesi login dari tes sebelumnya tidak terbawa.
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    protected static function chromeDriverIsRunning(): bool
    {
        $connection = @fsockopen('127.0.0.1', 9515, $errorCode, $errorMessage, 1);

        if ($connection) {
            fclose($connection);

            return true;
        }

        return false;
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            // Matikan pengelola kata sandi Chrome. Tanpa ini, setelah form berisi kata sandi dikirim,
            // muncul popup "Simpan sandi?" yang mencuri fokus keyboard dan klik pada tes berikutnya.
            '--disable-features=PasswordLeakDetection,PasswordManagerOnboarding,AutofillServerCommunication',
            '--password-store=basic',
            '--no-first-run',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        $options->setExperimentalOption('prefs', [
            'credentials_enable_service' => false,
            'profile.password_manager_enabled' => false,
            'profile.password_manager_leak_detection' => false,
            'autofill.profile_enabled' => false,
        ]);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://127.0.0.1:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
               isset($_ENV['DUSK_START_MAXIMIZED']);
    }
}
