<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

class Aitoc_Aitcheckout_Model_Rewrite_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    
    public function getQuote()
    {
        $quote = parent::getQuote();
        $action = Mage::app()->getRequest()->getActionName();
        if ('saveOrder' == $action)
        {   
            if (!$quote->validateMinimumAmount()) 
            {    
                $quote->setHasError(true);
            }    
        }
        return $quote;
    }
    
    public function saveCustomReview($data)
    {
        Mage::helper('aitcheckout/aitcheckoutfields')->saveCustomData($data);         
    }
    
 
}


class Aitoc_Aitcheckoutfields_Model_Rewrite_FrontCheckoutTypeOnepage extends Aitoc_Aitcheckout_Model_Rewrite_Checkout_Type_Onepage
{
    protected function _saveCustomData($data)
    {
        if ($data)
        {
            $oAttribute = Mage::getModel('aitcheckoutfields/aitcheckoutfields');

            foreach ($data as $sKey => $sVal)
            {
                $oAttribute->setCustomValue($sKey, $sVal, 'onepage');
            }
        }
        return $this;
    }

    // overwrite parent
    public function saveBilling($data, $customerAddressId)
    {
        $this->_saveCustomData($data);
        return parent::saveBilling($data, $customerAddressId);
    }

    // overwrite parent
    public function saveShipping($data, $customerAddressId)
    {
        $canSave = true;
        if ($this->getAitcheckoutfieldsHelper()->checkIfAitocAitcheckoutIsActive())
        {
            $billing = Mage::app()->getRequest()->getPost('billing', array());
            $canSave = empty($billing['use_for_shipping']);
            
        }
        $this->_saveCustomData($data);
        return ($canSave ? parent::saveShipping($data, $customerAddressId) : $this);
    }

    // overwrite parent
    public function saveShippingMethod($shippingMethod)
    {
        $oReq = Mage::app()->getFrontController()->getRequest();
        
        $data = $oReq->getPost('shippmethod');
        $this->_saveCustomData($data);
        
    /************** AITOC DELIVERY DATE COMPATIBILITY MODE: START ********************/
        
        $val = Mage::getConfig()->getNode('modules/AdjustWare_Deliverydate/active');
        if ((string)$val == 'true')
        {
            $errors = Mage::getModel('adjdeliverydate/step')->process('shippingMethod');
            if ($errors)
                return $errors;
        }
    
    /************** AITOC DELIVERY DATE COMPATIBILITY MODE: FINISH ********************/

        return parent::saveShippingMethod($shippingMethod);
    }
    
    // overwrite parent
    public function savePayment($data)
    {
        $return = parent::savePayment($data);
        $this->_saveCustomData($data);

        return $return;
    }

    // overwrite parent
    public function saveOrder()
    {
        // set review attributes data
        
        $oReq = Mage::app()->getFrontController()->getRequest();
        foreach ($oReq->getParams() as $_param)
        {
            if(is_array($_param) && Mage::helper('aitcheckoutfields')->checkIfAitocAitcheckoutIsActive())
            {
                Mage::helper('aitcheckout/aitcheckoutfields')->saveCustomData($_param); 
            }
        }
        $data = $oReq->getPost('customreview');
        $this->_saveCustomData($data);
       
        $oResult = parent::saveOrder();

        // save attribute data to DB
        
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        
        $iOrderId = $this->getCheckout()->getLastOrderId();
        
        if ($iOrderId)
        {
            $oAttribute = Mage::getModel('aitcheckoutfields/aitcheckoutfields');

            $oAttribute->saveCustomOrderData($iOrderId, 'onepage');
            $oAttribute->clearCheckoutSession('onepage');
        }
        
        Mage::dispatchEvent('aitcfm_order_save_after', array('order' => $order, 'checkoutfields' => $order->getCustomFields()));
        
        return $oResult;
    }
    
    // overwrite parent
    protected function _involveNewCustomer()
    {
        parent::_involveNewCustomer();
        
        $customerId = $this->getQuote()->getCustomer()->getId();
        Mage::getModel('aitcheckoutfields/aitcheckoutfields')->saveCustomerData($customerId, true);
    }
    
    /**
     *
     * @return Aitoc_Aitcheckoutfields_Helper_Data
     */
    public function getAitcheckoutfieldsHelper()
    {
        return Mage::helper('aitcheckoutfields');
    }
}


class Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage extends Aitoc_Aitcheckoutfields_Model_Rewrite_FrontCheckoutTypeOnepage
{
    protected $_steps = array(
        'shipping'         => false,
        'shipping_method'  => false,
        'payment'          => false
    );

	/**
     * Save data for disabled checkout steps
     * @param string $currentStep current checkout step
     * @return true
     */
    public function saveSkippedSata($currentStep)
    {
        if (!$this->_ifProcessSaving($currentStep))
        {
            return false;
        }

        switch ($currentStep)
        {
            case 'billing':
            case 'shipping':
            case 'shipping_method':

                $method = '_checkDataFor' . uc_words($currentStep, '', '_') . 'Step';
                $this->$method();

            break;

            case 'progress':

                if (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
                {
                    $this->_checkDataForPaymentStep();
                }

            break;
        }

        foreach ($this->_steps as $key => $val)
        {
            if($val)
            {
                $this->_checkStepForSave($key, false);
                $method = '_step' . uc_words($key, '', '_') . 'CustomSave';
                $this->$method();
            }
        }

        return true;
    }

    /**
     * Check if $value in steps array
     * @param string $value
     * @param bool $flag
     */
    protected function _checkStepForSave($value, $flag = true)
    {
        if (array_key_exists($value, $this->_steps))
        {
            $this->_steps[$value] = $flag;
        }
    }

    /**
     * @return array
     */
    protected function _getDisabledHash()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
    }

    /**
     * @param string $currentStep
     * @return bool
     */
    protected function _ifProcessSaving($currentStep)
    {
        $disabledHash = $this->_getDisabledHash();

        if ($disabledHash AND (in_array('shipping_method', $disabledHash) OR in_array('payment', $disabledHash) OR in_array('shipping', $disabledHash)))
        {
            $completeShiping = (bool)$this->getCheckout()->getStepData('shipping', 'complete');
            $completeShipMet = (bool)$this->getCheckout()->getStepData('shipping_method', 'complete');
            $completePayment = (bool)$this->getCheckout()->getStepData('payment', 'complete');

            if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
            {
                if ($completePayment AND $completeShipMet AND $completeShiping) return false;
            }
            elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
            {
                if ($completePayment AND $completeShipMet AND $completeShiping AND $currentStep != 'progress') return false;
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check data for billing step
     */
    protected function _checkDataForBillingStep()
    {
        $disabledHash = $this->_getDisabledHash();

        if (in_array('shipping', $disabledHash)) // no shipping info step
        {
            $this->_checkStepForSave('shipping');

            $this->_checkDataByName($disabledHash, 'shipping_method', 'payment'); // no shipping method step / no payment method step
        }
        elseif ($this->getQuote()->isVirtual())
        {
            $this->_checkDataByName($disabledHash, 'payment'); // no payment method step
        }
    }

    /**
     * Check data for shipping step
     */
    protected function _checkDataForShippingStep()
    {
        $disabledHash = $this->_getDisabledHash();

        $this->_checkDataByName($disabledHash, 'shipping_method', 'payment'); // no shipping method step / no payment method step
    }

    /**
     * Check data for shipping_method step
     */
    protected function _checkDataForShippingMethodStep()
    {
        $disabledHash = $this->_getDisabledHash();

        $this->_checkDataByName($disabledHash, 'payment'); // no payment method step
    }

    /**
     * Check data for payment step
     */
    protected function _checkDataForPaymentStep()
    {
        $disabledHash = $this->_getDisabledHash();

        $this->_checkDataByName($disabledHash, 'shipping_method'); // no shipping method step
    }

    /**
     * Check data for all steps by name
     * @param array $disabledHash
     * @param string $name
     * @param string $nameSecond
     */
    protected function _checkDataByName($disabledHash, $name, $nameSecond = '')
    {
        if (in_array($name, $disabledHash)) // no shipping method step
        {
            if(empty($nameSecond))
            {
                $this->_checkStepForSave($name);
            }
            else
            {
                $this->_checkDataByName($disabledHash, $nameSecond);
            }
        }
    }

    /**
     * Save shipping step
     */
    protected function _stepShippingCustomSave()
    {
        $data = array
        (
            'firstname' => Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE,
            'lastname'  => Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE,
        );

        $localConfig = Mage::helper('aitconfcheckout')->getStepData('shipping');

        foreach ($localConfig as $key => $value)
        {
            foreach ($value as $field)
            {
                $data[$field] = Aitoc_Aitconfcheckout_Model_Aitconfcheckout::SUBSTITUTE_CODE;
            }
        }

        if (version_compare(Mage::getVersion(), '1.4.0.0', '>=') && version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            parent::saveShipping($data, 0);
        }
        elseif (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
        {
            $this->saveShipping($data, 0);
        }

        // IF shipping step is disabled there is no way for shipping method to figure out which country to use.
        // So we set the default country from configuration->general->countries options instead of that.
        if (!Mage::getStoreConfig('aitconfcheckout/shipping/active'))
        {
            $address = $this->getQuote()->getShippingAddress();
            $billingCountry = null;
            $postData = Mage::app()->getRequest()->getPost('billing', array());

            if (isset($postData['country_id']) && $postData['country_id'])
            {
                $billingCountry = $postData['country_id'];
            }
            if (Mage::getStoreConfig('aitconfcheckout/shipping/copytoshipping') && $billingCountry)
            {
                $defaultCountry = $billingCountry;
            }
            else
            {
                $defaultCountry = Mage::helper('aitconfcheckout')->getDefaultCountryId();
            }

            $address->setCountryId($defaultCountry);
        }

        if (version_compare(Mage::getVersion(), '1.4.1.0', '>='))
        {
            $this->getCheckout()->getQuote()->load($this->getCheckout()->getQuoteId());
        }

        $this->getCheckout()->setStepData('shipping', 'complete', true);
//      ->setStepData('shipping_method', 'allow', true);
    }

    /**
     * Save shipping_method step
     */
    protected function _stepShippingMethodCustomSave()
    {
        $address = $this->getQuote()->getShippingAddress();
        $address->collectShippingRates()->save();

        $groups = $address->getGroupedAllShippingRates();

        $shippingMethod = '';

        foreach ($groups as $code => $_rates)
        {
            foreach ($_rates as $_rate)
            {
                $shippingMethod = $_rate->getCode();
            }
        }

        $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);
        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()->setStepData('shipping_method', 'complete', true);
//      ->setStepData('payment', 'allow', true);
    }

    /**
     * Save payment step
     */
    protected function _stepPaymentCustomSave()
    {
        $data = array('method' => 'checkmo');

        $payment = $this->getQuote()->getPayment();
        $payment->importData($data);

        $this->getQuote()->getShippingAddress()->setPaymentMethod($payment->getMethod());
        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()->setStepData('payment', 'complete', true);
//      ->setStepData('review', 'allow', true);
    }

    /**
     * Replace address field to AITOC_CODE
     * @param Mage_Customer_Model_Address $address
     * @param string $addressType
     * @return Mage_Customer_Model_Address
     */
    protected function _replaceCustomerAddress($address, $addressType)
    {
        $savedData = $address->getData();
        $savedData = Mage::helper('aitconfcheckout')->replaceAddressData($savedData, $addressType);

        $address->addData($savedData);
        return $address;;
    }

    /**
     * overwrite parent
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage|array
     */
    public function saveBilling($data, $customerAddressId)
    {
        if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields'))
        {
            $this->_saveCFMCustomData($data);
        }

        $helper = Mage::helper('checkout');

        if (empty($data)) {
            return array(
                'error' => -1,
                'message' => $helper->__('Invalid data.'),
            );
        }

        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId))
        {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);

            if ($customerAddress->getId())
            {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId())
                {
                    return array(
                        'error' => 1,
                        'message' => $helper->__('Customer Address is not valid.'),
                    );
                }

                $address->importCustomerAddress($this->_replaceCustomerAddress($customerAddress, 'billing'));
            }
        }
        else
        {
            // START AITOC CODE

            $data = Mage::helper('aitconfcheckout')->replaceAddressData($data,'billing');

            unset($data['address_id']);
            $address->addData($data);

            // END AITOC CODE
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true)
        {
            return array(
                'error' => 1,
                'message' => $validateRes,
            );
        }

        if (version_compare(Mage::getVersion(), '1.4.2', '>='))
        {
            if (true !== ($result = $this->_validateCustomerData($data)))
            {
                return $result;
			}
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod())
        {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId()))
            {
                return array(
                    'error' => 1,
                    'message' => $helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'),
                );
            }
        }

        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual())
        {
            /**
            * Billing address using otions
            */
            if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 0)
            {
                $shipping = $this->getQuote()->getShippingAddress();
                $shipping->setSameAsBilling(0);
            }
            if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1)
            {
                $billing = clone $address;
                $billing->unsAddressId()->unsAddressType();
                $shipping = $this->getQuote()->getShippingAddress();
                $shippingMethod = $shipping->getShippingMethod();
                $shipping->addData($billing->getData())
                    ->setSameAsBilling(1)
                    ->setShippingMethod($shippingMethod)
                    ->setCollectShippingRates(true);
                $this->getCheckout()->setStepData('shipping', 'complete', true);
            }
        }

        if (version_compare(Mage::getVersion(), '1.9.1', '<')) {
            if (true !== ($result = $this->_processValidateCustomer($address)))
            {
                return $result;
            }
        }



        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckout()
                ->setStepData('billing', 'allow', true)
                ->setStepData('billing', 'complete', true)
                ->setStepData('shipping', 'allow', true);

        // START AITOC CODE


        $this->saveSkippedSata('billing');

        if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1)
        {
            $this->saveSkippedSata('shipping');
        }

        // END AITOC CODE

        return array();
    }

    /**
     * overwrite parent
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage|array
     */
    public function saveShipping($data, $customerAddressId)
    {
        if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields'))
        {
            $this->_saveCFMCustomData($data);
        }
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid data')
            );
            return $res;
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId))
        {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId())
            {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()
                    AND !Mage::getStoreConfigFlag('adjgiftreg/general/active')) // for adj_giftreg compatibility
                {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }
                // start aitoc
                $address->importCustomerAddress($this->_replaceCustomerAddress($customerAddress, 'shipping'));
                // finish aitoc
            }
        }
        else
        {
            // start aitoc
            $data = Mage::helper('aitconfcheckout')->replaceAddressData($data, 'shipping');
            // finish aitoc

            unset($data['address_id']);
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            $res = array(
                'error' => 1,
                'message' => $validateRes
            );
            return $res;
        }

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        // start aitoc
        $this->saveSkippedSata('shipping');
        // finish aitoc
            
        return array();
    }

    /**
     * overwrite parent
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        $oResult = parent::saveShippingMethod($shippingMethod);
        
        if (!$oResult) // no errros
        {
            $this->saveSkippedSata('shipping_method');
        }

        return $oResult;
    }

    /**
     * Check Aitoc module active
     *
     * @return  bool
     */
     public function checkAitocModule()
    {
        $oConfig = Mage::getConfig();
        $sModuleFile = $oConfig->getOptions()->getEtcDir() . '/modules/Aitoc_Aitconfcheckout.xml';
        
        if (!file_exists($sModuleFile))
        {
            return false;
        }
        
        $oModuleMainConfig = simplexml_load_file($sModuleFile);
        
        $bIsActive = (bool)('true' == $oModuleMainConfig->modules->Aitoc_Aitcheckoutfields->active);
        return $bIsActive;
    }

    /**compatibility with cfm
     *
     * @param $data
     * @return Aitoc_Aitconfcheckout_Model_Rewrite_FrontCheckoutTypeOnepage
     */
    protected function _saveCFMCustomData($data)
    {
        if(method_exists($this, '_saveCustomData') )
        {
            return $this->_saveCustomData($data); //for old cfm
        }
        if ($data)
        {
            $oAttribute = Mage::getModel('aitcheckoutfields/aitcheckoutfields');

            foreach ($data as $sKey => $sVal)
            {
                $oAttribute->setCustomValue($sKey, $sVal, 'onepage');
            }
        }
        return $this;
    }
}

