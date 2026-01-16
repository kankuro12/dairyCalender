<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class ValidateRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:validate
                            {--show-all : Show all found route references}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate all route() references in controllers and views match defined routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Validating route references...');
        $this->newLine();

        // Get all defined route names (filter out unnamed routes)
        $definedRoutes = collect(Route::getRoutes())
            ->map(fn($route) => $route->getName())
            ->filter(fn($name) => !empty($name))
            ->unique()
            ->sort()
            ->values();

        $this->info("✓ Found {$definedRoutes->count()} defined routes");

        // Find all route references in code
        $references = $this->findRouteReferences();
        $this->info("✓ Found " . count($references) . " route references in code");
        $this->newLine();

        // Validate each reference
        $invalid = [];
        foreach ($references as $ref) {
            if (!$definedRoutes->contains($ref['name'])) {
                $invalid[] = $ref;
            } else if ($this->option('show-all')) {
                $this->line("  ✓ {$ref['name']} ({$ref['file']}:{$ref['line']})");
            }
        }

        // Report results
        if (empty($invalid)) {
            $this->newLine();
            $this->info('✅ All route references are valid!');
            return 0;
        } else {
            $this->newLine();
            $this->error('❌ Found ' . count($invalid) . ' invalid route reference(s):');
            $this->newLine();

            foreach ($invalid as $ref) {
                $this->line("  Route: <fg=red>{$ref['name']}</>");
                $this->line("  File:  <fg=yellow>{$ref['file']}</>:<fg=cyan>{$ref['line']}</>");
                $this->line("  Code:  {$ref['context']}");
                $this->newLine();
            }

            $this->warn('💡 Tip: Run "php artisan route:list" to see all available routes');
            return 1;
        }
    }

    /**
     * Find all route() references in controllers and views
     */
    protected function findRouteReferences(): array
    {
        $references = [];
        $directories = [
            app_path('Http/Controllers'),
            resource_path('views'),
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) continue;

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory)
            );

            $files = new RegexIterator(
                $iterator,
                '/^.+\.(php|blade\.php)$/i',
                RegexIterator::GET_MATCH
            );

            foreach ($files as $file) {
                $filePath = $file[0];
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);
                $content = file_get_contents($filePath);
                $lines = explode("\n", $content);

                foreach ($lines as $lineNum => $line) {
                    // Match route('route.name') or route("route.name")
                    if (preg_match_all('/route\([\'"]([^\'"]+)[\'"]\)/', $line, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $references[] = [
                                'name' => $match[1],
                                'file' => $relativePath,
                                'line' => $lineNum + 1,
                                'context' => trim($line),
                            ];
                        }
                    }
                }
            }
        }

        return $references;
    }
}
