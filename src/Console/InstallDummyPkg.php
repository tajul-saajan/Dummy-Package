<?php

namespace Tajul\Saajan\Console;

use Illuminate\Console\Command;

class InstallDummyPkg extends Command
{
    protected $signature = 'dummyPkg:install';

    protected $description = 'Install the DummyPkg';

    public function handle() {
        $this->info('Installing BlogPackage...');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Tajul\Saajan\DummyServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('Installed DummyPckg');
    }
}