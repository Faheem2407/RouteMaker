<?php

namespace Faheem2407\RouteMaker\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRouteFile extends Command
{
    protected $signature = 'make:route-file {name} {--web} {--api}';
    protected $description = 'Create a new route file in the specified directory with optional nesting';

    public function handle()
    {
        $name = $this->argument('name');
        $directory = $this->getDirectory($name);

        $pathParts = explode('/', $directory);
        $fileName = array_pop($pathParts);
        $directoryPath = implode('/', $pathParts);

        $routeFile = "routes/{$directoryPath}/{$fileName}.php";
        $filePath = base_path($routeFile);

        if (!File::exists(base_path("routes/{$directoryPath}"))) {
            File::makeDirectory(base_path("routes/{$directoryPath}"), 0755, true);
        }

        if (File::exists($filePath)) {
            $this->error("\n\tThe route file {$fileName}.php already exists in {$directoryPath}.\n");
            return;
        }

        File::put($filePath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Routes for {$fileName}.php\n");

        $this->info("\n\tRoute file [{$filePath}] created successfully.\n");

        $this->appendLoadRouteToServiceProvider($fileName, $directoryPath);
    }

    protected function getDirectory($name)
    {
        if ($this->option('web')) {
            return "web/{$name}";
        }

        if ($this->option('api')) {
            return "api/{$name}";
        }

        return "web/{$name}";
    }

    protected function appendLoadRouteToServiceProvider($fileName, $directoryPath)
    {
        $serviceProviderPath = app_path('Providers/AppServiceProvider.php');

        if (!File::exists($serviceProviderPath)) {
            $this->error("\n\tAppServiceProvider.php not found at {$serviceProviderPath}. Please make sure it's in the app/Providers directory.\n");
            return;
        }

        $loadLine = "\n        \$this->loadRoutesFrom(base_path('routes/{$directoryPath}/{$fileName}.php'));";

        $content = file_get_contents($serviceProviderPath);

        if (strpos($content, $loadLine) !== false) {
            $this->warn("\n\tAppServiceProvider already contains the load line for routes/{$directoryPath}/{$fileName}.php.\n");
            return;
        }

        $content = preg_replace(
            '/(public function boot\(\).*?\{)/s',
            '$1' . $loadLine,
            $content
        );

        file_put_contents($serviceProviderPath, $content);
    }
}
