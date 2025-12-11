<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Module;

class ExtractModulesFromRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-modules-from-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $modules = [];

        // Define routes to exclude (by URI keywords or controller names)
        $excludedUris = ['logout', 'update-slug', 'permissions', 'masterdelete'];
        $excludedControllers = ['CommonController'];

        foreach ($routes as $route) {
            $action = $route->getAction();
            $middlewares = $route->middleware();

            // Only consider routes within `auth:administration` middleware
            if (!in_array('auth', $middlewares)) {
                continue;
            }

            // Skip excluded URI paths
            $uri = $route->uri();
            foreach ($excludedUris as $exclude) {
                if (Str::contains($uri, $exclude)) {
                    continue 2;
                }
            }

            // Use controller to define module
            if (isset($action['controller'])) {
                $controllerFull = $action['controller'];
                $controller = class_basename(explode('@', $controllerFull)[0]);

                // Exclude specific controllers
                if (in_array($controller, $excludedControllers)) {
                    continue;
                }

                // Extract module name by stripping 'Controller'
                $moduleName = Str::replaceLast('Controller', '', $controller);

                // Add to unique modules
                $modules[$moduleName] = [
                    'name' => $moduleName,
                    'slug' => Str::slug($moduleName),
                ];
            }
        }

        // Save to DB
        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['slug' => $module['slug']],
                ['name' => $module['name']]
            );
        }

        $this->info('Admin modules with auth:administration middleware extracted successfully.');
    }
}
