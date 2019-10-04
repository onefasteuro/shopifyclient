<?php

namespace onefasteuro\ShopifyClient\Throttles;



class Throttle implements ThrottleInterface
{
	const THROTTLE_ERROR = 'Throttled';
	
	protected $cost = 0;
	protected $total = 1000;
	protected $available = true;
	protected $restore_rate = 50;
	protected $restore_time = 0;
	

	public function assertThrottle(array $output)
	{
		$is = false;
		if(array_key_exists('errors', $output)) {
			foreach($output['errors'] as $error) {
				if($error['message'] === static::THROTTLE_ERROR) {
					$is = true;
					$this->refresh($output['extensions']);
				}
			}
		}
		
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
		return ($this->restore_time > 0) ? true : false;
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
		sleep($this->restore_time);
	}
}