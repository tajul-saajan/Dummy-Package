<?php

namespace Tajul\Saajan\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Tajul\Saajan\Tests\TestCase;

class InstallBlogPackageTest extends TestCase {
    
    /** @test */
    function the_install_command_copies_a_the_configuration() {
        
        Event::fake();
        // make sure we're starting from a clean state
        if (File::exists(config_path('dummyPkg.php'))) {
            unlink(config_path('dummyPkg.php'));
        }

        $this->assertFalse(File::exists(config_path('dummyPkg.php')));

        Artisan::call('dummyPkg:install');

        $this->assertTrue(File::exists(config_path('dummyPkg.php')));
    }
}