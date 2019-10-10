<?php

namespace onefasteuro\ShopifyClient;

class GraphResponse
{
	protected $raw;
	
	public function __construct($response)
	{
		$this->raw = $response;
	}
	
	public function raw()
	{
		return $this->raw;
	}
	
	public function __get($k)
	{
		return $this->raw->$k;
	}
	
	public function __call($method, $params)
	{
		return call_user_func_array([$this->raw, $method], $params);
	}
	
	public function parsed()
	{
		return json_decode($this->raw->body, true);
	}
	
}
