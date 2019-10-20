<?php
	
namespace onefasteuro\ShopifyClient\Tests;

use onefasteuro\ShopifyClient\AdminClientInterface;
use onefasteuro\ShopifyClient\StorefrontClientInterface;


class HttpTest extends TestCase
{

	public function testFlowStorefront()
	{
		$mock = $this->mock(StorefrontClientInterface::class);
		
		$mock->shouldReceive(['init', 'clientFactory', 'transport', 'query'])->andReturn(\Requests_Response::class);
	}

    public function testFlowGraph()
    {
        $mock = $this->mock(AdminClientInterface::class);

        $mock->shouldReceive(['init', 'clientFactory', 'transport', 'query'])->andReturn(\Requests_Response::class);
    }

    public function testStorefrontResponse()
    {

        $client = resolve(StorefrontClientInterface::class, ['domain' => 'DOMAIN', 'token' => 'TOKEN']);



        $call = '{
            shop {
                name
            }
        }';

        $response = $client->query($call);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAdminResponse()
    {
        $client = resolve(AdminClientInterface::class, ['token' => 'TOKEN', 'domain' => 'DOMAIN']);


        $call = '{
            shop {
                name
            }
        }';

        $response = $client->query($call);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(!$response->hasErrors());
    }
}

