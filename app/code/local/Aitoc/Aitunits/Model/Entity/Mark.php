<?php
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     0JdTQfDMswel7fbpH42tkXIHe3fixI4GH3daX0aDVf
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitunits_Model_Entity_Mark extends Mage_Core_Model_Abstract
{
    protected $_handlers = array();
    protected $_flag;
    
    public function addHandler($sClassName)
    {
        if(!$this->hasHandler($sClassName))
        {
            array_push($this->_handlers,$sClassName);
        }
        return $this;
    }
    
    public function removeHandler($sClassName)
    {
        foreach($this->_handlers as $key=>$handlerName)
        {
            if($handlerName == $sClassName)
            {
                unset($this->_handlers[$key]);
                return true;
            }
        }
        return false;
    }
    
    public function hasHandler($sClassName)
    {
        //This class must be replaced so that he could handle the rewrited handler classes.
        if(array_search($sClassName, $this->_handlers)!==false)
        {
            return true;
        }
        return false;
    }
    
    public function insertInObject(Varien_Object $obj)
    {
        if(!$obj->hasData('aitunits_mark'))
        {
            $obj->setAitunitsMark($this);
            return $this;
        }
        return $this;
    }
    
    public function setFlag($sValue)
    {
        if(is_string($sValue))
        {
            $this->_flag = $sValue;
            return true;
        }
        return false; 
    }
    
    public function getFlag()
    {
        return $this->_flag;
    }
    
}