<?php

namespace Pupuga\module\bot;

class UseContent
{
    protected $language;
    public $dataContent;
    public $values;
    public $indexItem;

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

    public function getRate()
    {
        $value = $this->dataContent->default->rateDisplay;
        return $value;
    }
    
    public function getYears()
    {
        if (isset($this->values['debt']['value']) && isset($this->dataContent->default->salary) && isset($this->dataContent->default->spent)) {
            $debt = $this->getRefinance();
            $salary = $this->dataContent->default->salary;
            $spent = $this->dataContent->default->spent;
            $value = ceil($debt / (($salary - ($salary * $spent / 100)) * 12));
            if ($value > 10) {
                $value = 'flere';
            }
        } else {
            $value = 1;
        }
        
        $value = $value . ' år';
        
        return $value;
    }

    public function getRefinance()
    {
        if (isset($this->values['debt']['value'])) {
            $rate = floatval($this->dataContent->default->rate);
            $debt = $this->values['debt']['value'];
            $value = round($debt * (1 + ($rate / 100)));    
        } else {
            $value = 0;
        }
        
        return $value;
    }

    public function getRefinanceBank()
    {
        if (isset($this->values['debt']['value'])) {
            $index = $this->indexItem;
            $object = $this->dataContent->organization[$index];
            $rate = floatval($object->rate);
            $debt = $this->values['debt']['value'];
            $value = round($debt * (1 + ($rate / 100)));
        } else {
            $value = 0;
        }

        return $value;    
    }

    public function getProfit()
    {
        $values = $this->getRefinance() - $this->getRefinanceBank();
        return $values;
    }

    public function getBank()
    {
        $index = $this->indexItem;
        $object = $this->dataContent->organization[$index];
        $object->track = str_replace('[and]', '&', $object->track);
        
        foreach (GetData::app()->replaceFunctions as $shortCode => $replaceFunction) {
            if (strrpos($object->description, $shortCode) !== false) {
                $object->description = str_replace($shortCode, $this->$replaceFunction(), $object->description);
            }
        }
        
        ob_start();
        require_once Init::app()->dir . '/templates/organization.php';
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }

    public function getName()
    {
        $index = $this->indexItem;
        $object = $this->dataContent->organization[$index];

        return $object->name;
    }

    public function getTarget() {
        $index = $this->indexItem;
        $object = $this->dataContent->organization[$index];

        return $object->track;
    }
    
    public function getValue() {
        return $this->values['debt']['value'];
    }

    public function getUnits() {
        return $this->values['debt']['units'];
    }
    
}

?>
