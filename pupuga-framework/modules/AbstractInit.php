<?php

namespace Pupuga\module;

abstract class AbstractInit
{
	public $dataModule = array();
	public $dir;
	protected $classes = array();
	private static $instance;
	
	public function __construct($dir) 
	{
		$this->dir = $dir;
	}

	/**
	 * @return $this
	 */
	abstract public function init();
	
	/**
	 * @return $this
	 */
	public static function app()
	{
		if (self::$instance == null) {
			$class = get_called_class();
			self::$instance = new $class();	
		}

		return self::$instance;
	}
	
	/**
	 * @return $this
	 */
	protected function setDefineModule() 
	{
		$this->dataModule['moduleName'] = basename($this->dir);
		$this->dataModule['modulePath'] = $this->dir . DIRECTORY_SEPARATOR;
		$this->dataModule['moduleUrl'] = get_stylesheet_directory_uri() . '/pupuga-framework/' . MODULES_DIR_NAME . '/' . $this->dataModule['moduleName'] . '/';
		define('MODULEURL' . strtoupper($this->dataModule['moduleName']), $this->dataModule['moduleUrl']);
		$this->dataModule['moduleUrlAssets'] = get_stylesheet_directory_uri() . '/pupuga-framework/' . MODULES_DIR_NAME . '/' . $this->dataModule['moduleName'] . '/';
		define('MODULEURLASSETS' . strtoupper($this->dataModule['moduleName']), $this->dataModule['moduleUrl']);
		$this->dataModule['moduleControllerPath'] = $this->dataModule['modulePath'] . 'controllers' . DIRECTORY_SEPARATOR;
		$this->dataModule['moduleTemplatePath'] = $this->dataModule['modulePath'] . 'templates' . DIRECTORY_SEPARATOR;
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function requireClasses()
	{
		foreach ($this->classes as $class) {

			$file = $this->dataModule['moduleControllerPath'] . ucfirst( $class ) . '.php';
			require_once $file;

		}

		return $this;
	}
}