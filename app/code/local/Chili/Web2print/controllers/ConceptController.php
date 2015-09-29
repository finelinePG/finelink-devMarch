<?php

class Chili_Web2print_ConceptController extends Mage_Core_Controller_Front_Action {

    /**
     * Action predispatch
     *
     * Check customer authentication for all actions
     */
    public function preDispatch() {
        parent::preDispatch();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        $action = $this->getRequest()->getActionName();
        $backendActionsNologin = array('add');

        if (!in_array($action, $backendActionsNologin)) {
            if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            }
        }
    }

    /**
     * Display customer conceptlist
     */
    public function conceptlistAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * function is called to save product to database.
     */
    public function addAction() {
        $post = $this->getRequest()->getPost();

        $ajaxResult = array('status' => 'failed', 'content' => '');

        if (Mage::helper('customer')->isLoggedIn()) {
            $customOptions = serialize($post);

            $session = Mage::getSingleton('customer/session');
            $product = Mage::getModel('catalog/product')->load($post['product_id']);

            // Prepare post data to store for later add to cart actions
            try {
                Mage::getModel('web2print/concept')->storeConcept($product, $session, $post['chili_document_id'], $customOptions);

                // Copy the document instead of using the original
                $api = Mage::getModel('web2print/api');
                $workingChiliDocumentId = $api->moveResourceItem($post['chili_document_id'], $product->getUrlKey(), 'Documents', 'concept');

                $ajaxResult['status'] = 'success';
                $ajaxResult['title'] = $this->__('Document saved to concepts list');
                $ajaxResult['content'] = '<br /><br />';
                $ajaxResult['content'] .= '<center><button type="button" title="" class="button btn-cart" onclick="setLocation(\'' . Mage::getUrl('*/*/conceptlist') . '\')"><span>View all concepts</button></span></center>';
            } catch (Exception $e) {
                $ajaxResult['status'] = 'failed';
                $ajaxResult['title'] = $this->__('An error occured');
                $ajaxResult['content'] = $e->getMessage();
            }
        } else {
            $ajaxResult['title'] = $this->__('An error occured');
            $ajaxResult['content'] = $this->__("You need to be logged in to add items to your concepts list");
        }

        $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
    }

    /**
     * function is called to update concepts: save description data.
     */
    public function updateAction() {
        $post = $this->getRequest()->getPost();
        $ajaxResult = array('status' => 'failed', 'content' => '');

        $conceptId = $this->getRequest()->getParam('id');
        $concept = Mage::getModel('web2print/concept')->load($conceptId);

        // Update the database record if this is an update of the description
        if ($post && isset($post['description']) && is_array($post['description'])) {
            try {
                foreach ($post['description'] as $key => $value) {
                    Mage::getModel('web2print/concept')->load($key)->setDescription($value)->save();
                }

                Mage::getSingleton('core/session')->addSuccess($this->__('Concepts list sucessfully updated'));
            } catch (Exception $e) {
                Mage::getSingleton('costumer/session')->addError($e->getMessage());
            }

            $this->_redirect('*/*/conceptlist');

            // No additional database updates are required at this point
        } else {
            $customOptions = serialize($post);

            if ($concept->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                $concept->setOptions($customOptions)->save();
            } else {
                Mage::throwException($this->__('Unable to load concept'));
            }

            Mage::helper('web2print/image')->clearChiliDocumentImageCache($concept->getChiliId(), 'concept');

            $ajaxResult['status'] = 'success';
            $ajaxResult['title'] = $this->__('This concept was successfully updated');
            $ajaxResult['content'] = '<br /><br />';
            $ajaxResult['content'] .= '<center><button id="" type="button" title="" class="button btn-cart" onclick="setLocation(\'' . Mage::getUrl('*/*/conceptlist') . '\')"><span>View all concepts</button></span></center>';

            $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
        }
    }

    /**
     * function is called to remove concepts from database.
     */
    public function removeAction() {
        $id = $this->getRequest()->getParam('id');

        try {
            $concept = Mage::getModel('web2print/concept')->load($id);

            if ($concept->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                $concept->delete();
            } else {
                Mage::throwException($this->__('Unable to load concept'));
            }
        } catch (Exception $e) {
            Mage::getSingleton('costumer/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/conceptlist');
    }

    /**
     * @return mixed
     */
    public function addtocartAction() {
        $ajaxResult = array('status' => 'failed', 'content' => '');

        $conceptId = $this->getRequest()->getParam('conceptId');
        $concept = Mage::getModel('web2print/concept')->load($conceptId);
        $product = Mage::getModel('catalog/product')->load($concept->getProductId());

        if (!$product->getId()) {
            Mage::getSingleton('checkout/session')->addException($e, Mage::helper('web2print')->__('Cannot add item to shopping cart'));
            return $this->_redirect('*/*');
        }

        $cart = Mage::getSingleton('checkout/cart');

        try {
            if ($concept->getCustomerId() !== Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                Mage::throwException($this->__('Unable to load concept'));
            }

            $product->setNewDocumentId($concept->getData('chili_id'));
            $params = unserialize($concept->getOptions());

            $cart->addProduct($product, $params);
            $cart->save();

            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

            $ajaxResult['status'] = 'success';
            $ajaxResult['redirect'] = Mage::getUrl('checkout/cart/');
        } catch (Mage_Core_Exception $e) {
            Mage::log($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addException($e, Mage::helper('web2print')->__('Cannot add item to shopping cart'));
            Mage::log($e->getMessage());
        }

        $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
    }

    /* Add concept from cart */
    public function addfromcartAction() {

        //get All needed data
        $session = Mage::getSingleton('customer/session');

        $quoteItemId = $this->getRequest()->getParam('quoteItemId');
        $documentId = Mage::helper('web2print')->getDocumentIdByQuoteItemId($quoteItemId);
        $quoteItem = Mage::getModel('sales/quote_item')->load($quoteItemId);
        $quote = Mage::getModel('sales/quote')->load($quoteItem->getQuoteId());
        $quoteItem = $quote->getItemById($quoteItemId);

        //Check if quote belongs to current customer
        
        if (Mage::getSingleton('customer/session')->getCustomer()->getId() !== $quote->getCustomer()->getId()) {
            Mage::throwException($this->__('Unable to load concept'));
        }
        // Copy the document instead of using the original

        $api = Mage::getModel('web2print/api');
        $workingChiliDocumentId = $api->moveResourceItem($documentId, $quoteItem->getProduct()->getUrlKey(), 'Documents', 'concept');

        // Filter BuyRequest
        $buyrequest = $quoteItem->getBuyRequest()->toArray();
        unset($buyrequest['uenc']);

        Mage::getModel('web2print/concept')->storeConcept($quoteItem->getProduct(), $session, $workingChiliDocumentId, serialize($buyrequest));

        $quoteItem->delete();

        Mage::getSingleton('core/session')->addSuccess($this->__('Item successfully moved to concepts list'));
        $this->_redirect('checkout/cart');
    }

}