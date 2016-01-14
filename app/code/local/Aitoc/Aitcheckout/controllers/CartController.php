<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckout_CartController extends Aitoc_Aitcheckout_Controller_Action
{
    
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
    
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }
    
    protected function _expireAjax()
    {
        if (!$this->_getQuote()->hasItems()
            //|| $this->_getOnepage()->getQuote()->getHasError()
            //|| $this->_getOnepage()->getQuote()->getIsMultiShipping()
            ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        return false;
    } 
    
    /**
     * Initialize coupon
     */
    public function couponPostAction()
    {   
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) 
        {
            $data = $this->getRequest()->getPost();
            $currentStep = $data['step'];
            if (!$this->_getQuote()->getItemsCount()) 
            {
                $this->getResponse()
                    ->setBody(
                        Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep))
                    );
                return;
            }
            $couponCode = (string) $this->getRequest()->getPost('coupon_code', '');
            if ($data['remove_coupon'] == 1) 
            {
                $couponCode = '';
            }
            $oldCouponCode = $this->_getQuote()->getCouponCode();
            if (!strlen($couponCode) && !strlen($oldCouponCode)) 
            {  
                $this->getResponse()
                    ->setBody(
                        Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep))
                    );
                return;
            }

            
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save(); 
                
            if ($couponCode) 
            {
                if ($couponCode == $this->_getQuote()->getCouponCode()) 
                {
                    $result = array('error' => 0, 'message' => Mage::helper('aitcheckout')->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode)));
                }
                else {
                    $result = array('error' => -1, 'message' => Mage::helper('checkout')->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
                }
            } else {
                $result = array('error' => 1, 'message' => Mage::helper('aitcheckout')->__('Coupon code was canceled.'));
            }
            $this->getResponse()
                ->setBody(
                    Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep, $result))
                );
        }   
        
    }

    /**
     * Update shoping cart data action
     */
    public function updateItemOptionsAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) 
        {
            try {
                $itemId = (int)$this->getRequest()->getParam('id');
                $productId = $this->_getQuote()->getItemById($itemId)->getProduct()->getId();
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                $increment = $stockItem->getQtyIncrements() ? $stockItem->getQtyIncrements() : 1;
                $sign = $this->getRequest()->getParam('sign');
                $data = $this->getRequest()->getPost();
                $cartData = $data['cart'];
                $currentStep = $data['step'];
                if (is_array($cartData)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $cartData[$itemId]['qty'] = $filter->filter($cartData[$itemId]['qty'] + $sign * $increment) ;

                    $cart = $this->_getCart();
                    if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                        $cart->getQuote()->setCustomerId(null);
                    }
                    $this->_getQuote()->unsetData('messages');
                    if (Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.4.2'))
                    {
                        $cartData = $cart->suggestItemsQty($cartData);
                    } 
                    $oldQty = $this->_getQuote()->getItemById($itemId)->getQty();
                    					
                    $cart->updateItems($cartData);
                    if(Mage::helper('aitcheckout')->isErrorQuoteItemQty()) {
                        //restoring old qty in cart
                        $cartData[$itemId]['qty'] = $oldQty;
                        $cart->updateItems($cartData);
                        //don't allow to save quote and it's items, they can't be changed now
                        if(method_exists($this->_getQuote(), 'preventSaving')) {
                            $this->_getQuote()->preventSaving();
                        }
                        $message = Mage::helper('aitcheckout')->getLastErrorMessage();
                        //for lower magento version error message can be duplicated, so we update it with our one
                        if(!$message || version_compare(Mage::getVersion(), '1.6.0.0', '<')) $message = Mage::helper('aitcheckout')->__('Cannot update the item.');
                        Mage::throwException($message);
                    }
                    $cart->save();
                }
                if ($this->_expireAjax()) {
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                if ($this->_getCart()->getCheckoutSession()->getUseNotice(true)) {
                    $this->_getCart()->getCheckoutSession()->addNotice($e->getMessage());
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->_getCart()->getCheckoutSession()->addError($message);
                    }
                }
            } catch (Exception $e) {
                $this->_getCart()->getCheckoutSession()->addException($e, Mage::helper('aitcheckout')->__('Cannot update the item.'));
                Mage::logException($e);
            }
            $this->getResponse()
                ->setBody(
                    Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep))
                );
        }
    }
    
    public function updatePostAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) 
        {
            try {
                $currentStep = $this->getRequest()->getPost('step'); 
                $cartData = $this->getRequest()->getParam('cart');
                $oldData = $cartData;
                if (is_array($cartData)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    foreach ($cartData as $index => $data) {
                        if (isset($data['qty'])) {
                            $cartData[$index]['qty'] = $filter->filter($data['qty']);
                            $oldData[$index]['qty'] = $this->_getQuote()->getItemById($index)->getQty();
                        }
                    }
                    $cart = $this->_getCart();
                    if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                        $this->_getQuote()->setCustomerId(null);
                    }
                    $this->_getQuote()->unsetData('messages');
                    if (Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.4.2'))
                    {
                        $cartData = $cart->suggestItemsQty($cartData);
                    }    
                    					
                    $cart->updateItems($cartData);
                    if(Mage::helper('aitcheckout')->isErrorQuoteItemQty()) {
                        //restoring old qty in cart
                        $cart->updateItems($oldData);
                        //don't allow to save quote and it's items, they can't be changed now
                        if(method_exists($this->_getQuote(), 'preventSaving')) {
                            $this->_getQuote()->preventSaving();
                        }
                        $message = Mage::helper('aitcheckout')->getLastErrorMessage();
                        //for lower magento version error message can be duplicated, so we update it with our one
                        if(!$message || version_compare(Mage::getVersion(), '1.6.0.0', '<')) $message = Mage::helper('aitcheckout')->__('Cannot update the item.');
                        Mage::throwException($message);
                    }
                    $cart->save();
                }
                if ($this->_expireAjax()) {
                    return;
                }
            } catch (Mage_Core_Exception $e) { 
                $this->_getCart()->getCheckoutSession()->addError($e->getMessage()); 
            } catch (Exception $e) {  
                $this->_getCart()->getCheckoutSession()->addException($e, Mage::helper('aitcheckout')->__('Cannot update shopping cart.'));
                Mage::logException($e);
            }
            $this->getResponse()
                ->setBody(
                    Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep))
                );             
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) 
        { 
            $currentStep = $this->getRequest()->getPost('step'); 
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                $this->_getCart()->removeItem($id)
                  ->save();
            }
            if ($this->_expireAjax()) {
                return;
            }
            $this->getResponse()
                ->setBody(
                    Mage::helper('core')->jsonEncode($this->_extractStepOutput($currentStep))
                );
        }
    }
    
 
    
    
    

}