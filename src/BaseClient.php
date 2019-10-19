<?php
	
namespace onefasteuro\ShopifyClient;
	

use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use GuzzleHttp\Client as HttpClient;

abstract class BaseClient
{
    protected $token;
    protected $client = null;
    protected $headers;
    protected $url;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setHeaders(array $h)
    {
        $this->headers = $h;
        return $this;
    }

    public function setUrl($url)
    {
        //validate url
        $this->url = $url;
        return $this;
    }

    public function url()
    {
        return $this->url;
    }

    public function headers()
    {
        return $this->headers;
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

    protected function preparePayload($gql, $variables = [])
    {
        $send = (count($variables) > 0) ? ["query" => $gql, "variables" => $variables] : ["query" => $gql];
        $send_payload = json_encode($send);

        return $send_payload;
    }

    abstract protected function transport($send_payload);
		
	public function query($gql, $variables = [])
	{
		$send_payload = $this->preparePayload($gql, $variables);

		return $this->transport($send_payload);
	}
}
