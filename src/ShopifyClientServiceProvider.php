<?php

namespace onefasteuro\ShopifyClient;

use GuzzleHttp\Client as HttpClient;


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

	    $this->app->bind(ShopifyClientInterface::class, function($app, $params = []){
	        if(count($params) > 0) {
	            $client = new HttpClient($params);
            }
            else {
                $client = new HttpClient;
            }
	        return $client;
        });



	    $this->app->bind(Throttles\ThrottleInterface::class, function($app){
		    return new Throttles\Throttle;
	    });

	    $this->app->singleton(StorefrontClientInterface::class, function($app, $params = []){

	        $client = new StorefrontClient($app[ShopifyClientInterface::class]);

	        if(count($params) > 0 and array_key_exists('domain', $params) and array_key_exists('token', $params)) {
	            $client->init($params['domain'], $params['token']);
            }

            return $client;
        });

	    $this->app->singleton(GraphClientInterface::class, function($app, $params = []){
		
	    	//api version
	    	$version = $app['config']->get('shopifyclient.version');

	    	//throttle to use
	    	$throttle = $app['config']->get('shopifyclient.throttle');
		
	    	//instatiate our client
            $provider = $app[ShopifyClientInterface::class];
		    $client = new GraphClient($provider, $version, $app[$throttle]);
	    	
		    //if we have params let's init the client
	    	if(count($params) > 0 and array_key_exists('domain', $params) and array_key_exists('token', $params)) {
	    		$client->init($params['domain'], $params['token']);
		    }

	    	return $client;
	    });
	    $this->app->alias(GraphClientInterface::class, 'shopify.graohql.client');
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
