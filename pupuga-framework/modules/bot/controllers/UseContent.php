<?php

namespace Pupuga\module\bot;

class UseContent
{
    protected $language;
    public $dataContent;
    public $values;
    public $indexItem = 0;

    private static $instance;

    function __construct()
    {
        $this->language = Init::app()->language;
        $this->dataContent = Init::app()->dataObjects;
        $this->values = Init::app()->valueData;
        if (isset(Init::app()->valueData['debt']['index']) && Init::app()->valueData['debt']['index'] != '') {
            $this->indexItem = Init::app()->valueData['debt']['index'];
        } else {
            $this->indexItem = 0;
        }
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
    
    public function getValue() {
        return $this->values['debt']['value'];
    }

    public function getUnits() {
        return $this->values['debt']['units'];
    }

    public function getAnd() {
        return '&';
    }
    
}

?>
