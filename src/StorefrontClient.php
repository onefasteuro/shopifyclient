<?php
	
namespace onefasteuro\ShopifyClient;


class StorefrontClient extends AbstractClient implements StorefrontClientInterface
{
	
	public function __construct(\Requests_Session $client)
	{
		parent::__construct($client);
		
	}
	
	protected function init()
	{
		$this->setEndpoint('api/graphql');
	}
	
    protected function transport($send_payload)
    {

        $output = null;

        do {
            $output = $this->session->post($this->endpoint(), [], $send_payload);
            $response = new GraphResponse($output);

            $throttled = false;
        }
        while ($throttled === true);

        return $response;
    }
}
