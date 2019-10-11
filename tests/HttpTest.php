<?php
	
namespace onefasteuro\ShopifyClient\Tests;

	
use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use onefasteuro\ShopifyClient\GraphResponse;

class HttpTest extends TestCase
{
		
		
	public function testOk()
	{
		$mock = $this->mock(\onefasteuro\ShopifyClient\GraphClient::class);

		$success = file_get_contents(__DIR__.'/../tests/stubs/success.json');

		$req = new \Requests_Response;
		$req->status_code = 200;
		$req->body = $success;

		$mock->shouldReceive('query')->andReturn(new GraphResponse($req));



		/*
		$client->init(getenv('SHOPIFY_APP_TOKEN'), getenv('SHOPIFY_APP_DOMAIN'));
		
		$call = 'query {
				shop {
					idd
					nams
				}
		}';
		
		$response = $client->query($call, []);
		*/
	}

	public function testErrorsFound()
    {
        $mock = $this->mock(\onefasteuro\ShopifyClient\GraphClient::class);

        $success = file_get_contents(__DIR__.'/../tests/stubs/errors.json');

        $req = new \Requests_Response;
        $req->status_code = 200;
        $req->body = $success;

        $mock->shouldReceive('query')->andThrow(ErrorsFoundException::class);
    }


    public function testResponse()
    {
        $success = file_get_contents(__DIR__.'/../tests/stubs/success.json');

        $req = new \Requests_Response;
        $req->status_code = 200;
        $req->body = $success;

        $mock = $this->mock(GraphResponse::class);
    }


}

