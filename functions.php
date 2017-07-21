<?php 

/**
*
* You must install carbon fields plugin
*
*/

function useBotLang() {
    return 'uk';
}

require_once __DIR__. '/pupuga-framework/modules/InitModules.php';

function test($values) {
	return 'This is test short code function!!!!!' . $values[0]->value;
}

?>