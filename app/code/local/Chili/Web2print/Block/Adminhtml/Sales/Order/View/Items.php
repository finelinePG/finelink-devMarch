<?php

class Chili_Web2print_Block_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        $params = Mage::app()->getRequest()->getParams();
        $order = Mage::getModel('sales/order')->load($params['order_id']);

        return $order->getItemsCollection();
    }
}
