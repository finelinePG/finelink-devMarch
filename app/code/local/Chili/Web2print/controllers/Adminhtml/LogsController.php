<?php
class Chili_Web2print_Adminhtml_LogsController extends Mage_Adminhtml_Controller_Action {
    
    protected function _initAction(){        
        $this->loadLayout()->_setActiveMenu('web2print/logs')->_addBreadcrumb(Mage::helper('adminhtml')->__("Logs"), Mage::helper('adminhtml')->__("Logs"));
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


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('web2print/logs');
    }
}
