<?php
	
namespace onefasteuro\ShopifyClient;
	

use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use GuzzleHttp\Client as HttpClient;


class StorefrontClient extends BaseClient implements StorefrontClientInterface
{

	protected function assertUrl($domain)
	{
		return $domain . '/api/graphql';
	}

	public function init($shop, $token)
	{
		$domain = ShopifyUtils::formatDomain($shop, true);

		$this->setToken($token);
			
		$headers = array('Content-Type' => 'application/json',
				'X-Shopify-Storefront-Access-Token' => $this->token());

		$this->setHeaders($headers);

		$url = $this->assertUrl($domain);

		$this->setUrl($url);

		return $this;
	}

    protected function transport($send_payload)
    {
        $output = null;

        do {
            $output = $this->transport($send_payload);

            $throttled = false;
        }
        while ($throttled === true);


        return $output;
    }
}
