<?php
	
namespace onefasteuro\ShopifyClient\Tests;

use onefasteuro\ShopifyClient\GraphGraphClient;
use onefasteuro\ShopifyClient\GraphResponse;

class HttpTest extends TestCase
{

	public function testFlow()
	{
		$mock = $this->mock(\onefasteuro\ShopifyClient\GraphGraphClient::class);
		
		$mock->shouldReceive(['init', 'query', 'transport', 'parse'])->andReturn(GraphResponse::class);
	}
	
	public function test404()
	{
		$this->expectException(\onefasteuro\ShopifyClient\Exceptions\NotFoundException::class);
		
		$client = app(GraphGraphClient::class);
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
		
		$this->assertIsObject($response->assertSuccessResponse());
		$this->assertEquals(200, $response->statusCode());
		$this->assertEquals(false, $response->hasErrors());
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
		$this->assertIsArray($response->body());
		
		$errors = [
			"Field 'ids' doesn't exist on type 'Shop'"
		];
		
		$this->assertEquals($errors, $response->errors());
	}
	
	public function testNotJson()
	{
		$data = file_get_contents(__DIR__.'/stubs/notjson-raw.text');
		$body = GraphResponse::parseBody($data);
		$code = GraphResponse::parseStatusCode($data);
		$headers = GraphResponse::parseHeaders($data);
		
		$response = new GraphResponse($headers, $code, $body);
		
		$this->expectException(\onefasteuro\ShopifyClient\Exceptions\NotJsonException::class);
		$response->assertSuccessResponse();
	}
}

