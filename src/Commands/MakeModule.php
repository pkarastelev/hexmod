<?php

namespace Pkarastelev\Hexmod\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    /** @var string */
    protected $signature = 'make:module {name}';

    /** @var string */
    protected $description = 'Scaffold a new module';

    public function handle(): int
    {
        $moduleName = $this->argument('name');

        if (empty($moduleName)) {
            $this->error('Module name is required.');
            return 1;
        }

        $this->makeDirectories($moduleName);
        $this->generateFiles($moduleName);

        return 0;
    }

    private function makeDirectories(string $moduleName): void
    {
        $directories = [
            // Adapters
            'Adapters/Http/Controllers',
            'Adapters/Http/Requests',
            'Adapters/Http/Resources',
            'Adapters/Http/routes',

            // Application
            'Application/Ports/In',
            'Application/Ports/Out',
            'Application/Services',

            // Domain
            'Domain/Entities',

            // Infrastructure
            'Infrastructure/Persistence/Models',
            'Infrastructure/Persistence/Repositories',
            'Infrastructure/Providers',
        ];

        $modulePath = $this->getModulePath($moduleName);

        foreach ($directories as $directory) {
            File::makeDirectory($modulePath . '/' . $directory, 0755, true);
        }
    }

    private function generateFiles(string $moduleName): void
    {
        $modulePath = $this->getModulePath($moduleName);

        // routes
        File::copy(__DIR__ . '/../../stubs/routes.php.stub', $modulePath . '/Adapters/Http/routes/api.php');
        File::copy(__DIR__ . '/../../stubs/routes.php.stub', $modulePath . '/Adapters/Http/routes/web.php');

        // composer.json
        $composerJsonContents = file_get_contents(__DIR__ . '/../../stubs/composer.json.stub');
        $search = ['{{packageName}}', '{{moduleName}}'];
        $replace = [strtolower($moduleName), $moduleName];
        $composerJsonContents = str_replace($search, $replace, $composerJsonContents);
        File::put($modulePath . '/composer.json', $composerJsonContents);

        // service provider
        $serviceProviderContents = file_get_contents(__DIR__ . '/../../stubs/ServiceProvider.php.stub');
        $serviceProviderContents = str_replace('{{moduleName}}', $moduleName, $serviceProviderContents);
        File::put(
            $modulePath . '/Infrastructure/Providers/' . $moduleName . 'ServiceProvider.php',
            $serviceProviderContents
        );
    }

    private function getModulePath(string $moduleName): string
    {
        return 'modules/' . ucfirst($moduleName);
    }
}
