<?php

class Chili_Web2print_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default
{
 
    /* Returns string - download link
    *  Function that provides the order view page with a pdf download button
    */

    public function getDocumentPdfStatus($documentId, $exportType){    
        if($documentId){
            $pdfCollection = Mage::getModel('web2print/pdf')->getCollection()->addFieldToFilter('document_id', $documentId)->addFieldToFilter('export_type', $exportType)->addFieldToFilter('order_item_id', $this->getItem()->getId());
            $returnArray = array('downloadLink' => null, 'pdf' => null);
//            Zend_Debug::dump($pdfCollection);exit;
            if(count($pdfCollection)){
                $pdfModel = $pdfCollection->getFirstItem();
                $returnArray['pdf'] = $pdfModel;
                if($pdfModel->getPath()){
                    if($pdfModel->checkIfFileExists($pdfModel->getPath())){
                        $returnArray['downloadLink'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$pdfModel->getPath();
                    }
                }
            }
            
            return $returnArray;
        }
//        try{
//            if($documentId){ 
//                //get pdf collection and filter
////                $pdfCollection = Mage::getModel('web2print/pdf')->getCollection()->addFieldToFilter('document_id', $documentId)->addFieldToFilter('export_type', $exportType)->addFieldToFilter('order_item_id', $this->getItem()->getId());
//                $pdfModel = null;
//
//                foreach($pdfCollection as $pdf){
//                    $pdfModel = $pdf;
//                    break;
//                }
//                if($pdfModel){
//                    if($pdfModel->getPath()){
//                        //check if pdf is reachable                     
//                        if($pdfModel->checkIfFileExists($pdfModel->getPath())){
//                            return '<a title="created at '.$pdfModel->getUpdatedAt().'" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$pdfModel->getPath().'" target="_blank" class="scalable" type="button"><span>'.Mage::helper('web2print')->__('Download pdf').'</span></a><br/>                            
//                            '.Mage::helper('web2print')->__('Export profile').'<br/><b>'.$pdfModel->getExportProfile().'</b>';
//                        }else{
//                            return Mage::helper('web2print')->__('<b>File not found</b>');
//                        } 
//                    }else{
//                        //task status ophalen en weergeven
//                        if(!$pdfModel->getUpdatedAt()):  
//                            return Mage::helper('web2print')->__('<b>%s</b><br/> at<br/> %s', $pdfModel->getStatus(), $pdfModel->getCreatedAt());
//                        else: 
//                            return Mage::helper('web2print')->__('<b>%s</b><br/> at<br/> %s', $pdfModel->getStatus(), $pdfModel->getUpdatedAt());
//                        endif;                                
//                    }
//               }else{
//                   return Mage::helper('web2print')->__('<b>No pdf record found</b>');
//               }
//            }else{
//                return Mage::helper('web2print')->__('<b>No documentid found</b>');
//            }
//        }catch(Exception $e){
//
//        }    
        return null;
    }
    
}