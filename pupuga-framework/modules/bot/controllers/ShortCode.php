<?php

namespace Pupuga\module\bot;

class ShortCode {
	
	public $values;
	public $language;

	private static $instance;
	
	function __construct() 
	{
		$this->values = Init::app()->valueData;
		$this->language = Init::app()->language;
	}
	
	public static function app() {
		if (self::$instance == null) {
			$class = get_called_class();
			self::$instance = new $class();
		}

		return self::$instance;
	}

    public function __call($method, $arguments)
    {
        $object = UseContent::app();
        if (method_exists($object, $method)) {
            $html = $object->$method($arguments);
        } else {
            $html = '';
        }
        
        return $html;
    }
    
    public function getMethods()
    {
        $methods = array();
        $classes = get_class_methods(UseContent::app());
        foreach ($classes as $class) {
            if (strpos($class, 'get') === 0) {
                $key = lcfirst(substr($class, 3));
                $methods['[' . $key .']'] = $class;
            }
        }
        
        return $methods;
    }
}