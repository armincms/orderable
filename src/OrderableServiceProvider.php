<?php

namespace Armincms\Orderable;
 
use Illuminate\Foundation\Support\Providers\AuthServiceProvider; 
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Nova as LaravelNova; 

class OrderableServiceProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/orderable.php', 'orderable');  
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');  
        LaravelNova::serving([$this, 'servingNova']);
        $this->registerWebComponents();
        $this->registerPolicies();
        $this->configureMacros();
    } 

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register any Nova services.
     *
     * @return void
     */
    public function servingNova()
    {
        LaravelNova::resources([
            Orderable::resource('customer'),     
            Orderable::resource('order'),     
            // Orderable::resource('items'),     
        ]);
    }

    /**
     * Regsiter some macro methods.
     * 
     * @return void
     */
    public function configureMacros()
    {
        // Handles the customer orders relationship.
        Builder::macro('orders', function() {
            $model = $this->getModel();
            $resource = Orderable::resource('order');
            
            if($model instanceof Authenticatable) {
                return $model->hasMany($resource::$model);
            }

            unset(static::$macros['orders']);

            return $model->orders();
         });
    }

    /**
     * Register any HttpSite services.
     * 
     * @return void
     */
    public function registerWebComponents()
    {
        app('site')->push('orders', function($arminpay) {
            $arminpay->directory('orders');
            $arminpay->pushComponent(new Components\Billing);
            $arminpay->pushComponent(new Components\Payment);
            $arminpay->pushComponent(new Components\Shipped);
        });
    }
}
