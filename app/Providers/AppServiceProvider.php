<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ссылка на ассет с версией по времени изменения файла — сбрасывает кэш браузера
        Blade::directive('vasset', function ($expression) {
            return "<?php echo e(asset($expression) . '?v=' . (@filemtime(public_path($expression)) ?: '1')); ?>";
        });
    }
}
