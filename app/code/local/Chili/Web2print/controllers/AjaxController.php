<?php
class Chili_Web2print_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * function is called by ajax and returns an image url linked to a document id.
     */
    public function imageAction()
    {
        $ajaxResult = array('status' => 'failed', 'content' => '');

        //ophalen van image via api model
        $params = $this->getRequest()->getParams();
        $documentUrl = false;

        $chiliDocumentId = $params['documentId'];
        $controllerName = $params['controller'];

        try {
            if (isset($chiliDocumentId) && isset($controllerName)) {
                $documentUrl = Mage::helper('catalog/image')->generateCachedImage($chiliDocumentId, $controllerName);
            }

            // redirect to image instead of sending ajax result
            if (isset($params['redirect'])) {
                header('Location: ' . $documentUrl);
                exit;
            }

            $ajaxResult['status'] = 'success';
            $ajaxResult['content'] = $documentUrl;

        } catch (Exception $e) {
            $ajaxResult['status'] = 'failed';
            $ajaxResult['content'] = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg');
        }

        $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
    }


    /**
     * Create an epxort PDF task in chili.
     */
    public function exportpdfAction()
    {
        // get params:
        $itemType = $this->getRequest()->getParam('itemType', 'quote');
        $itemId = $this->getRequest()->getParam('itemId', 0);
        $exportType = $this->getRequest()->getParam('exportType', 'backend');
        $itemTypeModel = null;

        if($itemType == "quote") {
            $itemTypeModel = Mage::getModel('sales/quote_item')->load($itemId);
        } else {
            $itemTypeModel = Mage::getModel('sales/order_item')->load($itemId);
        }

        // validate params:
        if (!$itemTypeModel) {
            $ajaxResult = array('result' => 'error', 'content' => '<br /><p class="error a-center">' . $this->__('The selected product could ne be found.') . '</p>');
            $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
            return;
        }

        // generate pdf
        try {
            // Get the right website in order to be able to get the right PDF export settings
            if($itemType == "quote") {
                $website = Mage::getModel('sales/quote')->load($itemTypeModel->getQuoteId())->getStore()->getWebsite();
                $documentId = Mage::helper('web2print/data')->getDocumentIdByQuoteItemId($itemId);
            } else {
                $website = Mage::getModel('sales/order')->load($itemTypeModel->getOrderId())->getStore()->getWebsite();
                $documentId = Mage::helper('web2print/data')->getDocumentIdByOrderItemId($itemId);
            }

            $exportProfile = Mage::helper('web2print')->getPdfExportProfile($exportType, $website);
            $pdfTaskId = Mage::getModel('web2print/api')->createPdfTaskForAjax($documentId, $exportProfile);

        } catch (Exception $e) {
            $pdfTaskId = false;
        }

        // set output:
        if ($pdfTaskId) {
            $ajaxResult = array(
                'status' => 'success',
                'content' => '<br /><p class="a-center">' . $this->__('The PDF is requested. Please wait while we prepare your download.') . '</p>',
                'pdfTaskId' => $pdfTaskId
            );
        } else {
            $ajaxResult = array(
                'status' => 'error',
                'content' => '<br /><p class="a-center error">' . $this->__('The PDF could not be generated. Please try again or contact us.') . '</p>',
                'pdfTaskId' => 0
            );
        }

        $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
    }

    /**
     * Check if the export PDf task is completed and return the url to the pdf if it exists.
     */
    public function exportpdfstatusAction()
    {
        // get params:
        $pdfTaskId = $this->getRequest()->getParam('pdfTaskId');
        $error = null;
        $status = null;

        if (!$pdfTaskId) {
            $ajaxResult = array('result' => 'error', 'content' => '<p class="error a-center">' . $this->__('The selected product could not be found.') . '</p>');
            $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
            return;
        }

        try {
            $taskstatus = Mage::getModel('web2print/api')->getTaskStatus($pdfTaskId);

            $taskXml = simplexml_load_string($taskstatus);
            $pdfInfo = simplexml_load_string($taskXml['result']);

            $pdfUrl = (string)$pdfInfo['url'];
            $taskErrorMessage = (string)$taskXml['errorMessage'];
            $finished = $taskXml['finished'] == 'True' ? true : false;

            if ($finished && $pdfUrl && !$taskErrorMessage) {
                $status = 'success';
            } elseif (!$finished && !$taskErrorMessage) {
                $status = 'pending';
            } else {
                $error = $this->__($taskErrorMessage);
            }

        } catch (Exception $e) {
            $error = $this->__('An error occured while creating the PDF. Please try again or contact an administrator.');
        }

        $ajaxResult = array(
            'status' => $status,
            'content' => $pdfUrl ? '<br /><p class="a-center">' . $this->__('Your file is ready: ') . '<a href="' . $pdfUrl . '">' . $this->__('Download it here') . '</a></p>' : '',
            'error' => $error ? '<br /><p class="error a-center">' . $error . '</p>' : false,
        );
        $this->getResponse()->setBody(Zend_Json::encode($ajaxResult));
    }
}


