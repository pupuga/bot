<?php

/**
 * config
 *      botConfiguration => name
 *                          image
 *                          info
 *      clientConfiguration => image
 *      start => block-1 block-i ..
 *      
 *  
 * data[block]
 *      message => 1..n
 *      answer
 *          type => inputText
 *                  inputNumber
 *                  button
 *          name
 *          placeholder
 *          request
 *
 */

namespace Pupuga\module\bot;

use Pupuga\Helper;

abstract class AbstractGetData
{

	/**
	 * @var AbstractGetData
	 */
	
	public $data = array();
	public $dataBlock;
	public $config = array();
	private static $instance;
	protected $replaceFunctions = array();
    public $language;
	
	function __construct() 
	{
        $this->language = Init::app()->language;
        if ($this->language == '') {
            $this->language = trim($_POST['langCurrent']);
        }
		$this->getData();
	}
	
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
	abstract protected function getConfig();

	/**
	 * @return $this
	 */
	abstract protected function getData();

	/**
	 * @return $this
	 */
	public function setDataBlock($slug)
	{
		$this->dataBlock = $this->data->$slug;

		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function changeArray()
	{
		$search = array();
		$replace = array();
		
		if (is_object($this->dataBlock)) {
			$this->dataBlock = get_object_vars($this->dataBlock);
		}
		
		foreach ($this->dataBlock as $key => $value) {
			if (!is_array($value)) {
				$this->dataBlock[$key] = array($value);
			}	
		}
		
		$replaceData = array (
			'[value]' =>  trim($_POST['value']),
			'[units]' => trim($_POST['units']),
			'[' => '<',
			']' => '>',
		);
		
		foreach ($replaceData as $key => $value) {
			$search[] = $key;
			$replace[] = $value;
		}
		
		foreach ($this->dataBlock['message'] as $key => $value) {
			foreach ($this->replaceFunctions as $shortCode => $replaceFunction) {
				if (strrpos($value, $shortCode) !== false && method_exists(ShortCode::app(), $replaceFunction)) {
					$value = str_replace($shortCode, ShortCode::app()->$replaceFunction(), $value);
				}
			}
			$value = str_replace($search, $replace, $value);
			$this->dataBlock['message'][$key] = $value;
		}
		
		foreach ($this->dataBlock['answer'] as $key => $value) {
			$value = str_replace($search, $replace, $this->dataBlock['answer'][$key]->inscription);
			$this->dataBlock['answer'][$key]->inscription = $value;
		};
				
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addConfig()
	{
		$this->dataBlock['config'] = $this->config;

		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function xmlToObjects()
	{
		$xml = '<?xml version="1.0" standalone="yes"?><blocks>' . $this->data . '</blocks>';
		$this->data = new \SimpleXMLElement($xml);

		return $this;
	}

	public function convertDataToJSON()
	{
		$json = json_encode($this->dataBlock, JSON_UNESCAPED_SLASHES);

		return $json;
	}

	public function getDataBlock($slug = '')
	{
		$dataBlock = $this
			->setDataBlock($slug)
			->changeArray()
			->convertDataToJSON();

		return $dataBlock;
	}

}

?>