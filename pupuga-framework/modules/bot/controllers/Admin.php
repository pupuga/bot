<?php

namespace Pupuga\module\bot;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Admin {
    
    protected static $lang;
    
	function __construct()
	{
        add_action('carbon_fields_register_fields', array($this, 'addFields') );
        add_action('carbon_register_fields', array($this, 'addFields'));
		add_action('admin_enqueue_scripts', array($this, 'addFooterScripts'));
        $this->lang = Init::app()->lang;
	}
	
	public function addFields() 
	{
        $container = Container::make('theme_options', __('Simple Bot'))
            ->set_page_parent('plugins.php');
        
        foreach ($this->lang as $language) {
            $container
                ->add_tab(__($language), array(
                    Field::make('textarea', 'botdata_'.$language, 'Data'),
                    Field::make('text', 'botname_'.$language, 'Name'),
                    Field::make('image', 'botimage_'.$language, 'Image')->set_value_type('url'),
                    Field::make('text', 'botinfo_'.$language, 'Info'),
                    Field::make('text', 'botstartblock_'.$language, 'Start block'),
                    Field::make('image', 'clientimage_'.$language, 'Image')->set_value_type('url')
                ));   
        }
	}

	public function addFooterScripts()
	{
		$moduleUrl = Init::app()->dataModule['moduleUrl']; 
        
		echo '<script type="text/javascript">var langBot = ' . json_encode($this->lang) . '</script>';
		
		wp_enqueue_style('style-codemirror', $moduleUrl . 'assets/modules/code-mirror/lib/codemirror.css');
		wp_enqueue_style('style-codemirror-admin', $moduleUrl . 'assets/css/admin.css');
		wp_enqueue_script('script-codemirror', $moduleUrl . 'assets/modules/code-mirror/lib/codemirror.js', array(), null, true);
		wp_enqueue_script('script-codemirror-mode', $moduleUrl . 'assets/modules/code-mirror/mode/xml/xml.js', array(), null, true);
		wp_enqueue_script('script-codemirror-admin', $moduleUrl . 'assets/js/admin.js', array(), null, true);
	}

}

new Admin();

?>