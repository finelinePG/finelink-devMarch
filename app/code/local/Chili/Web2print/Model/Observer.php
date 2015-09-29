<?php

class Chili_Web2print_Model_Observer {

    /**
     * EVENT LISTENER
     * 
     * Sets the chili document id form the session as option in the quote
     * Move the file to the temporary quote items folder 
     * 
     * @param type $cartItem 
     */
    public function setQuoteDocumentId($cartItem) {
        $product = $cartItem->getProduct();

        $requestChiliDocumentId = Mage::app()->getRequest()->getParam('chili_document_id');


        if ($product->getWeb2printDocumentId() != '') {

            // @todo remove line below
            $quoteItem = $cartItem->getQuoteItem()->setProduct($product);

            //Get document id of current item
            //If reorder, take from order item
            //If add to cart, take from session
            if (Mage::app()->getRequest()->getRouteName() == 'sales' && Mage::app()->getRequest()->getActionName() == 'reorder') {
                $reorderDocumentIds = Mage::registry('reorder_document_array');
                $documentId = $reorderDocumentIds[$product->getId()][0];
                array_shift($reorderDocumentIds[$product->getId()]);
                Mage::unregister('reorder_document_array');
                Mage::register('reorder_document_array', $reorderDocumentIds);

            } elseif($product->getNewDocumentId()){
                $documentId = $product->getNewDocumentId();

            } else {
                $documentId = $requestChiliDocumentId;
            }
          
            //Try to move the item to the quotes folder on CHILI
            try {
                $api = Mage::getModel('web2print/api');
                if ($api->isServiceAvailable()) {
                    $documentId = $api->moveResourceItem($documentId, $product->getUrlKey());
                } else {
                    Mage::getSingleton('checkout/session')->addError('Service currently unavailable.');
                    return;
                }
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addError('Document path not properly configured in backend. Clear session after changes');
                throw new Exception($e->getMessage());
            }

            //Set the right document ID as quote item option
            $option = new Varien_Object();
            $option->setProduct($quoteItem->getProduct());
            $option->setCode('chili_document_id');
            $option->setValue($documentId);
            $quoteItem->addOption($option);
            $product->setNewDocumentId("");

            //clear all pre-generated images from the cache
            Mage::helper('web2print/image')->clearChiliDocumentImageCache($documentId, 'cart');
        }
    }

    /**
     * Make sure to add the chili document ID to the quote items
     * @param $observer
     */
    public function setQuoteDocumentIdAfterUpdate($observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();

        $postData = Mage::app()->getRequest()->getPost();
        $documentId = $postData['chili_document_id'];

        //Set the right document ID as quote item option
        $option = new Varien_Object();
        $option->setProduct($quoteItem->getProduct());
        $option->setCode('chili_document_id');
        $option->setValue($documentId);
        $quoteItem->addOption($option);
        
        //clear session var
        $this->removeDocumentIdBySku($quoteItem->getProduct()->getSku());
        $type = Mage::helper('catalog/image')->getImageConversionProfile('cart');
        $cacheModel = Mage::getSingleton('core/cache');
        $cacheModel->remove('image_' . $type . '_' . $documentId);
    }

    /**
     * Returns document id
     * @param type $sku 
     */
    protected function getDocumentIdBySku($product) {
        $documentIds = Mage::getSingleton('checkout/session')->getChiliDocumentIds();

        $documentId = $documentIds[$product->getSku()];

        //If document id is empty, duplicate document because editor is not loaded
        if (!$documentId) {
            $originalDocumentId = Mage::helper('web2print')->getItemId($product->getWeb2printDocumentId());
            $editorApi = Mage::getModel('web2print/editor');

            $documentId = $editorApi->getPersonalDocument($originalDocumentId, $product->getUrlKey(), $product->getSku());
        }

        return $documentId;
    }

    protected function removeDocumentIdBySku($sku) {
        $documentIds = Mage::getSingleton('checkout/session')->getChiliDocumentIds();
        unset($documentIds[$sku]);
        Mage::getSingleton('checkout/session')->setChiliDocumentIds($documentIds);
    }

    /**
     * EVENT LISTENER
     * 
     * When saving an order, create for each order item a pdf and save url
     * @todo	activate this method again (system.xml) / changes have already been made
     */
    public function orderPlaceAfterCreatePdf($observer) {
        // Load the order and order items related to the event
        $order = $observer->getOrder();
        $orderItems = $order->getAllItems();
        $website = $order->getStore()->getWebsite();
        $storeId = $order->getStore()->getId();

        foreach ($orderItems as $orderItem) {
            $documentId = $orderItem->getProductOptionByCode('chili_document_id');

            try {
                if (!empty($documentId)) {
                    //Frontend
                    $pdfFrontend = Mage::getModel('web2print/pdf')->getCollection()
                                        ->addFieldToFilter('document_id', $documentId)
                                        ->addFieldToFilter('export_type', 'frontend')
                                        ->getFirstItem();
                    if (!$pdfFrontend) {
                        $pdfFrontend = Mage::getModel('web2print/pdf');
                    }
                    $pdfFrontend->setDocumentId($documentId);
                    $pdfFrontend->setOrderItemId($orderItem->getId());
                    $pdfFrontend->setOrderId($order->getId());
                    $pdfFrontend->setOrderIncrementId($order->getIncrementId());
                    $pdfFrontend->setCreatedAt(date("Y-m-d H:i:s"));
                    $pdfFrontend->setExportType('frontend');

                    if (Mage::helper('web2print')->isValidExportType($website, 'frontend')) {
                        $pdfFrontend->setStatus('queued');
                        $pdfFrontend->setExportProfile(Mage::helper('web2print')->getPdfExportProfile('frontend', $website, $orderItem->getProductId(), $storeId));
                        $pdfFrontend->save();
                    } else {
                        $pdfFrontend->setStatus('no-pdf-export-settings-found');
                        $pdfFrontend->save();
                    }

                    //Backend
                    $pdfBackend = Mage::getModel('web2print/pdf');
                    $pdfBackend->setDocumentId($documentId);
                    $pdfBackend->setOrderItemId($orderItem->getId());
                    $pdfBackend->setOrderId($order->getId());
                    $pdfBackend->setOrderIncrementId($order->getIncrementId());
                    $pdfBackend->setCreatedAt(date("Y-m-d H:i:s"));
                    $pdfBackend->setExportType('backend');

                    if (Mage::helper('web2print')->isValidExportType($website, 'backend')) {
                        $pdfBackend->setStatus('queued');
                        $pdfBackend->setExportProfile(Mage::helper('web2print')->getPdfExportProfile('backend', $website, $orderItem->getProductId(), $storeId));
                        $pdfBackend->save();
                    } else {
                        $pdfBackend->setStatus('no-pdf-export-settings-found');
                        $pdfBackend->save();
                    }
                }
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
    }

    /**
     * EVENT LISTENER
     * 
     * Sets the document id as option in order item
     * @param type $observer
     * @return Chili_Web2print_Model_Observer 
     */
    public function convertQuoteItemToOrderItem($observer) {
        //Get order and quote item
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();

        //Get document id from quote item
        $documentId = Mage::helper('web2print')->getDocumentIdByQuoteItemId($item->getId());
        if ($documentId) {
            //Get current order item options
            $options = $orderItem->getProductOptions();

            //Add document id to options array
            $options['chili_document_id'] = $documentId;

            //Save order options
            $orderItem->setProductOptions($options);

            //Move document on CHILI server
            Mage::getModel('web2print/api')->moveResourceItem($documentId, $item->getProduct()->getUrlKey(), 'Documents', 'order');
        }
        return $this;
    }

    /**
     * EVENT LISTENER
     * 
     * Creates a log record in web2print_log table
     * @param array $observer 
     */
    public function logApiCall($observer) {
        if (Mage::getStoreconfig('web2print/general/debug')) {
            $method = $observer->getMethod();
            $settings = $observer->getSettings();

            $logRecord = Mage::getModel('web2print/log');

            $logRecord->setMethod($method);
            $logRecord->setParameters(serialize($settings));

            $logRecord->save();
        }

        return $this;
    }

    /**
     * EVENT LISTENER
     * Create a document array of the reordered documents 
     */
    public function reorderPreDispatch($observer) {

        $orderid = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderid);

        $documentIds = array();

        foreach ($order->getItemsCollection() as $orderItem) {
            $documentIds[$orderItem->getProductId()][] = $orderItem->getProductOptionByCode('chili_document_id');
        }

        Mage::register('reorder_document_array', $documentIds);
    }

    public function convertOrderToQuote($observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $documentIds = array();

        foreach ($order->getItemsCollection() as $orderItem) {
            $documentIds[$orderItem->getProductId()][] = $orderItem->getProductOptionByCode('chili_document_id');
        }


        foreach ($quote->getAllItems() as $quoteItem) {
            $documentId = $documentIds[$quoteItem->getProduct()->getId()][0];
            if ($documentId) {
                $option = new Varien_Object();
                $option->setProduct($quoteItem->getProduct());
                $option->setCode('chili_document_id');
                $option->setValue($documentId);
                $quoteItem->addOption($option);
            }
        }
    }

    public function convertQuoteItemToOrderItemAdmin($observer) {
        //Get order and quote item
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();

        //Get document id from quote item

        $options = Mage::getModel('sales/quote_item_option')->getCollection();
        $options->addFieldToFilter('item_id', $item->getId());
        $options->addFieldToFilter('code', 'chili_document_id');
        $options->load();

        foreach ($options as $option) {
            $documentId = $option->getValue();
            break;
        }

        if ($documentId) {
            //Get current order item options
            $options = $orderItem->getProductOptions();

            //Add document id to options array
            $options['chili_document_id'] = $documentId;

            //Save order options
            $orderItem->setProductOptions($options);
        }
        return $this;
    }

    /**
     * EVENT LISTENER
     * Catalog product save check on document id
     * @param type $observer 
     */
    public function prepareProductSave($observer) {
        $product = $observer->getEvent()->getProduct();

        if ($product->getWeb2printDocumentId()) {
            if (count($product->getWebsiteIds()) > 1) {
                Mage::throwException('A web2print product can only contain to 1 website. Product not saved');
            }
        }
    }

    /**
     * CRONTAB
     * Run the update pdf's cronjob
     *
     * @param Mage_Cron_Model_Schedule $cronInfo
     */
    public function cronUpdatePdfs($cronInfo)
    {
        Mage::getModel('web2print/pdf')->updatePdfs();
    }

}
