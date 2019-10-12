<?php

namespace onefasteuro\ShopifyClient;



class ShopifyClientServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }


    
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(__DIR__ . '/../config/shopifyclient.php', 'shopifyclient');
	
	    $this->app->bind(Throttles\Throttle::class, function($app){
		    return new Throttles\Throttle;
	    });

	    $this->app->singleton(GraphClient::class, function($app){
		
	    	$version = $app['config']->get('shopifyclient.version');

	    	$throttle = $app['config']->get('shopifyclient.throttle');

	    	return new GraphClient($version, $app[$throttle]);
	    });
    }
    

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['shopifyclient'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/shopifyclient.php' => config_path('shopifyclient.php'),
        ], 'shopifyclient.config');
	
	    $this->commands([

	    ]);
    }
}
