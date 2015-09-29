<?php

class Chili_Web2print_EditorController extends Mage_Core_Controller_Front_Action {

    /**
     * Load the CHILI Editor and decide the mode (create/edit concept or quote item)
     */
    public function loadAction()
    {
        try {
            $paramLoadType = $this->getRequest()->getParam('type');
            $paramId = $this->getRequest()->getParam('id');
            $sessionCustomerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

            $editor = Mage::getSingleton('web2print/editor');

            switch($paramLoadType) {
                // Default create mode
                case 'product':
                    $productId = $paramId;
                    $editor->setMode('product');
                break;

                // Edit quote item mode (product in shoppingcart)
                case 'quoteitem':
                    $quoteItem = Mage::getModel('sales/quote_item')->load($paramId);
                    $quoteCustomerId = Mage::getModel('sales/quote')->load($quoteItem->getQuoteId())->getCustomerId();

                    if($sessionCustomerId !== $quoteCustomerId) {
                        Mage::throwException($this->__('Unable to load quote item'));
                    }

                    $editor->setMode('quoteitem');
                    $editor->setQuoteItem($quoteItem);
                    $editor->setChiliDocumentId(Mage::helper('web2print')->getDocumentIdByQuoteItemId($quoteItem->getId()));
                    $productId = $quoteItem->getProductId();
                break;

                // Edit concept item
                case 'concept':
                    // @todo add check for customer specific concepts
                    $concept = Mage::getModel('web2print/concept')->load($paramId);

                    if($sessionCustomerId !== $concept->getCustomerId()) {
                        Mage::throwException($this->__('Unable to load concept'));
                    }

                    $editor->setMode('concept');
                    $editor->setConcept($concept);
                    $editor->setChiliDocumentId($concept->getChiliId());
                    $productId = $concept->getProductId();
                break;
            }


            $product = Mage::getModel('catalog/product')->load($productId);


            if(!Mage::helper('web2print/data')->isProductAllowed($product)) {
                Mage::throwException($this->__('You are not allowed to edit this product'));
            }

            Mage::register('product',$product);
            Mage::register('current_product',$product);

            $editor->setProduct($product);
            $editor->requestChiliEditorData($paramLoadType);

            $this->loadLayout();
            $this->renderLayout();

        } catch(Exception $e) {
            //Show error if configured
            if(Mage::getStoreConfig('web2print/connection/redirect_exception')) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }

            //redirect to page
            $this->_redirect(Mage::getStoreConfig('web2print/connection/exception_cms'));
        }
    }

}
