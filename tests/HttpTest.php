<?php
	
namespace onefasteuro\ShopifyClient\Tests;

	

use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotJsonException;
use onefasteuro\ShopifyClient\GraphClient;
use onefasteuro\ShopifyClient\GraphResponse;

class HttpTest extends TestCase
{

	public function testFlow()
	{
		$mock = $this->mock(\onefasteuro\ShopifyClient\GraphClient::class);
		
		$mock->shouldReceive(['init', 'query', 'transport', 'parse'])->andReturn(GraphResponse::class);
	}
	
	public function test404()
	{
		$this->expectException(NotFoundException::class);
		
		$client = app(GraphClient::class);
		$client->init('http://example.com', 'testtoken');
		
		$response = $client->query([]);
	}
	

	public function testResponse()
	{
		$data = file_get_contents(__DIR__.'/stubs/success-raw.text');
		$body = GraphResponse::parseBody($data);
		$code = GraphResponse::parseStatusCode($data);
		$headers = GraphResponse::parseHeaders($data);
		
		$response = new GraphResponse($headers, $code, $body);
		
		$this->assertEquals(200, $response->statusCode());
		$this->assertEquals('gid://shopify/Shop/5521145907', $response->data('shop.id'));
	}
	
	public function testErrorsResponse()
	{
		$data = file_get_contents(__DIR__.'/stubs/errors-raw.text');
		$body = GraphResponse::parseBody($data);
		$code = GraphResponse::parseStatusCode($data);
		$headers = GraphResponse::parseHeaders($data);
		
		$response = new GraphResponse($headers, $code, $body);
		
		$this->assertEquals(true, $response->hasErrors());
	}
}

