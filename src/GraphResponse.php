<?php
	
namespace onefasteuro\ShopifyClient;
	
use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotAuthorizedException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use Illuminate\Support\Arr;
	
class GraphResponse implements GraphResponseInterface
{
	protected $status_code = 200;
	protected $body;
	protected $headers;
		
	public function __construct($headers, $status_code, $body)
	{
		$this->setStatusCode($status_code);
		$this->setBody($body);
		$this->setHeaders($headers);
	}
		
		
	public function statusCode()
	{
		return $this->status_code;
	}
		
	public function body()
	{
		return $this->body;
	}
		
	public function headers($key = null)
	{
		return ($key === null) ? $this->headers : $this->headers[$key];
	}
	
	
	/**
	 * Set the headers from the response
	 * @param $h
	 */
	protected function setHeaders($h)
	{
		$headers = null;
		$exploded = explode("\r\n\r\n", $h);
		for ($index = 0; $index < count($exploded) -1; $index++)
		{
				
			foreach (explode("\r\n", $exploded[$index]) as $i => $line)
			{
				if ($i > 0) {
					list ($key, $value) = explode(': ', $line);
					$headers[$index][$key] = $value;
				}
			}
		}
		
		$this->headers = array_pop($headers);
	}
	
	protected function setStatusCode($s)
    {
         $this->status_code = $s;
         return $this;
    }
	
	protected function setBody($b)
    {
        $this->body = json_decode($b, true);
        return $this;
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
		
		$errors = $this->parsed('errors');
			
		if(is_array($errors)) {
			if(count($errors) === 1) {
				$output[] =$errors[0]['message'];
			}
				
			if (count($errors) > 1) {
				foreach($errors as $msg) {
					$output[] = $msg['message'];
				}
			}
		}
			
		return implode("\n", $output);
	}
		
	public function hasErrors()
	{
		return (array_key_exists('errors', $this->parsed())) ? true : false;
	}
		
		
	public function __call($method, $params)
	{
		return call_user_func_array([$this->raw, $method], $params);
	}
		
	public function parsed($key = null)
	{
		return ($key === null) ? $this->body : Arr::get($this->body, $key);
	}
		
	public function data()
	{
		return $this->parsed('data');
	}
		
	public function isNotFound()
	{
		return $this->status_code === 404 ? true : false;
	}
		
	public function isNotAuthorized()
	{
		return $this->status_code === 401 ? true : false;
	}
		
	public function isOk()
	{
		return $this->status_code === 200 ? true : false;
	}
		
}
