<?php
	
namespace onefasteuro\ShopifyClient;


interface GraphClientInterface
{
	public function query($gql, $variables = []);
}
