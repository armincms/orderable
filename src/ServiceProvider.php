<?php
namespace Armincms\Orderable;
  
use Illuminate\Foundation\Support\Providers\AuthServiceProvider; 
use Laravel\Nova\Nova; 
use Zareismail\Gutenberg\Gutenberg;

class ServiceProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = []; 

    /**
     * Register any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/orderable.php', 'orderable');  
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');   
        $this->registerPolicies(); 
        Nova::resources(config('orderable.resources'));
        Gutenberg::fragments([
            Cypress\Fragments\Billing::class,
        ]);
        Gutenberg::widgets([
            Cypress\Widgets\Billing::class,
        ]);
        Gutenberg::templates([
            \Armincms\Orderable\Gutenberg\Templates\BillingWidget::class,
        ]);

        app('router')->middleware('web')->prefix('_order')->group(function($router) {
            $router
                ->post('{order}', Http\Controllers\RegisterController::class)
                ->name('orderable.order.register');
            $router
                ->get('{order}', Http\Controllers\VerifyController::class)
                ->name('orderable.order.verify');
        });
    }   
}
