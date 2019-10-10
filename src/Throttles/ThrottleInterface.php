<?php

namespace onefasteuro\ShopifyClient\Throttles;



use onefasteuro\ShopifyClient\GraphResponse;

interface ThrottleInterface
{
    public function shouldThrottle();

    public function throttle();
    
    public function assertThrottle(GraphResponse $output);
}