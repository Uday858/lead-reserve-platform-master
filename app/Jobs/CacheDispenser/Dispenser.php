<?php 

namespace App\Jobs\CacheDispenser;

/**
* @class \App\Jobs\CacheDispenser\Dispenser
*/
class Dispenser {
	/**
	* @var $getter
	*/
	public $getter;

	/**
	* Generate information from database and push into cache.
	* @return bool
	*/
	public function generate() {
		return true;
	}
}