<?php
	
namespace onefasteuro\ShopifyClient;
	

use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class GraphClient extends BaseClient implements GraphClientInterface
{
	protected $version;
	protected $throttle;
	protected $token;
		
	public function __construct(ShopifyClientInterface $client, $version, Throttles\ThrottleInterface $throttle)
	{
	    parent::__construct($client);
		$this->throttle = $throttle;
		$this->version = $version;
	}
		
	protected function assertUrl($domain)
	{
		return $domain . '/admin/api/' . $this->version . '/graphql.json';
	}

	public function init($shop, $token)
	{
		$domain = ShopifyUtils::formatDomain($shop, true);

		$this->setToken($token);
			
		$headers = array('Content-Type: application/json',
				'X-Shopify-Access-Token: '.$this->token(),
				'X-GraphQL-Cost-Include-Fields: '. true);
		$url = $this->assertUrl($domain);
		
		$this->client = static::clientFactory($url, $headers);
		
		return $this;
	}
		

	public function version()
	{
		return $this->version;
	}

		
	public function setVersion($v)
	{
		$this->version = $v;
		return $this;
	}
	

	public function getThrottle()
	{
		return $this->throttle;
	}
		
	public function query($gql, $variables = [])
	{
		$send = (count($variables) > 0) ? ["query" => $gql, "variables" => $variables] : ["query" => $gql];
		$send_payload = json_encode($send);
			
		$output = null;
			
		//client not init, stop and let everyone know
		if(!is_resource($this->client)) {
			throw new NotReadyException('The client is not ready to query the data.');
		}
		
		do {
			$response = $this->transport($send_payload);

			$output = static::parse($response);
			

			
			$output->assertSuccessResponse();
				
			$throttled = $this->getThrottle()->assertThrottle($output);
			$this->getThrottle()->mightThrottle();
		}
		while ($throttled === true);


		return $output;
	}
	
		
	protected function transport($payload)
	{

	}

	/*
	public function __destruct()
	{
		if (is_resource($this->client)) {
			curl_close($this->client);
		}
	}
	*/
}
