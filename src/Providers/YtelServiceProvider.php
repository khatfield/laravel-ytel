<?php


namespace Khatfield\LaravelYtel\Providers;


use Illuminate\Support\ServiceProvider;
use Khatfield\LaravelYtel\Ytel;

class YtelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $config = realpath(__DIR__ . '/..') . '/config/ytel.php';
        $this->mergeConfigFrom($config, 'ytel');

        $this->app->singleton(Ytel::class, function($app)
        {
            $ytel = new Ytel($app['config']);

            return $ytel;
        });

        $this->app->alias(Ytel::class, 'ytel');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/..') . '/config/ytel.php';
        $this->publishes(
            [
                $config => config_path('ytel.php'),
            ], 'ytel-config');
    }

    public function provides()
    {
        return ['ytel', Ytel::class];
    }
}