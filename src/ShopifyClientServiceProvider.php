<?php

namespace onefasteuro\ShopifyClient;




use onefasteuro\ShopifyClient\Throttles\Throttle;
use onefasteuro\ShopifyUtils\ShopifyUtils;

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

	    $this->app->bind(ShopifyClientInterface::class, function($app, $config = []){

	        $domain = array_key_exists('domain', $config) ? ShopifyUtils::formatDomain($config['domain'], true) : null;
            $session = new \Requests_Session($domain);

            $session->headers['Content-Type'] = 'application/json';

            if(array_key_exists('token', $config)) {

                switch ($config['type']) {

                    case StorefrontClientInterface::class:

                        $session->headers['X-Shopify-Storefront-Access-Token'] = $config['token'];

                        break;


                        case AdminClientInterface::class:

                        $session->headers['X-Shopify-Access-Token'] = $config['token'];

                        if($app['config']->get('shopifyclient.extra_graph_headers') === true) {
                            $session->headers['X-GraphQL-Cost-Include-Fields'] = true;
                        }

                        break;
                }
            }

            return $session;
        });
	    $this->app->alias(ShopifyClientInterface::class, 'shopifyclient.client');



	    $this->app->bind(Throttles\ThrottleInterface::class, function($app){
		    return new Throttles\Throttle;
	    });

	    $this->app->bind(StorefrontClientInterface::class, function($app, $config = []){

	        $config['type'] = StorefrontClientInterface::class;
	        $client = $app->makeWith(ShopifyClientInterface::class, $config);

	        return new StorefrontClient($client);
        });

	    $this->app->bind(AdminClientInterface::class, function($app, $config = []){

	    	//api version
	    	$version = $app['config']->get('shopifyclient.version');

	    	//throttle to use
	    	$throttle_class = $app['config']->get('shopifyclient.throttle');

            $config['type'] = AdminClientInterface::class;
            $client = $app->makeWith(ShopifyClientInterface::class, $config);

            $throttle = new Throttle;

	    	return new AdminClient($version, $throttle, $client);
	    });
	    $this->app->alias(AdminClientInterface::class, 'shopify.graohql.client');
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
