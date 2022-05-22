<?php
namespace Armincms\Orderable;

use Illuminate\Contracts\Support\DeferrableProvider;  
use Illuminate\Foundation\Support\Providers\AuthServiceProvider; 
use Laravel\Nova\Nova; 

class ServiceProvider extends AuthServiceProvider implements DeferrableProvider
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
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/orderable.php', 'orderable');  
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');   
        $this->registerPolicies();
        $this->configureMacros();
        Nova::resources(config('orderable.resources'));
    } 

    /**
     * Regsiter some macro methods.
     * 
     * @return void
     */
    public function configureMacros()
    {
        // Handles the customer orders relationship.
        // Builder::macro('orders', function() {
        //     $model = $this->getModel();
        //     $resource = Orderable::resource('order');
            
        //     if($model instanceof Authenticatable) {
        //         return $model->hasMany($resource::$model);
        //     }

        //     unset(static::$macros['orders']);

        //     return $model->orders();
        //  });
    } 

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [
            \Illuminate\Console\Events\ArtisanStarting::class,
            \Laravel\Nova\Events\ServingNova::class,
        ];
    }
}
