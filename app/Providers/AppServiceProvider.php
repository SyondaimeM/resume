<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PortfolioContent;
use App\Services\ErrorEmailReporter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;
use RuntimeException;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(PortfolioContent::class);
        $this->app->singleton(ErrorEmailReporter::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(MessageLogged::class, function (MessageLogged $event) {
            if (in_array($event->level, ['error', 'critical', 'alert', 'emergency'], true)) {
                $exception = $event->context['exception'] ?? null;
                app(ErrorEmailReporter::class)->report(
                    $exception instanceof Throwable ? $exception : new RuntimeException('An application error was logged; inspect local logs.')
                );
            }
        });
        View::composer(['fields.*', 'partials.portfolio-site-data', 'partials.portfolio-heading'], function ($view) {
            $view->with('portfolio', app(PortfolioContent::class)->all());
        });
    }
}
