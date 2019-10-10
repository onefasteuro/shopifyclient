<?php

namespace onefasteuro\ShopifyClient;

interface GraphResponseInterface
{
	public function isOk();
	
	public function isNotFound();
	
	public function raw();
	
	public function hasErrors();
	
	public function parsed($key = null);
	
	public function data();
	
}
