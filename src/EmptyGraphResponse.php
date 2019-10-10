<?php

namespace onefasteuro\ShopifyClient;


class EmptyGraphResponse implements GraphResponseInterface
{
	public function raw()
	{
		return null;
	}
	
	public function parsed()
	{
		return null;
	}
	
	public function isNotFound()
	{
		return true;
	}
	
	public function isOk()
	{
		return false;
	}
	
}
