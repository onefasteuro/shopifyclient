<?php
	
namespace onefasteuro\ShopifyClient\Clients;


use onefasteuro\ShopifyClient\AbstractClient;
use onefasteuro\ShopifyClient\GraphResponse;

class GraphClient extends AbstractClient
{
	protected function transport($payload)
	{
		$output = $this->session->post($this->endpoint(), [], $payload);
		$response = new GraphResponse($output);
		
		return $response;
	}
}
