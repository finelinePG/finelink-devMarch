<?php

class Chili_Web2print_Model_Pdf extends Mage_Core_Model_Abstract
{
    const HIRES_PDF = 'backend';
    const LOWRES_PDF = 'frontend';

    protected $api;
    
    public function _construct() {
        parent::_construct();
        $this->_init('web2print/pdf');
        
        try{
           $this->api = Mage::getModel('web2print/api'); 
        }catch(Exception $e){
           throw new Exception($e->getMessage());           
        }
    }

    /**
     *  remove pdf and create  task
     */
    public function regeneratePdfs($documentId, $exportType, $orderItemId, $orderIncrementId, $orderId){ 
        if (!$this->api->isServiceAvailable()) {
            Mage::helper('web2print')->log("regeneratePdfs has not run because service is not available");
            return null;
        }
        try{
            $fileLocation = Mage::helper('web2print')->getPDFSavePath($exportType).$documentId.'.pdf';
            $pdfCollection = $this->getCollection()->addFieldToFilter('document_id', $documentId)->addFieldToFilter('export_type', $exportType)->addFieldToFilter('order_item_id', $orderItemId);            
    
            if($pdfCollection->getSize() > 0){
                foreach($pdfCollection as $pdf){
                    if($this->checkIfFileExists($fileLocation)){
                        unlink($fileLocation);
                    }                        
                        
                    $this->api->createPDF($documentId, $exportType, 1, $pdf); 
                    break;
                }
            }else{
               $orderInfo = array('orderItemid' => $orderItemId, 'orderIncrementId' => $orderIncrementId, 'orderId' => $orderId);               
               $this->api->createPDF($documentId, $exportType, 1, null, $orderInfo);                       
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     *  Regenerate new PDF's for a specific quote item ID
     */
    public function regeneratePdfByOrderItemId($orderItemId,$website){
        if (!$this->api->isServiceAvailable()) {
            Mage::helper('web2print')->log("regeneratePdfByOrderItemId has not run because service is not available");
            return null;
        }
        try{
            // Fetch the PDF collection for this quote item
            $orderItemPdfs = Mage::getModel('web2print/pdf')->getCollection()->addFieldToFilter('order_item_id', $orderItemId);

            $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
            $productId = $orderItem->getProductId();

            if(count($orderItemPdfs)){
                foreach($orderItemPdfs as $pdf){
                    // Remove old pdf file and reset the Pdf record
                    if($this->checkIfFileExists($pdf->getPath())){
                        unlink($pdf->getPath());
                    }
                    
                    if(Mage::helper('web2print')->isValidExportType($website,$pdf->getExportType())){
                        $pdf->setPath('');
                        $pdf->setTaskId('');
                        $pdf->setMessage(null);
                        $pdf->setStatus('queued');
                        $pdf->setUpdatedAt(date("Y-m-d H:i:s"));
                        $pdf->setExportProfile(Mage::helper('web2print')->getPdfExportProfile($pdf->getExportType(), $website, $productId));
                        $pdf->save();
                    }else{
                        $pdf->setPath('');
                        $pdf->setTaskId('');
                        $pdf->setStatus('no-pdf-export-settings-found');
                        $pdf->save();
                    }
                }
                
            }else{
                // @todo Mage add functionality to create new pdf records when they were removed
            }
    
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Create task for queued pdfs and download pdfs
     */
    public function updatePdfs($orderId = null) {
        if (!$this->api->isServiceAvailable()) {
            Mage::helper('web2print')->log("updatePdfs has not run because service is not available");
            return null;
        }
        try{
            //First create task id for queued pdfs
            $queuedPdfs = Mage::getModel('web2print/pdf')->getCollection()->addFieldToFilter('status', 'queued');
            
            if($orderId){
                $queuedPdfs->addFieldToFilter('order_increment_id',$orderId);
            }

            if(count($queuedPdfs)){
                foreach($queuedPdfs as $queuedPdf){
                    //Generate task id for pdf item
                    $this->api->createPdfTask($queuedPdf);
                }
            }
            
            //Second check status of requested pdfs and download
            $requestedPdfs = Mage::getModel('web2print/pdf')->getCollection()->addFieldToFilter('status', array('in'=>array('requested','running','queued-chili')));
            
            if($orderId){
                $requestedPdfs->addFieldToFilter('order_increment_id',$orderId);
            }
            
            if(count($requestedPdfs)) {
                foreach($requestedPdfs as $requestedPdf) {
                    $website = Mage::getModel('sales/order')->loadByIncrementId($requestedPdf->getOrderIncrementId())->getStore()->getWebsite();
                    $this->api->setWebsite($website->getId());
                    $taskstatus = $this->api->getTaskStatus($requestedPdf->getTaskId());
                    
                    $requestedPdf->setMessage(null);
                    
                    if ($taskstatus == "") {
                        $requestedPdf->setStatus('task-error');
                        $requestedPdf->setMessage('Call to retrieve task returned no result.');
                        $requestedPdf->setUpdatedAt(date('Y-m-d H:i:s'));
                        $requestedPdf->save();
                        continue;
                    }
                    
                    $taskXml = simplexml_load_string($taskstatus);
                    if ($taskXml['found'] == 'false') {
                        $requestedPdf->setStatus('task-error');
                        $requestedPdf->setMessage('Task not found.');
                        $requestedPdf->setUpdatedAt(date('Y-m-d H:i:s'));
                        $requestedPdf->save();
                        continue;
                    }
                    
                    $pdfXml = simplexml_load_string((string)$taskXml['result']);
                    $pdfUrl = $pdfXml['url'];
                    $taskErrorMessage = (string)$taskXml['errorMessage'];
                    
                    $taskXml['started'] = (string) $taskXml['started'];
                    $taskXml['finished'] = (string) $taskXml['finished'];
                                       
                    if($pdfUrl && $taskErrorMessage == '') {
                        $filename = 'order_'.$requestedPdf->getOrderIncrementId().'-'.$requestedPdf->getOrderItemId().'.'.$this->determinFileType($pdfUrl);
                        $fileDir = Mage::helper('web2print')->getPDFSavePath($requestedPdf->getExportType(),$website);
                       
                        $savePath = $fileDir . $filename;
                        $this->createDirectories($fileDir);
                       
                        $downloadFileResponse = $this->downloadFile($pdfUrl, $savePath); 
                        
                        if($downloadFileResponse == 1) {
                            $requestedPdf->setPath($savePath);
                            $requestedPdf->setStatus('completed');
                            $requestedPdf->save();
                            
                            //dispatching event
                            Mage::dispatchEvent('web2print_pdf_save_after',array('pdf'=>$requestedPdf));
                        } else {
                            $requestedPdf->setStatus('download-error');
                        }
                    } elseif($taskErrorMessage == '' && $taskXml['started'] == 'True' && $taskXml['finished'] == 'False') {
                        $requestedPdf->setStatus('running');
                    } elseif($taskErrorMessage == '' && $taskXml['started'] != 'True') {
                        $requestedPdf->setStatus('queued-chili');
                        $requestedPdf->setUpdatedAt(date('Y-m-d H:i:s'));
                    } else {
                        $requestedPdf->setStatus('task-error');
                        $requestedPdf->setMessage($taskErrorMessage);
                        $requestedPdf->setUpdatedAt(date('Y-m-d H:i:s'));
                    }
                    $requestedPdf->save();
                }
            }
       
        }catch(Exception $e){
            Mage::log($e->getMessage());
        }
    }

    /**
     * Download files from remote server
     */
    public function downloadFile($src, $dest) {

        $fp = fopen($dest, 'w');
        
        $ch = curl_init($src);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        $data = curl_exec($ch);
        
        curl_close($ch);
        fclose($fp);
                
        return $data;
    }
        
    
    /**
     * check if pdf file exists
     */
    public function checkIfFileExists($path){
        $exists = false;
        
        if(file_exists($path)){
            $exists = true; 
        }

        return $exists;
    } 
    
    /**
     * Create directories if these not exists
     */
    public function createDirectories($fileDir){
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $fileDir));    
    }
    
    /**
     * Compress file
     */
    public function compressFile($archive, $usedFiles, $tmpZipPath){
        $filter = new Zend_Filter_Compress(array(
            'adapter' => 'Zip',
            'options' => array(
                'archive' => $archive,
             ),
         ));
        $compressed = $filter->filter($tmpZipPath);        
        
        foreach($usedFiles as $file){
            unlink($file);
        }
        
        rmdir($tmpZipPath);
    }
    
    /**
     * Determin the file type based on the download URL extension
     */
    public function determinFileType($url) {
        $fileType = 'pdf'; // Default
        $urlExtension = substr($url, -3);
        
        if($urlExtension == "zip") {
            $fileType = "zip";
        }
        
        return $fileType;
    }
}

