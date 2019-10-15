<?php
	
namespace onefasteuro\ShopifyClient;
	

use onefasteuro\ShopifyClient\Exceptions\NotReadyException;
use onefasteuro\ShopifyUtils\ShopifyUtils;

interface GraphClientInterface
{
	public function query($gql, $variables = []);
}
