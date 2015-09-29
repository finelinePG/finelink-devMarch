<?php
    class Chili_Web2print_Block_Adminhtml_Pdfs_Grid_Renderer_DocumentUrl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
          public function render(Varien_Object $row)
          {
            $value =  $row->getPath();
            $pdfModel = Mage::getModel('web2print/pdf');
            
            
            if(!$value){
                $error = '<span style="color:red;">'.Mage::helper('web2print')->__('Path not found.').'</span>';
                
                $configuredPath = Mage::helper('web2print')->getPDFSavePath($row->getExportType()).$row->getDocumentId().'.pdf';
                
                if($pdfModel->checkIfFileExists($configuredPath)){
                    return '<a TARGET="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$configuredPath.'">'.$configuredPath.'</a>';
                    /*try{
                        $pdfModel->load($row->getPdfId());
                        $pdfModel->setUpdatedAt(date("Y-m-d H:i:s")); 
                        $pdfModel->setUrl($configuredPath);
                        $pdfModel->setStatus('completed');
                        $pdfModel->setExportType($row->getExportType());
                        $pdfModel->save();
                        return '<a TARGET="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$configuredPath.'">'.$configuredPath.'</a>';
                    }catch(Exception $e){
                        return Mage::helper('web2print')->__('Could not update database record.');
                    }*/
                    
                }else{
                    return $error;
                }
            }else{
               if($pdfModel->checkIfFileExists($value)){
                    return '<a TARGET="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$value.'">'.$value.'</a>';
                }else{
                    return '<span style="color:red;">'.Mage::helper('web2print')->__('Pdf not found.').'</span>';
                }  
            }
                                   
          }
    }
?>
