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

class MageParts_Ddq_Model_Observer_Adminhtml extends MageParts_Base_Model_Observer_Adminhtml
{

    /**
     * After applying changes to the configuration we should reload all cached data
     * associated with the extension, and perhaps also perform some other actions.
     *
     * @param Varien_Event_Observer $observer
     */
    public function changedCfg(Varien_Event_Observer $observer)
    {
        parent::changedCfg($observer);
        Mage::helper('ddq/cache')->clean('all');
    }

    /**
     * Adds DDQ HTML to inventory tab (where editing products). Could potentially also
     * be used for other things in the future.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAbstractHtml(Varien_Event_Observer $observer)
    {
        try {
            /* @var $block Mage_Core_Block_Abstract */
            $block = $observer->getEvent()->getBlock();

            $blockClass = get_class($block);

            if ($this->_getControllerName() == 'catalog_product' && ($this->_getActionName() == 'edit' || $this->_getActionName() == 'new')) {
                if ($blockClass == 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory' || $blockClass == 'Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogProductEditTabInventory') {
                    // adds our customised block to the inventory tab
                    $observer->getTransport()->setHtml($observer->getTransport()->getHtml() . Mage::getBlockSingleton('ddq/adminhtml_catalog_product_form')->toHtml());
                }
            }
        } catch (Exception $e) {
            Mage::helper('ddq')->log($e->getMessage());
        }
    }

    /**
     * Add / remove layout handles etc. before the layout loads.
     *
     * @param Varien_Event_Observer $observer
     */
    public function modifyLayout(Varien_Event_Observer $observer)
    {
        if ($this->_getControllerName() == 'system_config') {
            $section = Mage::app()->getRequest()->getParam('section');

            if ($section == 'ddq') {
                $observer->getLayout()->getUpdate()->addHandle('adminhtml_system_config_edit_section_ddq');
            }
        }
    }

    /**
     * Modify DDQ data before a product is saved.
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeProductSave(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            try {
                $product = $observer->getProduct();

                if ($product) {
                    // work out which values should be removed based on use config / default value checkboxes
                    $useConfig = Mage::app()->getRequest()->getParam('ddq_use_config');

                    // reset values that have been set to use config / default values
                    if (is_array($useConfig) && count($useConfig)) {
                        foreach ($useConfig as $key => $data) {
                            if ($data === 'on') {
                                if ($product->hasData($key)) {
                                    $product->setData($key, null);
                                }
                            }
                        }
                    }

                    // clean up the quantity options table array (the keys are submitted as 0, 1, 2 etc., they should be 1.0000, 2.000....)
                    if ($product->hasData('ddq_qty_list')) {
                        $list = $product->getData('ddq_qty_list');

                        if (!empty($list)) {
                            $product->setData('ddq_qty_list', Mage::helper('ddq')->cleanQuantityData($list));
                        }
                    }
                }
            } catch (Exception $e) {
                Mage::helper('ddq')->log($e->getMessage());
            }
        }
    }

    /**
     * For some reason when updating attributes for multiple products
     * Magento seems to ignore the backend model to some degree. It
     * does not seem to be applied for our attribute ddq_qty_list,
     * which needs to be serialized before it's saved. Hence we have
     * to go around that and we do so by including this function. It's
     * only purpose is to serialize the value of the ddq_qty_list
     * attribute before the attribute is saved (when updating it from
     * adminhtml_catalog_product_action_attribute_edit).
     *
     * @param Varien_Event_Observer $observer
     */
    public function serializeDdqAttributeData(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            // debug log
            Mage::helper('ddq')->log('Executing event observer method serializeDdqAttributeData');

            // retrieve data from post
            $data = Mage::app()->getRequest()->getParam('attributes');

            // modify data
            if (is_array($data) && isset($data['ddq_qty_list']) && is_array($data['ddq_qty_list'])) {
                $data['ddq_qty_list'] = serialize(Mage::helper('ddq')->cleanQuantityData($data['ddq_qty_list']));
            }

            // relay back modified data
            Mage::app()->getRequest()->setParam('attributes', $data);
        }
    }

}
