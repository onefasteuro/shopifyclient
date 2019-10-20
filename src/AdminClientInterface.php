<?php
	
namespace onefasteuro\ShopifyClient;


interface AdminClientInterface
{
	
	public function query($gql, $variables = []);
}
