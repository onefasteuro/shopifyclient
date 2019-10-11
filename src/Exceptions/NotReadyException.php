<?php

namespace onefasteuro\ShopifyClient\Exceptions;

use Throwable;

class NotReadyException extends \Exception
{

	
	public function __construct($message = "You must init the client before sending the requrest.", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
	
}
