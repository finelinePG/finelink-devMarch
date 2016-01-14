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
 * @package    MageParts_Ddq
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Ddq_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Name of get param keeping configurable product id.
     */
    const GET_PARAM_CONFIGURABLE_ID = 'mp_ddq_configurable_id';

    /**
     * Name of get param keeping simple product id.
     */
    const GET_PARAM_SIMPLE_ID = 'mp_ddq_simple_id';

    /**
     * Actions to be dispatched before running this controller.
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // extension is enabled
        if (!Mage::getStoreConfigFlag('ddq/general/enabled')) {
            $this->_getHelper()->log("The extension is not enabled.");
            $this->norouteAction();
        }
    }

    /**
     * AJAX response from requesting ddq data of simple product associated with configurable.
     *
     * @return MageParts_Ddq_Helper_Data
     */
    public function fetchForConfigurableAction()
    {
        $result = null;
        $cacheKey = '';

        // collect requested product ids
        $configurableId = Mage::app()->getRequest()->getParam(self::GET_PARAM_CONFIGURABLE_ID);
        $simpleId = Mage::app()->getRequest()->getParam(self::GET_PARAM_SIMPLE_ID);

        /* @var $cache MageParts_Ddq_Helper_Cache */
        $cache = Mage::helper('ddq/cache');

        // attempt to load cached response
        if ($cache->isCacheEnabled()) {
            $cacheKeyPieces = array(
                'ddq_ajax_data',
                $configurableId,
                $simpleId
            );

            $cacheKey = $cache->generateCacheKey($cacheKeyPieces);
            $result = $cache->load($cacheKey);
        }

        if (!$result) {
            $result = array(
                'config' => array(),
                'html' => ''
            );

            // load configurable product
            $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);

            // this registry entry will be used when loading the simple product by the assignDefaultData function (check the helper and observers). it's required for the data's fallback behavior.
            Mage::register(MageParts_Ddq_Helper_Data::REG_KEY_AJAX_CONFIGURABLE_PRODUCT, $configurableProduct);

            // load the simple product
            $simpleProduct = Mage::getModel('catalog/product')->load($simpleId);

            // we always want to include this parameter in order to accurately display the fallback text box which is standard in Magento
            $result['config']['enabled'] = (bool) $simpleProduct->getDdqEnabled();

            if ($configurableProduct && $configurableProduct->getId() && $configurableProduct->getDdqEnabled() && $simpleProduct && $simpleProduct->getId() && $simpleProduct->getDdqEnabled()) {
                // if the simple products ddq config is exactly same as the configurable products we do not need to return any data at all since fallback values will anyways be used and have already been rendered.
                if (!$this->_getHelper()->ddqIsEqual($configurableProduct, $simpleProduct)) {
                    // retrieve ddq config for the simple product record.
                    $list = $simpleProduct->getDdqQtyList();

                    if (is_array($list) && count($list)) {
                        foreach ($list as $k => $v) {
                            $list[$k] = is_array($list[$k]) && isset($list[$k]['p']) ? $list[$k]['p'] : array();
                        }
                    }

                    $result['config']['preselected'] = $simpleProduct->getDdqPreselected();
                    $result['config']['layout'] = $simpleProduct->getDdqLayout();
                    $result['config']['qtyOptions'] = $list;
                    
                    if (!$this->_getHelper()->ddqIsEqual($configurableProduct, $simpleProduct, null, false, true)) {
                        // this is a work around, we need the product record to be available to us
                        // in the ddq/ddq blocks constructor, and unfortunately this is currently
                        // the best way of achieving that.
                        Mage::unregister(MageParts_Ddq_Helper_Data::CUSTOM_PRODUCT_RECORD_REGISTRY_KEY);
                        Mage::register(MageParts_Ddq_Helper_Data::CUSTOM_PRODUCT_RECORD_REGISTRY_KEY, $simpleProduct);

                        // create and render ddq/ddq block for the product record.
                        $block = Mage::app()->getLayout()->createBlock('ddq/ddq', ('mp.ddq.product.' . $simpleProduct->getId()));

                        $result['html'] = $block->setAvoidJsInstance(true)->toHtml();
                    }

                }
            }

            $result = Zend_Json::encode($result);

            // store the output of this function in the cache for later use
            if ($cache->isCacheEnabled() && !empty($cacheKey)) {
                $cache->save($result, $cacheKey, $cache->getProductTags($configurableProduct));
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($result);
    }

    /**
     * Retrieve helper object.
     *
     * @return MageParts_Ddq_Helper_Data
     */
    public function _getHelper()
    {
        return Mage::helper('ddq');
    }

}
