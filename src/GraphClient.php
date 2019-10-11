<?php
	
namespace onefasteuro\ShopifyClient;
	
use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
	
class GraphClient
{
		
	protected $client;
	protected $version;
	protected $throttle;
		
	protected $shop_domain;
	protected $endpoint_url;
	protected $headers;
	protected $token;
		
		
	public function __construct($version, Throttles\ThrottleInterface $throttle)
	{
		$this->throttle = $throttle;
		$this->version= $version;
	}
		
	protected function assertUrl($domain)
	{
		if(!preg_match('/https\:\/\//', $domain)) {
			$domain = sprintf('https://%s', $domain);
		}
		
		return $domain . '/admin/api/' . $this->version . '/graphql.json';
	}
		
	public function init($shop, $token)
	{
		$this->shop_domain = $shop;
		$this->token = $token;
			
		$this->headers = array('Content-Type: application/json',
				'X-Shopify-Access-Token: '.$this->token,
				'X-GraphQL-Cost-Include-Fields: '. true);
			
		$this->endpoint_url = $this->assertUrl($shop);
		
		$this->client = static::clientFactory($this->endpoint_url, $this->headers);
		
		return $this;
	}
		

	public function version()
	{
		return $this->version;
	}
		

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
			
		//client not init, stop and let everyone know
		if(!is_resource($this->client)) {
			throw new NotReadyException;
		}
			
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $send);
			
		do {
			$output = static::assertClientResponse($this->client);
				
			$output->assertSuccessResponse();
				
			$throttled = $this->getThrottle()->assertThrottle($output);
			$this->getThrottle()->mightThrottle();
		}
		while ($throttled === true);


		return $output;
	}
		
		
		
	protected static function assertClientResponse($client)
	{
		$response = curl_exec($client);
		$header_length = curl_getinfo($client, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_length);
		$body = substr($response, $header_length);
		$status_code = curl_getinfo($client, CURLINFO_HTTP_CODE);
		
		return new GraphResponse($headers, $status_code, $body);
	}
		
		
	protected static function clientFactory($url, array $headers)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
			
		return $ch;
	}
		
		
	public function __destruct()
	{
		if (is_resource($this->client)) {
			curl_close($this->client);
		}
	}
}
