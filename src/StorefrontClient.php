<?php
	
namespace onefasteuro\ShopifyClient;

use onefasteuro\ShopifyUtils\ShopifyUtils;


class StorefrontClient extends AbstractClient implements StorefrontClientInterface
{

	protected function endpoint()
	{
		return 'api/graphql';
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
