<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckout_Controller_Action extends Mage_Checkout_Controller_Action
{
    /**
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * @param string $step
     * 
     * @return string
     */
    protected function _getStepHtml($step)
    {
        $layout = Mage::getModel('core/layout');
        $update = $layout->getUpdate();
        $update->load('aitcheckout_checkout_' . $step);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * @return string
     */
    protected function _getMessagesHtml()
    {
        $quote = $this->_getOnepage()->getQuote();
        if ($quote->hasItems() && !$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            $this->_getOnepage()->getCheckout()->addError($error);
        }

        foreach ($quote->getMessages() as $message) {
            if ($message) {
                $this->_getOnepage()->getCheckout()->addMessage($message);
            }
        }

        $layout = $this->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session');
        return $this->getLayout()->getMessagesBlock()->getGroupedHtml();
    }

    /**
     * @param string $step
     * @param array $result
     * @param bool|array $resolve
     * 
     * @return string
     */
    protected function _extractStepOutput($step, $result = array(), $resolve = false)
    {
        $output = Mage::getSingleton('aitcheckout/save_response')->addStepResponse($step, $result);
        if ($resolve !== false && is_array($resolve)) {
            $output->addStepResponse('resolve_' . $step, $resolve);
        }

        $output = $output->toArray();
        $data = $this->getRequest()->getPost('reload_steps');
        if ($data) {
            $reloadSteps = explode(',', $data);
            foreach ($reloadSteps as $reloadStep) {
                if ($reloadStep == 'messages') {
                    $html = $this->_getMessagesHtml();
                } else {
                    $html = $this->_getStepHtml($reloadStep);
                }
                if ($reloadStep == 'review') {
                    $html .= Mage::getModel('core/layout')->createBlock('aitcheckout/checkout_paypal_iframe')->toHtml();
                }

                $output['update_section'][$reloadStep] = array('name' => $reloadStep, 'html' => $html);
            }
        }
        return $output;
    }
}