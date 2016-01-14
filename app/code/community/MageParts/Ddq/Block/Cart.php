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

class MageParts_Ddq_Block_Cart extends Mage_Core_Block_Template
{

    /**
     * Product list record registry key name.
     */
    const CART_ITEM_RECORD_REGISTRY_KEY = 'mp_ddq_cart_item_record';

    /**
     * Retrieve concatenation of ddq/ddq block outputs for products currently
     * being listed on the rendered page.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $result = '';

        if (Mage::helper('ddq')->isEnabled()) {
            $result.= parent::_toHtml();

            $quote = Mage::getSingleton('checkout/session')->getQuote();

            if ($quote) {
                $items = $quote->getAllVisibleItems();

                if (count($items)) {
                    foreach ($items as $item) {
                        if (!$item->getParentItem()) {
                            if ($item && $item->getId() && $item->getProduct()->getDdqEnabled()) {
                                // this is a work around, we need the product record to be available to us
                                // in the ddq/ddq blocks constructor, and unfortunately this is currently
                                // the best way of achieving that.
                                Mage::unregister(self::CART_ITEM_RECORD_REGISTRY_KEY);
                                Mage::register(self::CART_ITEM_RECORD_REGISTRY_KEY, $item);

                                // create and render ddq/ddq block for the product record.
                                $block = Mage::app()->getLayout()->createBlock('ddq/ddq', ('mp.ddq.cart.item.' . time()));

                                if ($block) {
                                    $result.= $block->toHtml();
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

}
