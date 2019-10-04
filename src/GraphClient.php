<?php

namespace onefasteuro\ShopifyClient;

class GraphClient
{
	
	protected $client;
	protected $version;
	protected $throttle;
	
	protected $shop;
	protected $token;


	public function __construct($version, Throttles\ThrottleInterface $throttle)
	{
		$this->throttle = $throttle;
		$this->version= $version;
	}
	
	protected function assertUrl()
	{
		return 'https://' . $this->shop . '/admin/api/' . $this->version . '/';
	}
	
	public function init($shop, $token)
	{
		$this->shop = $shop;
		$this->token = $token;
		
		$headers = ['Content-Type' => 'application/json', 'X-Shopify-Access-Token' => $this->token, 'X-GraphQL-Cost-Include-Fields' => true];
		
		$url = $this->assertUrl();
		
		$this->client = new \Requests_Session($url, $headers);
		
		return $this;
	}
	
	/**
	 * Get the version
	 * @return mixed
	 */
	public function version()
	{
		return $this->version;
	}
	
	/**
	 * Get the app token
	 * @return mixed
	 */
	public function token()
	{
		return $this->token;
	}
	
	
	public function setToken($k)
	{
		$this->token = $k;
		return $this;
	}
	
	public function setVersion($v)
	{
		$this->version = $v;
		return $this;
	}
	
	/**
	 * Is the client ready to query
	 * @return bool
	 */
	public function ready()
	{
		return $this->client instanceof \Requests_Session ? true : false;
	}
	
	/**
	 * @return null|Throttles\ThrottleInterface
	 */
	public function getThrottle()
	{
		return $this->throttle;
	}
	
	public function query($gql, $variables = [])
	{
		if(count($variables) > 0) {
			$send = ["query" => $gql, "variables" => $variables];
		}
		else {
			$send = ["query" => $gql];
		}
		
		$send = json_encode($send);
		
		$output = null;
		$throttled = true;
		
		
		do {
			$response = $this->client->post('graphql.json', [], $send);
			$output = json_decode($response->body, true);
			$throttled = $this->getThrottle()->assertThrottle($output);
			
			$this->getThrottle()->mightThrottle();;
		}
		while ($throttled === true);
		
		
		return $output;
	}
	
}
