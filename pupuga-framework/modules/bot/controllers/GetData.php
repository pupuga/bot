<?php

namespace Pupuga\module\bot;

use Pupuga\Helper;

class GetData extends AbstractGetData
{
    
	function __construct() {
		parent::__construct();
		$this->replaceFunctions = array(
			'[formula]' => 'getFormula'
		);
	}

	protected function setImageAvatar($name)
	{
		$image = carbon_get_theme_option($name . 'image_'.$this->language);
		if ($name == 'bot') {
			if (rand(1, 1) == 1) {
				$sex = 'g';
			} else  {
				$sex = 'm';
			}	
		} else {
			$sex = '';
		}
		$imageDefault = MODULEURLBOT . 'assets/images/'. $name . $sex .'.svg';

		$image = ($image != '') ? $image : $imageDefault;
		
		return $image;
	}

	/**
	 * @return $this
	 */
	public function getConfig()
	{
		$this->config['botConfiguration'] = array(
			'botName' => carbon_get_theme_option('botname_'.$this->language),
			'botImage' => $this->setImageAvatar('bot'),
			'botInfo' => carbon_get_theme_option('botinfo_'.$this->language),
			'botStartBlock' => carbon_get_theme_option('botstartblock_'.$this->language)
		);
		
		$this->config['clientConfiguration'] = array(
			'clientImage' => $this->setImageAvatar('client'),
		);
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function getData() 
	{
		$this->data = carbon_get_theme_option('botdata_'.$this->language);
		$this->xmlToObjects();
		
		return $this;
	}
	
}

?>