<?php

class Chili_Web2print_Block_Adminhtml_Sales_Order_View_Tab_Documents
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        $params = Mage::app()->getRequest()->getParams();
        $order = Mage::getModel('sales/order')->load($params['order_id']);

        return $order;
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array(
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        );
    }

    public function getOrderInfoData()
    {
        return array(
            'no_use_order_link' => true,
        );
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', array('order_id'=>$orderId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('PDF Documents');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('PDF Documents');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
