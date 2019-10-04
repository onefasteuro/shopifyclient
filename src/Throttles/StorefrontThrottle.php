<?php

namespace onefasteuro\ShopifyClient\Throttles;



class StorefrontThrottle extends Throttle implements ThrottleInterface
{
	const THROTTLE_ERROR = 'Throttled';
	
	protected $should_throttle = false;

	public function assertThrottle(array $output)
	{
		$is = false;
		if(array_key_exists('errors', $output)) {
			foreach($output['errors'] as $error) {
				if($error['message'] === static::THROTTLE_ERROR) {
					$is = true;
				}
			}
		}
		
		$this->should_throttle = $is;
		
		return $is;
	}

    protected function refresh(array $data)
    {
        $this->cost = $data['cost']['requestedQueryCost'];
        $this->total = $data['cost']['throttleStatus']['maximumAvailable'];
        
        $this->available = $data['cost']['throttleStatus']['currentlyAvailable'];
        $this->restore_rate = $data['cost']['throttleStatus']['restoreRate'];
        $this->restore_time = ceil($this->cost / $this->restore_rate);
    }
    
    public function shouldThrottle()
	{
		return $this->should_throttle;
	}
	
	//the throttle method
	public function mightThrottle()
	{
		if($this->shouldThrottle()) {
			$this->throttle();
		}
	}
	
	public function throttle()
	{
		sleep(2);
	}
}