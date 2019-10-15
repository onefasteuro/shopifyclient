<?php
	
namespace onefasteuro\ShopifyClient;


interface ClientInterface
{
	public function query($gql, $variables = []);
}
