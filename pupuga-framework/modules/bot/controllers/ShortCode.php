<?php

namespace Pupuga\module\bot;

class ShortCode {
	
	protected $values;

	private static $instance;
	
	function __construct() 
	{
		$this->values = Init::app()->valueData;
	}
	
	public static function app() {
		if (self::$instance == null) {
			$class = get_called_class();
			self::$instance = new $class();
		}

		return self::$instance;
	}
	
	public function getFormula() {
		return test($this->values);
	}
	
}