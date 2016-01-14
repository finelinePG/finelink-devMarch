<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

class Aitoc_Aitcheckoutfields_Model_Rewrite_AdminSalesOrderCreate extends Mage_Adminhtml_Model_Sales_Order_Create
{
    // overwrite parent
    public function createOrder()
    {
        $mainModel = Mage::getModel('aitcheckoutfields/aitcheckoutfields');
        $oldOrderId='';
        
        /* {#AITOC_COMMENT_END#}
        if(isset($_SESSION['adminhtml_quote']['order_id'])||isset($_SESSION['adminhtml_quote']['reordered']))
        {
            $oldOrderId=isset($_SESSION['adminhtml_quote']['order_id'])?$_SESSION['adminhtml_quote']['order_id']:$_SESSION['adminhtml_quote']['reordered'];
            $oldOrder = Mage::getModel('sales/order')->load($oldOrderId);
            $storeId = $oldOrder->getStoreId();
            $websiteId = $oldOrder->getStore()->getWebsiteId();
        }else{
        	$quote = $this->getQuote();
        	$storeId = $quote->getStoreId();
            $websiteId = $quote->getStore()->getWebsiteId();
        }
        
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitcheckoutfields')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        if (!($ruler->checkRule('store',$storeId,'store') || $ruler->checkRule('store',$websiteId,'website')))
        {
        	if($oldOrderId)
        	{
        		$oldData = $mainModel->getOrderCustomData($oldOrderId, $storeId, true);
        		foreach ($oldData as $oldAttr){
        			if(in_array($oldAttr['type'],array('checkbox','radio','select','multiselect')))
        			{
        				$oldAttr['rawval'] = explode(',',$oldAttr['rawval']);
        			}
        			$_SESSION['aitoc_checkout_used']['adminorderfields'][$oldAttr['id']]=$oldAttr['rawval'];
        		} 
        	}
        }
        else 
        {
        {#AITOC_COMMENT_START#} */
            $attributeCollection = $mainModel->getAttributeCollecton();
            $data = Mage::app()->getRequest()->getPost('aitoc_checkout_fields');
            
            foreach($attributeCollection as $attribute)
            {
                if(isset($data[$attribute->getAttributeCode()]))
                {
                    if($attribute->getFrontend()->getInputType()!=='static')
                    {
                        $_SESSION['aitoc_checkout_used']['adminorderfields'][$attribute->getId()]=$data[$attribute->getAttributeCode()];
                    }
                }
            }
        /* {#AITOC_COMMENT_END#}
        }
        {#AITOC_COMMENT_START#} */

        $order = parent::createOrder();

        if (isset($_SESSION['aitoc_checkout_used']['new_customer']))
        {
            $_SESSION['aitoc_checkout_used']['register'] = $_SESSION['aitoc_checkout_used']['adminorderfields'];
            $customerId = $order->getCustomerId();
            $mainModel->saveCustomerData($customerId);
        }
        
        $orderId = $order->getId();
        $mainModel->saveCustomOrderData($orderId, 'adminorderfields');
        
        Mage::dispatchEvent('aitcfm_order_save_after', array('order' => $order, 'checkoutfields' => $order->getCustomFields()));
        
        $mainModel->clearCheckoutSession('adminorderfields');
        Mage::getSingleton('adminhtml/session')->unsetData('aitcheckoutfields_admin_post_data');
           		
        return $order;
    }
    
    // overwrite parent
    public function importPostData($data){
        $toReturn = parent::importPostData($data);
        		
		if($postData = Mage::app()->getRequest()->getPost('aitoc_checkout_fields'))
		{
		    if(!Mage::getSingleton('adminhtml/session')->hasData('aitcheckoutfields_admin_post_data'))
			{
			    Mage::getSingleton('adminhtml/session')->addData(array('aitcheckoutfields_admin_post_data'=>$postData));
			}
			elseif($postData != Mage::getSingleton('adminhtml/session')->getData('aitcheckoutfields_admin_post_data'))
			{
			    Mage::getSingleton('adminhtml/session')->addData(array('aitcheckoutfields_admin_post_data'=>$postData));
			}
		}
		        
        return $toReturn; 
    }
}


class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate extends Aitoc_Aitcheckoutfields_Model_Rewrite_AdminSalesOrderCreate
{
    public function initFromOrder(Mage_Sales_Model_Order $order)
    {
        try
        {
            parent::initFromOrder($order);
        }
        catch (Exception $e)
        {
            return Mage::app()->getFrontController()->getResponse()->setRedirect(getenv("HTTP_REFERER"));
        }
        
        return $this;
    }
}

