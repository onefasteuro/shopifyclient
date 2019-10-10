<?php

namespace onefasteuro\ShopifyClient;

use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotAuthorizedException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use Requests_Response;

class GraphResponse implements GraphResponseInterface
{
	protected $raw;
	protected $parsed_response;
	
	public function __construct(Requests_Response $response)
	{
		$this->raw = $response;
		$this->parsed_response = $this->parse($this->raw);
	}
	
	
	public function assertSuccessResponse()
	{
		if($this->isNotFound()) {
			throw new NotFoundException('Could not locate this url.', $this->status_code);
		}
		
		if($this->isNotAuthorized()){
			throw new NotAuthorizedException('This request is not authorized', $this->status_code);
		}
		
		if($this->hasErrors()) {
			$errors = $this->assertErrors();
			throw new ErrorsFoundException('Your request contained some errors: ' . $errors, 400);
		}
		
		return $this;
	}
	
	public function raw()
	{
		return $this->raw;
	}
	
	public function assertErrors()
	{
		$output = [];

		
		if(count($this->parsed_response['errors']) === 1) {
			$output[] = $this->parsed('errors');
		}
		
		if (count($this->parsed('errors')) > 1) {
			foreach($this->parsed_response['errors'] as $msg) {
				$output[] = $msg['message'];
			}
		}
		
		
		return implode("\n", $output);
	}
	
	public function hasErrors()
	{
		return (array_key_exists('errors', $this->parsed())) ? true : false;
	}
	
	public function __get($k)
	{
		return $this->raw->$k;
	}
	
	public function __call($method, $params)
	{
		return call_user_func_array([$this->raw, $method], $params);
	}
	
	public function parsed($key = null)
	{
		return ($key === null) ? $this->parsed_response : $this->parsed_response[$key];
	}
	
	public static function parse($raw)
	{
		return json_decode($raw->body, true);
	}
	
	public function isNotFound()
	{
		return $this->raw->status_code === 404 ? true : false;
	}
	
	public function isNotAuthorized()
	{
		return $this->raw->status_code === 401 ? true : false;
	}
	
	public function isOk()
	{
		return $this->raw->status_code === 200 ? true : false;
	}
	
}
