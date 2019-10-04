<?php

namespace onefasteuro\ShopifyClient\Throttles;



interface ThrottleInterface
{
    public function shouldThrottle();

    public function throttle();
    
    public function assertThrottle(array $output);
}