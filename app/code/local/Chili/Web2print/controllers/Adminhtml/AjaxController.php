<?php
class Chili_Web2print_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
    /**
     * returns a json object with tree structure
     */
    public function getresourcesAction(){
        $result = Array();
        
        $result['result'] = '';
        try{
            $webservice = Mage::getModel('web2print/api');
            
            if($this->getRequest()->getParam('website') && $this->getRequest()->getParam('website') != 'null'){
                $webservice->setWebsite($this->getRequest()->getParam('website'));
            }else{
                $webservice->setWebsite(null);
            }
            
            $params = $this->getRequest()->getParams('element');
            $blockParams['visibility'] = 'open';
            $blockParams['element'] = $params['element'];
            $blockParams['resourceType'] = $params['type'];

            if($params['type'] === 'Documents'){
                $xml = new SimpleXMLElement($webservice->getResourceTree($params['type'])); 
            }else{
                $xml = new SimpleXMLElement($webservice->searchForResource($params['type']));       
            }

            $this->loadLayout();

            if($xml->item->count()){
                $result['result'] .= $this->getLayout()->getBlock('resourcebrowser_result')->setItem($xml)->setParams($blockParams)->toHtml();
            }else{
                $result['result'] .= 'No resources found';
            }
            
            $result['status'] = 'success';
        }catch(Exception $e){
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
        }
        $this->getResponse()->setBody ( Zend_Json::encode($result) );
    }
    
    
    public function documentsAction(){
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('web2print/adminhtml_sales_order_view_tab_documents')->toHtml()
        );
    }
    

    public function downloadPdfsAction(){
         $params = $this->getRequest()->getParams();
         $result = Array();        
         $result['result'] = '';
         
         $usedFiles = array();
         
         try{
            $order = Mage::getModel('sales/order')->load($params['orderid']);
            $items = $order->getAllItems();
            $pdfModel = Mage::getModel('web2print/pdf');
            $activated = false;
            
            if(count($items) > 0){               
                $frontendSavePath = Mage::helper('web2print')->getPDFSavePath();
                $backendSavePath = Mage::helper('web2print')->getPDFSavePath('backend');                
                
                $tmpZipPath = $frontendSavePath.$order->getIncrementId().'/';
                $pdfModel->createDirectories($tmpZipPath);               
                
                foreach($items as $item){
                     $documentId = $item->getProductOptionByCode('chili_document_id');
                      if($documentId){     
                        
                        $exportTypes = array( 'frontend'=>$frontendSavePath.$documentId.'.pdf', 'backend'=>$backendSavePath.$documentId.'.pdf');
                        foreach($exportTypes as $key => $fileLocation){         
                             if($pdfModel->checkIfFileExists($fileLocation)){
                                 $activated = true;             
                                 $newFileLocation = $tmpZipPath.$key.'_'.$documentId.'.pdf';
                                 copy($fileLocation, $newFileLocation); 
                                 $usedFiles[] = $newFileLocation;
                             }
                        }
                      }
                }  
                
                
                if($activated){
                    $pdfModel->compressFile('media/pdf/order_'.$order->getIncrementId().'.zip', $usedFiles, $tmpZipPath);
                    $result['result'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/pdf/order_'.$order->getIncrementId().'.zip';
                }else{
                    $result['result'] = false;
                }
            }

            $result['status'] = 'success';
         }catch(Exception $e){
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
         }
         $this->getResponse()->setBody ( Zend_Json::encode($result) );
    }
    
    /**
     * Request pdfs if these are not available
     */
    public function regeneratePdfFilesAction() {
         $params = $this->getRequest()->getParams();
         $result = Array();        
         $result['result'] = '';

         try {
            $order = Mage::getModel('sales/order')->load($params['order_id']);
            $items = $order->getAllItems();
            $pdfModel = Mage::getModel('web2print/pdf');
            $website = $order->getStore()->getWebsite();

            foreach($items as $item) {
                // check for quote items with document ID's and add a new PDF task to the queue
                $documentId = $item->getProductOptionByCode('chili_document_id');                 
                
                if($documentId){                    
                     $pdfModel->regeneratePdfByOrderItemId($item->getId(),$website);
                }
            }
            
            $this->loadLayout();
            
            $orderItemsBlock =  $this->getLayout()->getBlock('order_documents_grid')->toHtml();
            $result['result'] = $orderItemsBlock;
            $result['status'] = 'success';
            
         }catch(Exception $e){
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
         }
         
         $this->getResponse()->setBody( Zend_Json::encode($result) );
    }
    
    /**
     * Refresh action will load the items block and returns it
     */
    public function refreshPdfFilesAction(){
        try{
            $result = Array();        
            $result['result'] = '';
            
            $this->loadLayout();
            
            $orderItemsBlock =  $this->getLayout()->getBlock('order_documents_grid')->toHtml();     
            $result['result'] = $orderItemsBlock;
            $result['status'] = 'success';
        }catch(Exception $e){
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
        }
        $this->getResponse()->setBody ( Zend_Json::encode($result) );
    }
    
    /**
     * Refresh action will load the items block and returns it
     */
    public function updatePdfFilesAction(){
        try{
            $result = Array();        
            $result['result'] = '';
            
            $this->loadLayout();
            
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
            Mage::getModel('web2print/pdf')->updatePdfs($order->getIncrementId());
            
            $orderItemsBlock =  $this->getLayout()->getBlock('order_documents_grid')->toHtml();     
            $result['result'] = $orderItemsBlock;
            $result['status'] = 'success';
        }catch(Exception $e){
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
        }
        $this->getResponse()->setBody ( Zend_Json::encode($result) );
    }
    
    /**
     * returns a json object with tree structure
     */
    public function loadItemsAction() {
        $result = Array();
        
        $result['result'] = '';
        try {
            $webservice = Mage::getModel('web2print/api');
            
            if ($this->getRequest()->getParam('website')) {
                $webservice->setWebsite($this->getRequest()->getParam('website'));
            } else {
                $webservice->setWebsite(null);
            }
            
            $params = $this->getRequest()->getParams('folder');
            $blockParams['visibility'] = 'open';
            $blockParams['element'] = $params['element'];
            $blockParams['resourceType'] = $params['type'];            
            $xml = new SimpleXMLElement($webservice->getLevelResourceTree($params['type'], base64_decode($params['folder']))); 

            $this->loadLayout();

            if ($xml->item->count()) {
                $result['result'] .= $this->getLayout()->getBlock('resourcebrowser_item_result')->setItem($xml)->setParams($blockParams)->toHtml();
                //$result['result'] = '<ul class="resource-level open"><li class="dir">Not implemented yet</li></ul>';
            } else {
                $result['result'] .= 'No resources found';
            }
            
            $result['status'] = 'success';
        } catch(Exception $e) {
            $result['status'] = 'error';
            $result['result'] = '<ul class="messages"><li class="error-msg"><ul><li><span>'.$e->getMessage().'</span></li></ul></li></ul>';
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }


    protected function _isAllowed()
    {
        return true;
    }
}
