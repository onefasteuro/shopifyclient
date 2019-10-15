<?php
	
namespace onefasteuro\ShopifyClient;
	

use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class GraphClient implements GraphClientInterface
{
		
	protected $client;
	protected $version;
	protected $throttle;
	protected $token;
		
	public function __construct($version, Throttles\ThrottleInterface $throttle)
	{
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
		

	public function token()
	{
		return $this->token;
	}

    public function setToken($t)
    {
        $this->token = $t;
        return $this;
    }


    protected function setHeaders()
    {
        //TODO
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
			throw new NotReadyException;
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
		$headers = [];
		
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $payload);
		
		return curl_exec($this->client);
	}
	
	public static function parse($response)
	{
		$headers = ShopifyUtils::parseHeaders($response);
		$status_code = ShopifyUtils::parseStatusCode($response);
		$body = ShopifyUtils::parseBody($response);

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
	
	public function getInfo($key)
	{
		return curl_getinfo($this->client, $key);
	}
		
	public function __destruct()
	{
		if (is_resource($this->client)) {
			curl_close($this->client);
		}
	}
}
