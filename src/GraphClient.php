<?php
	
namespace onefasteuro\ShopifyClient;



class GraphClient extends AbstractClient
{
	protected function transport($payload)
	{
		$output = $this->session->post($this->endpoint(), [], $payload);
		$response = new GraphResponse($output);
		
		return $response;
	}
}
