<?php
	
namespace onefasteuro\ShopifyClient;


interface GraphClientInterface
{
	public static function parse($response);
	
	public function init($shop, $token);
	
	public function query($gql, $variables = []);
}
