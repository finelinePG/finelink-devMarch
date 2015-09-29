<?php

class Chili_Web2print_Model_Rewrite_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item {

    /**
     * Compare item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  bool
     */
    public function compare($item)
    {
        if($item->getOptionByCode('chili_document_id')) {
            return false;
        }

        return parent::compare($item);
    }
}