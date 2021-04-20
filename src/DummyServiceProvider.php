<?php

namespace Tajul\Saajan;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Tajul\Saajan\Entities\Calculator;
use Tajul\Saajan\Console\InstallDummyPkg;
use Tajul\Saajan\Http\Middleware\CapitalizeTitle;
use Tajul\Saajan\Providers\EventServiceProvider;

class DummyServiceProvider  extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('calculator', function ($app) {
            return new Calculator();
        });

        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'dummyPkg');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadRoutesFrom(__DIR__ .'/../routes/web.php');
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dummyPkg');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallDummyPkg::class,
            ]);
        }

        if (!class_exists('CreatePostsTable')) {
            $this->publishes([
                __DIR__ . '/database/migrations/create_posts_table.php.stub' =>
                database_path('migrations/' . date('Y_m_d_His', time()) . '_create_posts_table.php'),
            ], 'migrations');
        }

        //route specific middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('capitalize', CapitalizeTitle::class);

        //Middleware Groups
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('web', CapitalizeTitle::class);
    }


    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        $this->app->register(EventServiceProvider::class);
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('dummyPkg.prefix'),
            'middleware' => config('dummyPkg.middleware'),
        ];
    }
}
