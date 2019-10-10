<?php

namespace onefasteuro\ShopifyClient;

use Requests_Response;

class GraphResponse implements GraphResponseInterface
{
	protected $raw;
	
	public function __construct(Requests_Response $response)
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
	
	public function isNotFound()
	{
		return $this->raw->status_code === 404 ? true : false;
	}
	
	public function isOk()
	{
		return $this->raw->status_code === 200 ? true : false;
	}
	
}
