<?php
	
namespace onefasteuro\ShopifyClient;
	
use onefasteuro\ShopifyClient\Exceptions\ErrorsFoundException;
use onefasteuro\ShopifyClient\Exceptions\NotAuthorizedException;
use onefasteuro\ShopifyClient\Exceptions\NotFoundException;
use Illuminate\Support\Arr;
use onefasteuro\ShopifyClient\Exceptions\NotJsonException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class GraphResponse implements GraphResponseInterface, Arrayable, Jsonable
{
	protected $status_code = 200;
	protected $body;
	protected $headers;
	
	protected $is_json = true;
		
	public function __construct(array $headers, $status_code, $body)
	{
		$this->setStatusCode($status_code);
		$this->setBody($body);
		$this->setHeaders($headers);
	}
		
		
	public function statusCode()
	{
		return $this->status_code;
	}
		
	public function body($key = null)
	{
		return ($key === null) ? $this->body : Arr::get($this->body, $key);
	}
		
	public function headers($key = null)
	{
		return ($key === null) ? $this->headers : (array_key_exists($key, $this->headers) ? $this->headers[$key][0] : null);
	}
	
	
	/**
	 * Set the headers from the response
	 * @param $h
	 */
	protected function setHeaders($h)
	{
		$this->headers = $h;
		return $this;
	}
	
	protected function setStatusCode($s)
    {
         $this->status_code = $s;
         return $this;
    }
	
	protected function setBody($b)
    {
    	$result = json_decode($b, true);
    	
    	if(json_last_error() === JSON_ERROR_NONE) {
		    $this->body = $result;
		    $this->is_json = true;
	    }
	    else {
	    	$this->is_json = false;
	    	$this->body = $b;
	    }
	    
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
		
		if(!$this->isJson()) {
			throw new NotJsonException('This request does not have a valid response', $this->status_code);
		}
		
		return $this;
	}
		
	public function errors()
	{
		$output = [];
		
		$errors = $this->body('errors');
			
		if(is_array($errors)) {
			if(count($errors) === 1) {
				$output[] = $errors[0]['message'];
			}
				
			if (count($errors) > 1) {
				foreach($errors as $msg) {
					$output[] = $msg['message'];
				}
			}
		}
			
		return $output;
	}
		
	public function hasErrors()
	{
		return (array_key_exists('errors', $this->body())) ? true : false;
	}
		
		
	public function __call($method, $params)
	{
		return call_user_func_array([$this->raw, $method], $params);
	}
		
	
	public function data($key = null)
	{
		return ($key === null) ? $this->body('data') : $this->body('data.'.$key);
	}
	
	/**
	 * Verifies that our response contains certain data
	 * @param $key
	 */
	public function assertResponseContains($key)
	{
		//TODO
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
	
	public function isJson()
	{
		return $this->is_json;
	}
	
	
	public static function parseHeaders($response)
	{
		$split = explode("\r\n\r\n", $response);
		$headers_section = trim( strstr($split[0], "\r\n") );
		unset($split);
		
		$headers = [];
		
		$headers_array = explode("\r\n", $headers_section);
		foreach($headers_array as $header)
		{
			$h = explode(': ', $header);
			$headers[$h[0]] = $h[1];
		}
		
		return $headers;
	}
	
	public static function parseStatusCode($response)
	{
		$code = strstr($response,"\r\n",true);
		preg_match('/\d{3}/', $code, $matches);
		
		
		return (count($matches) > 0) ? (int) $matches[0] : 0;
	}
	
	public static function parseBody($response)
	{
		$split = explode("\r\n\r\n", $response);
		return $split[1];
	}

	public function toArray()
    {
        return [
            'body' => $this->body(),
            'headers' => $this->headers(),
            'status_code' => $this->statusCode(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }
		
}
