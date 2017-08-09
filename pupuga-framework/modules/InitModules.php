<?php

namespace Pupuga\modules;

class InitModules
{
	private $modules = array(
		'bot',
	);

	function __construct()
	{
	    if (!defined('\Carbon_Fields\VERSION')) {
            add_action('after_setup_theme', array($this, 'carbonFieldsLoad'));   
        }
        
		$this
			->setDefine()
			->requireAbstractInit()
			->requireModules();
	}

    public function carbonFieldsLoad() {
        require_once(get_stylesheet_directory() . '/pupuga-framework/vendor/autoload.php');

        \Carbon_Fields\Carbon_Fields::boot();
    }

	/**
	 * @return $this
	 */
	protected function setDefine() 
	{
		define('MODULES_PATH', __DIR__ . DIRECTORY_SEPARATOR);
		define('MODULES_DIR_NAME', basename(MODULES_PATH));

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function requireModules() 
	{
		foreach ($this->modules as $module) {
			$file = MODULES_PATH . $module . DIRECTORY_SEPARATOR . 'Init.php';

			require_once $file;
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function requireAbstractInit() 
	{
		$file = MODULES_PATH . 'AbstractInit.php';

		require_once $file;

		return $this;
	}
	
}


new InitModules();
