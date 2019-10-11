<?php

namespace onefasteuro\ShopifyClient;

interface GraphResponseInterface
{
	public function isOk();
	
	public function isNotFound();
	
	public function hasErrors();
	
	public function body($key = null);
	
	public function data();
	
}
