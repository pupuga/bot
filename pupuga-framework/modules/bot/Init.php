<?php

namespace Pupuga\module\bot;

use Pupuga\module as Module;

class Init extends Module\AbstractInit
{
	
	public $valueData = array();
	public $lang = array('ua', 'en');
    public $language;

    protected $classes = array(
		'Admin',
		'ShortCode',
		'AbstractGetData',
		'GetData',
		'Ajax'
	);
	
	public function __construct() 
	{
		parent::__construct(__DIR__);
	}
	
	public function setCurrentLanguage()
    {
        $this->language = $this->lang[0];
        $langFunctionName = 'useBotLang';
        if (function_exists($langFunctionName)) {
            $language = trim($langFunctionName());
            if ($language) {
                foreach ($this->lang as $languageLoop) {
                    if ($languageLoop === $language) {
                        $this->language = $languageLoop;
                    }
                }
            }   
        }
        
        return $this;
    }
	
	public function init()
	{
		$this
            ->setCurrentLanguage()
			->setDefineModule()
			->setCookies()
			->requireClasses()
			->setStylesScripts();

		add_shortcode('bot', array($this, 'startShortCode'));
		
		return $this;
	}
	
	public function start($language)
	{
		$objectData = GetData::app()
			->getConfig();

		$slug = (trim($objectData->config['botConfiguration']['botStartBlock']) != '') 
            ? $objectData->config['botConfiguration']['botStartBlock'] 
            : 'block-1';
		
		$dataBlock = $objectData
			->setDataBlock($slug)
			->changeArray()
			->convertDataToJSON();
			
		wp_enqueue_script('script-common', MODULEURLBOT . 'assets/js/common.js', array(), null, true);
		
		ob_start();
		echo '
		<script type="text/javascript">
			var lang = ' . json_encode($this->lang) . ',
			    langCurrent = "' . $language . '",
			    data' . ucfirst($this->dataModule['moduleName']) . ' = '  . $dataBlock . ',
				config' . ucfirst($this->dataModule['moduleName']) . ' = '  . json_encode($objectData->config, JSON_UNESCAPED_SLASHES) . ',
				url' . ucfirst($this->dataModule['moduleName']) . ' = "' . $this->dataModule['moduleUrlAssets'] . 'assets/";
		</script>';
		require_once $this->dataModule['moduleTemplatePath'].'index.php';
		$content = ob_get_contents();
		ob_clean();
		
		return $content;
	}

	public function startShortCode() {
		return $this->start($this->language);
	}
	
	public function setStylesScripts()
	{
		if (!is_admin()) {
			wp_enqueue_script( 'jquery' );
			wp_localize_script('jquery', '$ajax', array(
				'url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('check-nonce')
			));

			wp_enqueue_script('script-mustache', MODULEURLBOT . 'assets/js/mustache.js', array(), null, true);
			wp_enqueue_style('style-common', MODULEURLBOT . 'assets/css/common.css');
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function setCookies() {

		if (isset($_POST['value']) && trim($_POST['value']) != ''){

			$valueData = array();
			$value = trim($_POST['value']);

			if(isset($_POST['units']) && trim($_POST['units']) != '') {
				$units = trim($_POST['units']);
			} else {
				$units = '';
			}

			if(isset($_POST['name']) && trim($_POST['name']) != '') {
				$name = trim($_POST['name']);
			} else {
				$name = '';
			}

			/** 
			 *  if  cookie is not empty
			 */
			if (isset($_COOKIE['pupugaBotValue']) && trim($_COOKIE['pupugaBotValue']) != '') {
				$valueData = json_decode(str_replace('\\', '', $_COOKIE['pupugaBotValue']));
				if (!$valueData) {
					$valueData = array();
				}
			}

			$valueData[] = array('value' => $value, 'units' => $units, 'name' => $name);
			
			setcookie('pupugaBotValue', json_encode($valueData), 0, '/', '', false);

			$this->valueData = $valueData;
			
		} else {
			setcookie('pupugaBotValue', 0, time() - 3600, '/', '', false);
			$this->valueData = array();
		}
		
		return $this;
	}
	
}

Init::app()->init();

?>