<?php
	
namespace onefasteuro\ShopifyClient;


interface StorefrontClientInterface
{
	
	public function query($gql, $variables = []);
}
