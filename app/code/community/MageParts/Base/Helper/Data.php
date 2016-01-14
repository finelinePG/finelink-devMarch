<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Name of top module (module extending base if any).
     *
     * @var string
     */
    protected $_moduleName = 'mageparts_base';

    /**
     * Whether or not the extension is enabled.
     *
     * @var boolean
     */
    protected $_enabled;

    /**
     * Whether or not debug is enabled.
     *
     * @var boolean
     */
    protected $_debugEnabled;

    /**
     * Working store id.
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Internal memory for current hostname.
     *
     * @var string
     */
    protected $_currentHostname;

    /**
     * Internal memory for current URL.
     *
     * @var string
     */
    protected $_currentUrl;

    /**
     * Log message into extension log file
     *
     * @param string $msg
     * @param boolean $force
     * @return MageParts_Base_Helper_Data
     */
    public function log($msg, $force=false)
    {
        if ($msg instanceof Exception) {
            Mage::logException($msg);
        } else if ($this->isDebugEnabled() || $force) {
            Mage::log($msg, null, $this->getLogfileName(), true);
        }

        return $this;
    }

    /**
     * Shortcut function.
     *
     * @return array
     */
    public function getGlobalTags()
    {
        return Mage::helper($this->_getModuleName() . '/cache')->getGlobalTags();
    }

    /**
     * Return logfile name
     *
     * @return array
     */
    public function getLogfileName()
    {
        return $this->_moduleName . '.log';
    }

    /**
     * Check if extension is active or not
     *
     * @param string $extension
     * @return boolean
     */
    public function isActive($extension='')
    {
        $result = true;

        if (!empty($extension)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check if extending extension is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if(is_null($this->_enabled)) {
            $this->_enabled = (bool) Mage::getStoreConfig($this->_getModuleName() . '/general/enabled');
        }

        return $this->_enabled;
    }

    /**
     * Retrieve whether or not logging is enabled.
     *
     * @return boolean
     */
    public function isDebugEnabled()
    {
        if (is_null($this->_debugEnabled)) {
            $this->_debugEnabled = (bool) Mage::getStoreConfig($this->_getModuleName() . '/general/debug_enabled');
        }
        return $this->_debugEnabled;
    }

    /**
     * Get working store id, works for both frontend and
     * backend.
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            // get router name
            $routeName = Mage::app()->getRequest()->getRouteName();

            // check if the store parameter is available if we are in the admin area
            if ($routeName == 'adminhtml') {
                $store = Mage::app()->getRequest()->getParam('store');

                if (!is_numeric($store)) {
                    $storeModel = Mage::getModel('core/store')->load($store);

                    if ($storeModel && $storeModel->getId()) {
                        $this->_storeId = $storeModel->getId();
                    }
                } else {
                    $this->_storeId = (int) $store;
                }
            } else {
                $this->_storeId = (int) Mage::app()->getStore()->getId();
            }
        }

        return (int) $this->_storeId;
    }

    /**
     * Retrieve working store instance, or instance of requested id.
     *
     * @param $storeId int
     * @return Mage_Core_Model_Store
     */
    public function getStore($storeId=null)
    {
        $result = null;

        if (!is_null($storeId)) {
            $result = Mage::app()->getStore($storeId);
        } else {
            $result = Mage::app()->getStore($this->getStoreId());
        }

        return $result;
    }

    /**
     * Returns the name of the module extending the base
     * module. This is used to retrieve configuration values
     * etc.
     *
     * @return string
     */
    public function _getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * Retrieve client ip address.
     *
     * @return string
     */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check if a string is serialized.
     *
     * @param string $val
     * @return boolean
     */
    public function isSerialized($val)
    {
        $result = false;

        if (is_string($val) && !empty($val)) {
            $val = @unserialize($val);
            $result = !($val === false && $val != 'b:0;');
        }

        return $result;
    }

    /**
     * Retrieve currently selected currency code.
     *
     * @param int $storeId
     * @return string
     */
    public function getCurrencyCode($storeId=null)
    {
        return $this->getStore($storeId)->getCurrentCurrencyCode();
    }

    /**
     * Retrieve currency symbol.
     *
     * @param int $storeId
     * @return string
     */
    public function getCurrencySymbol($storeId=null)
    {
        return Mage::app()->getLocale()->currency($this->getCurrencyCode($storeId))->getSymbol();
    }

    /**
     * Check whether or not we are in the admin panel.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml');
    }

    /**
     * Redirect to specified url.
     *
     * @param string $url
     * @param boolean $strict
     * @return $this
     */
    public function redirect($url, $strict=false)
    {
        if (is_string($url) && !empty($url)) {
            if ($strict) {
                header('Location: ' . $url) ;
                exit();
            } else {
                Mage::app()->getResponse()->setRedirect($url);
            }
        }

        return $this;
    }



    /**
     * Removing trailing character (or string) from string.
     *
     * @param string $str
     * @param string $trail
     * @return string
     */
    public function removeStringTrail($str, $trail)
    {
        if (!empty($str) && !empty($trail)) {
            if (substr($str, (strlen($str) - strlen($trail))) == $trail) {
                $str =  substr($str, 0, (strlen($str) - strlen($trail)));
            }
        }

        return $str;
    }

    /**
     * Check if array is associative.
     *
     * @param array $v
     * @return boolean
     */
    public function isAssoc(array $v)
    {
        return array_keys($v) !== range(0, count($v) - 1);
    }

    /**
     * Retrieve customer group.
     *
     * @param int $customerId
     * @return int
     */
    public function getCustomerGroup($customerId=null)
    {
        $result = null;

        if (!is_null($customerId)) {
            $customer = Mage::getModel('customer/customer')->load($customerId);

            if ($customer && $customer->getId()) {
                $result = $customer->getGroupId();
            }
        } else {
            $result = $this->getCustomerSession()->getCustomerGroupId();
        }

        return $result;
    }

    /**
     * Retrieve id of logged in customer.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getCustomerSession()->getCustomer()->getId();
    }

    /**
     * Retrieve customer session.
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

}
