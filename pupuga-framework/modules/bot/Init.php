<?php

namespace Pupuga\module\bot;

use Pupuga\module as Module;

class Init extends Module\AbstractInit
{

    public $valueData = array();
    public $language;
    public $lang = array('no');
    public $dataObjects = array();

    protected $classes = array(
        'Admin',
        'UseContent',
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
            ->getDataObjects()
            ->clearCookies()
            ->setCookies()
            ->setDefineModule()
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
			    data' . ucfirst($this->dataModule['moduleName']) . ' = ' . $dataBlock . ',
				config' . ucfirst($this->dataModule['moduleName']) . ' = ' . json_encode($objectData->config, JSON_UNESCAPED_SLASHES) . ',
				url' . ucfirst($this->dataModule['moduleName']) . ' = "' . $this->dataModule['moduleUrlAssets'] . 'assets/";
		</script>';
        require_once $this->dataModule['moduleTemplatePath'] . 'index.php';
        $content = ob_get_contents();
        ob_clean();
        
        return $content;
    }

    public function startShortCode()
    {
        return $this->start($this->language);
    }

    public function setStylesScripts()
    {
        if (!is_admin()) {
            wp_enqueue_script('jquery');
            wp_localize_script('jquery', '$ajax', array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('check-nonce')
            ));
            wp_enqueue_script('script-mustache', MODULEURLBOT . 'assets/js/mustache.js', array(), null, true);
            wp_enqueue_style('style-common', MODULEURLBOT . 'assets/css/common.css');
        }

        return $this;
    }


    private function clearCookies()
    {
        if (!is_admin()) {
            setcookie('pupugaBotValue', 0, time() - 3600, '/', '', false);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setCookies()
    {
        
        $valueData = array();

        if (isset($_COOKIE['pupugaBotValue']) && trim($_COOKIE['pupugaBotValue']) != '') {
            $valueData = json_decode(str_replace('\\', '', $_COOKIE['pupugaBotValue']));
            if (!$valueData) {
                $valueData = array();
            }
        }

        if (isset($_POST['value']) && trim($_POST['value']) != '') {
            $value = trim($_POST['value']);
        } else {
            $value = '';
        }

        if (isset($_POST['units']) && trim($_POST['units']) != '') {
            $units = trim($_POST['units']);
        } else {
            $units = '';
        }

        if (isset($_POST['name']) && trim($_POST['name']) != '') {
            $name = trim($_POST['name']);
        } else {
            $name = '';
        }

        $indexReplace = false;
        $index = 0;
        foreach ($valueData as $key => $valueRow) {
            $index = $valueRow->index;
            if (trim($valueRow->name) == $name) {
                $indexReplace = $key;
            }
            if (isset($_POST['index']) && trim($_POST['index']) !== '' && trim($valueRow->name) == trim($_POST['index'])) {
                $name = trim($valueRow->name);
                $value = trim($valueRow->value);
                $units = trim($valueRow->units);
                $indexReplace = $key;
                if ($index + 1 >= count($this->dataObjects->organization)) {
                    $index = 0;   
                } else {
                    $index++;
                }
            }
            $this->valueData[trim($valueRow->name)] = array('value' => $valueRow->value, 'units' => $valueRow->units, 'index' => $index);
        }
        if ($name) {
            $this->valueData[$name] = array('value' => $value, 'units' => $units, 'index' => $index);
            $valuesNew = array('name' => $name, 'value' => $value, 'units' => $units, 'index' => $index);
            if ($indexReplace !== false) {
                $valueData[$indexReplace] = $valuesNew;
            } else {
                $valueData[] = $valuesNew;
            }
            setcookie('pupugaBotValue', json_encode($valueData), 0, '/', '', false);
        }
        
        return $this;
    }


    /**
     * get all data from xml data
     */
    public function getDataObjects()
    {
        $data = carbon_get_theme_option('content_data_'.$this->language);
        $xml = '<?xml version="1.0" standalone="yes"?><data>' . $data . '</data>';
        $this->dataObjects = new \SimpleXMLElement($xml);
        return $this;
    }

}

Init::app()->init();

?>