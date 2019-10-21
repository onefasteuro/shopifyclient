<?php
	
namespace onefasteuro\ShopifyClient;


use Illuminate\Support\Arr;

class GraphResponse
{
    protected $raw;
    protected $body;

	public function __construct(\Requests_Response $response)
    {
        $this->raw = $response;
        $this->body = static::parse($response->body);
    }

    public function getHeader($k)
    {
        return $this->raw->headers[$k];
    }

    public function getHeaders()
    {
        return $this->raw->headers;
    }

    public function getStatusCode()
    {
        return $this->raw->status_code;
    }

    public function getBody($key = null)
    {
        if($key === null) {
            return $this->body;
        }
        else {
            return Arr::get($this->body, $key);
        }
    }

    public static function parse($response_body)
    {
        return json_decode($response_body, true);
    }

    public function isOk()
    {
        return ($this->getStatusCode() === 200 and !$this->hasErrors()) ? true : false;
    }

    public function hasErrors()
    {
        return array_key_exists('errors', $this->body) ? true : false;
    }

    public function errors()
    {
    	return $this->hasErrors() ? $this->getBody('errors') : [];
    }
    
    public function data($key = null)
    {
    	if($key === null) {
	    	return $this->getBody('data');
	    }
	    else {
	    	return $this->getBody('data.' . $key);
	    }
    }
    
    public function __toString()
    {
	    return $this->raw->body;
    }
	
	/**
     * Magic method to access the underlying request response object methods
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params = [])
    {
        return call_user_func_array([$this->raw, $method], $params);
    }
}
