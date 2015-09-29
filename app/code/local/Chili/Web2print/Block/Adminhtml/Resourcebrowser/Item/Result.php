<?php
class Chili_Web2print_Block_Adminhtml_Resourcebrowser_Item_Result extends Mage_Adminhtml_Block_Template{
    private $_listDisplay = array('Documents');
    
    /* Returns html string
     * function for building layout resourcetree 
     */
    public function getChildrenItems($item,$params){
        $returnValue = '';
        $returnValue .= $this->getLayout()->getBlock('resourcebrowser_item_result')->setItem($item->item)->setParams($params)->toHtml();

        return $returnValue;
    }
    
    public function getDisplaySettings(){
        return $this->_listDisplay;
    }
    
    
}