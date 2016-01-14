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

class MageParts_Ddq_Model_Observer_Global
{

    /**
     * Add default ddq data to loaded product record.
     *
     * @param Varien_Event_Observer $observer
     */
    public function addDefaultDdqValues(Varien_Event_Observer $observer)
    {
        if ($this->_getHelper()->isEnabled()) {
            // we need to assign this to a variable in order to pass it as a reference to assignDefaultProductData
            $product = $observer->getProduct();
            $this->_getHelper()->assignDefaultProductData($product);
        }
    }

    /**
     * Add default ddq data to loaded product collection.
     *
     * @param Varien_Event_Observer $observer
     */
    public function addDefaultDdqValuesToProductCollection(Varien_Event_Observer $observer)
    {
        if ($this->_getHelper()->isEnabled() && $this->_getHelper()->isEnabledForProductCollections() && !$this->_getHelper()->isInCart(false)) {
            if ($observer->getCollection() && count($observer->getCollection())) {
                foreach ($observer->getCollection() as $product) {
                    $this->_getHelper()->assignDefaultProductData($product);
                }
            }
        }
    }

    /**
     * Refresh stock related ddq cache when saving a stock item.
     *
     * @param Varien_Event_Observer $observer
     */
    public function refreshDdqCache(Varien_Event_Observer $observer)
    {
        if ($observer && $observer->getItem() && $observer->getItem()->getProduct()) {
            Mage::helper('ddq/cache')->clean(array(
                'tag' => Mage_Catalog_Model_Product::CACHE_TAG . '_' . $observer->getItem()->getProduct()->getId()
            ));
        }
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
