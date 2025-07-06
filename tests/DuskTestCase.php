<?php

namespace Tests;

use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BeforeClass;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', ['--env' => 'dusk.local']);
    }

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        $dbPath = __DIR__.'/../database/testing.sqlite';

        if (file_exists($dbPath)) {
            unlink($dbPath);
        }

        touch($dbPath);

        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--disable-password-manager-reauthentication',
            '--disable-features=AutofillServerCommunication,PasswordManagerRedesign',
            '--password-store=basic',
            '--no-default-browser-check',
            '--disable-save-password-bubble',
            '--user-data-dir='.sys_get_temp_dir().'/chrome-profile-'.getmypid(),
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    #[AfterClass]
    public static function cleanup(): void
    {
        $path = base_path('database/testing.sqlite');

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
