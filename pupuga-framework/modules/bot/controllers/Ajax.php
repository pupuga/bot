<?php

namespace Pupuga\module\bot;

class Ajax 
{
	function __construct() 
	{
		add_action('wp_ajax_getMessageBlock', array($this, 'getDataBlockAjax'));
		add_action('wp_ajax_nopriv_getMessageBlock', array($this, 'getDataBlockAjax'));
	}

	function getDataBlockAjax()
	{
		$slug = trim($_POST['target']);
        
		if ($slug != '') {
			$json = GetData::app()->getDataBlock($slug);
		} else {
			$json = '';
		}
		echo $json;

		exit;
	}
}

new Ajax();