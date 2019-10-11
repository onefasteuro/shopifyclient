<?php

namespace onefasteuro\ShopifyClient\Tests;

use onefasteuro\ShopifyClient\ShopifyClientServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
	
class TestCase extends BaseTestCase
{
		
	protected function getPackageProviders($app)
	{
		return [ ShopifyClientServiceProvider::class ];
	}


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

    }
	
	protected function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub
		
		$dotenv = \Dotenv\Dotenv::create(__DIR__.'/../');
		$dotenv->load();
	}
	
}
