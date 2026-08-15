<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The installation guard must not redirect the tests to the installer;
        // the installer itself is covered by tests/Feature/InstallerTest.php.
        config()->set('core.installer.enabled', false);

        // The test suite must not depend on a compiled Vite manifest.
        $this->withoutVite();
    }
}
