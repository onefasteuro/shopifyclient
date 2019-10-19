<?php
	
namespace onefasteuro\ShopifyClient;


interface StorefrontClientInterface
{

	public function init($shop, $token);
	
	public function query($gql, $variables = []);
}
