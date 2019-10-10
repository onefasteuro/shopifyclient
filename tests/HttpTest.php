<?php
	
namespace onefasteuro\ShopifyClient\Tests;

	
use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;

class HttpTest extends TestCase
{
		
		
	public function testErrors()
	{
		$this->expectException(ErrorsFoundException::class);
		
		$client = app(\onefasteuro\ShopifyClient\GraphClient::class);
		
		$client->init(getenv('SHOPIFY_APP_TOKEN'), getenv('SHOPIFY_APP_DOMAIN'));
		
		$call = 'query {
				shop {
					idd
					nams
				}
		}';
		
		$response = $client->query($call, []);
	}
	
	public function testNotFound()
	{
		$this->expectException(NotFoundException::class);
		
		$client = app(\onefasteuro\ShopifyClient\GraphClient::class);
		
		$client->init('testdomain.myshopify.com', '124e32e');
		
		$call = 'query {
				shop {
					id
					name
				}
		}';
		
		$response = $client->query($call, []);
	}
	
	public function testResponse()
	{
		
		$client = app(\onefasteuro\ShopifyClient\GraphClient::class);
		
		$client->init(getenv('SHOPIFY_APP_TOKEN'), getenv('SHOPIFY_APP_DOMAIN'));
		
		$call = 'query {
				shop {
					id
					name
				}
		}';
		
		$response = $client->query($call, []);
		
		$content = $response->parsed('data');
		
		$this->assertIsArray($content);
	}
	
}

