<?php

namespace Pupuga\modules;

class InitModules
{
	private $modules = array(
		'bot',
	);

	function __construct()
	{
		$this
			->setDefine()
			->requireAbstractInit()
			->requireModules();
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
