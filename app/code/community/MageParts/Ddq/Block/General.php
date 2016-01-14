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

class MageParts_Ddq_Block_General extends Mage_Core_Block_Template
{

    /**
     * This will initiate the block which contains all functions and
     * attachments of the ddq block on the frontend. It will select
     * a template based on the product type and display the quantity
     * option panel.
     */
    protected function _construct()
    {
        parent::_construct();

        /* @var $cache MageParts_Ddq_Helper_Cache */
        $cache = Mage::helper('ddq/cache');

        if ($cache->isEnabled() && $cache->isCacheEnabled()) {
            $this->addData(array(
                'cache_lifetime' => $cache->_getConfigLifetime(),
                'cache_tags'     => $cache->getTags(),
                'cache_key'      => $cache->generateCacheKey(array(
                    'ddq_general',
                    $cache->getStoreId(),
                    $this->_getView()
                ), false)
            ));
        }
    }

    /**
     * Retrieve which is the active view type on frontend.
     *
     * @return string
     */
    public function _getView()
    {
        $result = 'product';

        if (Mage::helper('ddq')->isCollectionInjectionUrl()) {
            $result = 'list';
        } else if (Mage::helper('ddq')->isInCart(false)) {
            $result = 'cart';
        }

        return $result;
    }

    /**
     * Retrieve list of paths which should be hidden using CSS.
     *
     * @param boolean $qtyBoxPathsOnly
     * @return string
     */
    public function getCssRemovalPaths($qtyBoxPathsOnly=false)
    {
        $result = '';

        if (Mage::getStoreConfigFlag('ddq/layout/hide_qty_boxes')) {
            $paths = Mage::helper('ddq')->getQtyBoxElPaths(true, false);

            if (is_array($paths) && count($paths)) {
                $result = implode(',', array_unique($paths));
            }
        }

        if (!$qtyBoxPathsOnly && Mage::getStoreConfigFlag('ddq/layout/hide_tier_price_box')) {
            $tierPricePath = Mage::getStoreConfig('ddq/element_paths/tier_price_box');

            if (!empty($tierPricePath)) {
                $result.= !empty($result) ? ', ' . $tierPricePath : $tierPricePath;
            }
        }

        return $result;
    }
    
}
