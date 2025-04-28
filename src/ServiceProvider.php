<?php

namespace Pkarastelev\Hexmod;

use Pkarastelev\Hexmod\Commands\MakeModule;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->commands([
            MakeModule::class,
        ]);

        $this->autoloadModulesNamespace();
    }

    private function autoloadModulesNamespace(): void
    {
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
        $composerJson['autoload']['psr-4']['Modules\\'] = 'modules/';
        file_put_contents(
            base_path('composer.json'),
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
