<?php
class Chili_Web2print_Adminhtml_PdfsController extends Mage_Adminhtml_Controller_Action {
    
    protected function _initAction(){
        $this->loadLayout()->_setActiveMenu('web2print/pdfexport')->_addBreadcrumb(Mage::helper('adminhtml')->__("Pdf Manager"), Mage::helper('adminhtml')->__("Pdf Manager"));
        return $this;
    }
    
    public function indexAction(){
        try{
            $this->_initAction();
            $this->renderLayout();
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('adminhtml');
        }
    }

    public function massDelAction()
        {
            $pdfIds = $this->getRequest()->getParam('pdf');     
            if(!is_array($pdfIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web2print')->__('No items selected.'));
            }else {
                try {
                    $pdfModel = Mage::getModel('web2print/pdf');
                    foreach ($pdfIds as $pdfId) {
                        $pdfModel->load($pdfId)->delete();
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('web2print')->__(
                        'Total of %d record(s) were deleted.', count($pdfIds)
                    )
                    );
                    } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            $this->_redirect('*/*/index');
        }
        
        
        public function massDownloadAction(){
            $pdfIds = $this->getRequest()->getParam('pdf');
            
            if(!is_array($pdfIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web2print')->__('No items selected.'));
            }else {
                try{
                    $pdfModel = Mage::getModel('web2print/pdf');
                    $activated = false;
                    $usedFiles = array();
                    $time = strtotime("now");

                    $frontendSavePath = Mage::helper('web2print')->getPDFSavePath();           
                    $backendSavePath = Mage::helper('web2print')->getPDFSavePath('backend');

                    $tmpZipPath = $frontendSavePath.$time.'/';
                    $pdfModel->createDirectories($tmpZipPath);


                    foreach($pdfIds as $pdfId){
                        $pdf = $pdfModel->load($pdfId);
                        if($pdf->getStatus() === 'completed'){
                            $documentId = $pdf->getDocumentId();
                            if($documentId){
                                
                                $path = '';
                                if($pdfModel->getExportType() == 'frontend'){
                                    $path = $frontendSavePath.$documentId.'.pdf';
                                }else{
                                    $path = $backendSavePath.$documentId.'.pdf';
                                }
                                
                                if($pdfModel->checkIfFileExists($path)){
                                     $activated = true;             
                                     $newFileLocation = $tmpZipPath.$pdfModel->getExportType().'_'.$documentId.'.pdf';
                                     copy($path, $newFileLocation); 
                                     $usedFiles[] = $newFileLocation;
                                }       

                            }  
                        }
                    }

                    if($activated){
                        $pdfModel->compressFile('media/pdf/documents.zip', $usedFiles, $tmpZipPath);
                         
                        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/pdf/documents.zip';
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('web2print')->__('Downloadlink successfully created. Click <a href="%s" TARGET="_blank">here</a> to download.', $path));
                    }
                }catch(Exception $e){
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
                
                
            }
            $this->_redirect('*/*/index');
        }
        
         public function massRegenerateAction(){
            $pdfIds = $this->getRequest()->getParam('pdf');
            $pdfModel = Mage::getModel('web2print/pdf');
            if(!is_array($pdfIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('web2print')->__('No items selected.'));
            }else {
                try{
                     foreach($pdfIds as $pdfId){
                         $pdf = $pdfModel->load($pdfId);
                         $pdf->regeneratePdfs($pdf->getDocumentId(), $pdf->getExportType(), $pdf->getOrderItemId(), $pdf->getOrderIncrementId(), $pdf->getOrderId());
                     }
                     
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('web2print')->__('Files successfully regenerated'));
                }catch(Exception $e){
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            
            $this->_redirect('*/*/index');
         }



    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('web2print/pdfexport');
    }
        
}
