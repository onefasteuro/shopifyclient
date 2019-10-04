<?php

namespace onefasteuro\Shopify\Tests;

use onefasteuro\Shopify\ShopifyServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
	
class TestCase extends BaseTestCase
{
		
	protected function getPackageProviders($app)
	{
		return [ ShopifyServiceProvider::class ];
	}


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'host' => 'alpha.caqz5o3oetjd.us-east-1.rds.amazonaws.com',
            'port' => '3306',
            'database' => 'craft',
            'username' => 'admin',
            'password' => 'Gh6ttY7j$5ft',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'dftapp_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);


        $app['config']->set('shopify', include __DIR__ . '/../conf.php');

    }
}
