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

class MageParts_Ddq_Model_Observer
{

    /**
     * Item option hash table register key.
     */
    const OPTION_HASH_TABLE_KEY = 'mp_ddq_option_hash_table';

    /**
     * Evaluate quantity of product when adding it to the shopping cart.
     *
     * @param Varien_Event_Observer $observer
     */
    public function evaluateQuantity(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            // method to be applied when an item cannot be merged with another
            $handlingMethod = Mage::getStoreConfig('ddq/cart/handle_unavailable_option');

            // array of requested items (grouped products for example adds several items at the same time)
            $items = $observer->getItems();

            if (is_array($items) && count($items)) {
                /* @var $item Mage_Sales_Model_Quote_Item */
                foreach ($items as $item) {
                    if ($item->getParentItem()) {
                        continue;
                    }

                    // requested item quantity
                    $qty = $item->getQtyToAdd() ? (float) $item->getQtyToAdd() : (float) $item->getQty();

                    if ($qty > 0) {
                        // retrieve cart item option hash as separation identification
                        $optionHash = Mage::helper('ddq')->getCartItemOptionHash($item, array('info_buyRequest', 'ddq_identifier'));

                        // retrieve stored option hash / item id pairings for comparison check
                        $optionHashTable = Mage::getSingleton('checkout/session')->getData(self::OPTION_HASH_TABLE_KEY);

                        if (!is_array($optionHashTable)) {
                            $optionHashTable = array();
                        }

                        /* @var $quote Mage_Sales_Model_Quote */
                        $quote = Mage::getSingleton('checkout/session')->getQuote();

                        // retrieve all item ids which has the same option hash as the requested on (these are effectively the ones we can merge with)
                        $itemIds = (!is_null($optionHash) && count($optionHashTable) && isset($optionHashTable[$optionHash])) ? $optionHashTable[$optionHash] : array();

                        if (Mage::helper('mageparts_base/url')->getMca() != 'checkout/cart/updateItemOptions' && $quote && count($itemIds)) {
                            // check all merge:able items, if merge:able item qty + requested qty exists in the ddq list assigned to the merge:able item then merge them
                            foreach ($itemIds as $id) {
                                $cartItem = $quote->getItemById($id);

                                if ($cartItem && $cartItem->getId()) {
                                    if ($cartItem->getProduct()) {
                                        // retrieve list of quantity options and check if the requested quantity merged with the quantity of $i is an available option
                                        $list = $cartItem->getProduct()->getDdqQtyList();

                                        if (is_null($list)) {
                                            // we need to reload the product, otherwise the quantity option settings for
                                            // the product won't be correct. This is a strange problem and the only way
                                            // to resolve it seems to tbe to reload the product.
                                            $cartItem->getProduct()->load($cartItem->getProduct()->getId());

                                            $list = $cartItem->getProduct()->getDdqQtyList();
                                        }

                                        if ($this->_getHelper()->isSerialized($list)) {
                                            $list = @unserialize($list);
                                        }

                                        if (is_array($list) && count($list)) {
                                            $key = $this->_getHelper()->getQtyKey(floatval($cartItem->getOrigData('qty')) + $qty);

                                            if (isset($list[$key]) && !$cartItem->getQtyToAdd()) {
                                                // update the quantity of the original (merge:able) item
                                                $cartItem->setQty($key)->save();

                                                // remove the requested item, otherwise we will get a double entry in the cart
                                                $quote->deleteItem($item);

                                                continue 2;
                                            } else if ($handlingMethod == 'error') {
                                                Mage::getSingleton('checkout/session')->addError(Mage::helper('ddq')->__(Mage::getStoreConfig('ddq/cart/error_message'), $cartItem->getQty(), (float) $cartItem->getOrigData('qty')));
                                                throw new Exception(Mage::helper('ddq')->__(Mage::getStoreConfig('ddq/cart/error_message'), $qty, $cartItem->getQty()));
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($handlingMethod == 'separate_entry') {
                            $optionProduct = Mage::helper('ddq')->getProductByItem($item);

                            // add custom item option to ensure the item is separated
                            $separatingOption = Mage::getModel('sales/quote_item_option')
                                ->setData(array(
                                    'code' => 'ddq_identifier',
                                    'value' => microtime(true),
                                    'product' => $optionProduct,
                                    'product_id' => $optionProduct->getId()
                                )
                            );

                            $item->addOption($separatingOption);
                        }
                    }
                }
            }
        }
    }

    /**
     * Apply overriding prices to shopping cart items.
     *
     * @param Varien_Event_Observer $observer
     */
    public function overrideItemPrices(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            // array of requested items (grouped products for example adds several items at the same time)
            $items = $observer->getQuote() ? $observer->getQuote()->getItemsCollection() : array();

            if (count($items)) {
                foreach ($items as $item) {
                    if (!$item->getParentItem()) {
                        Mage::helper('ddq')->overrideItemPrice($item);
                    } else {
                        $this->_getHelper()->addActiveItemParentProduct($item);
                    }
                }
            }
        }
    }

    /**
     * Reset prices which have been overridden in the shopping cart.
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetOverridingPrices(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            // array of requested items (grouped products for example adds several items at the same time)
            $items = $observer->getCart()->getQuote()->getAllVisibleItems();

            if (count($items)) {
                foreach ($items as $item) {
                    if (!$item->getParentItem()) {
                        $item->setCustomPrice(null)->setOriginalCustomPrice(null);
                    }
                }
            }
        }
    }

    /**
     * Reset prices which have been overridden in the shopping cart.
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetOverridingConfigurePrice(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            if ($observer->getItem() && !$observer->getItem()->getParentItem()) {
                $observer->getItem()->setCustomPrice(null)->setOriginalCustomPrice(null);
            }
        }
    }

    /**
     *
     */
    public function collectParentItemProducts(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            // array of requested items (grouped products for example adds several items at the same time)
            $items = $observer->getQuote() ? $observer->getQuote()->getItemsCollection() : array();

            if (count($items)) {
                foreach ($items as $item) {
                    if ($item->getParentItem()) {
                        $this->_getHelper()->addActiveItemParentProduct($item);
                    }
                }
            }
        }
    }

    /**
     * Add item option hash to checkout session. This is for comparison
     * checks when adding new items to the cart in order to determine
     * which item the requested one should be merged with, if any.
     *
     * @param Varien_Event_Observer $observer
     */
    public function addOptionHashToSession(Varien_Event_Observer $observer)
    {
        if (Mage::helper('ddq')->isEnabled()) {
            try {
                if ($observer->getItem() && $observer->getItem()->getId()) {
                    // retrieve cart item option hash as separation identification
                    $optionHash = Mage::helper('ddq')->getCartItemOptionHash($observer->getItem(), array('info_buyRequest', 'ddq_identifier'));

                    if (!is_null($optionHash)) {
                        // retrieve stored option hash / item id pairings for comparison check
                        $optionHashTable = Mage::getSingleton('checkout/session')->getData(self::OPTION_HASH_TABLE_KEY);

                        if (!is_array($optionHashTable)) {
                            $optionHashTable = array();
                        }

                        if (!isset($optionHashTable[$optionHash])) {
                            $optionHashTable[$optionHash] = array();
                        }

                        foreach ($optionHashTable as &$ids) {
                            if(($key = array_search($observer->getItem()->getId(), $ids)) !== false) {
                                unset($ids[$key]);
                            }
                        }

                        // add the option hash to the table
                        $optionHashTable[$optionHash][] = $observer->getItem()->getId();

                        // re-save the table in the session
                        Mage::getSingleton('checkout/session')->setData(self::OPTION_HASH_TABLE_KEY, $optionHashTable);
                    }
                }
            } catch (Exception $e) {
                Mage::helper('ddq')->log($e);
            }
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
