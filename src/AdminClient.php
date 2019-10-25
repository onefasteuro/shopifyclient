<?php
	
namespace onefasteuro\ShopifyClient;


class AdminClient extends AbstractClient implements AdminClientInterface
{
	protected $version;
	protected $throttle;
		
	public function __construct($version, Throttles\ThrottleInterface $throttle, \Requests_Session $client)
	{
	    parent::__construct($client);
		$this->throttle = $throttle;
		$this->version = $version;
		$this->setEndpoint('admin/api/' . $this->version . '/graphql.json');
	}
	

	public function version()
	{
		return $this->version;
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

		
	protected function transport($payload)
	{
        do {
            $output = $this->session->post($this->endpoint(), [], $payload);
            $response = new GraphResponse($output);

            $throttled = $this->getThrottle()->assertThrottle($response);
            $this->getThrottle()->mightThrottle();
        }
        while ($throttled === true);


        return $response;
	}

}
