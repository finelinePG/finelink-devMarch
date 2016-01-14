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

class MageParts_Ddq_Block_Ddq extends Mage_Core_Block_Template
{

    /**
     * Internal memory for quantity options array.
     *
     * @var array
     */
    protected $_qtyOptions = null;

    /**
     * Products used for this instance.
     *
     * @var array
     */
    protected $_products;

    /**
     * Placeholder for unknown option data.
     *
     * @var string
     */
    protected $_optionUnknownDataPlaceHolder = '?';

    /**
     * Internal memory for preselected quantity.
     *
     * @var float
     */
    protected $_preselected;

    /**
     * Internal memory location for config json object, to avoid generating
     * it multiple times under certain circumstances.
     *
     * @var array
     */
    protected $_jsConfig;

    /**
     * Whether or not we are on the cart configure page.
     *
     * @var boolean
     */
    protected $_isConfigureCartItemAction;

    /**
     * This will initiate the block which contains all functions and
     * attachments of the ddq block on the frontend. It will select
     * a template based on the product type and display the quantity
     * option panel.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('mageparts/ddq/catalog/product/type/default.phtml');

        /* @var $cache MageParts_Ddq_Helper_Cache */
        $cache = Mage::helper('ddq/cache');

        if ($cache->isEnabled()) {
            /* @var Mage_Sales_Model_Quote_Item $item */
            $item = null;
            $itemHash = null;
            $cacheCartItems = Mage::getStoreConfigFlag('ddq/cache/cache_cart_items');
            $isConfigureAction = $this->isCartConfigureItemAction();

            if ($this->_getHelper()->isInCart()) {
                $ddqId = null;

                if ($isConfigureAction) {
                    $id = (int) $this->getRequest()->getParam('id');

                    if ($id) {
                        $item = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($id);
                    }

                    $ddqId = $item->getProduct()->getId();
                }

                if (!$item) {
                    $item = Mage::registry(MageParts_Ddq_Block_Cart::CART_ITEM_RECORD_REGISTRY_KEY);
                }

                if ($item) {
                    $itemHash = Mage::helper('ddq')->getCartItemOptionHash($item, array('info_buyRequest', 'ddq_identifier'));

                    $itemProduct = !$isConfigureAction ? $this->_getHelper()->getSimpleProductFromItemOptions($item) : $item->getProduct();

                    if (!$itemProduct || !$itemProduct->getId()) {
                        $itemProduct = $item->getProduct();
                    }

                    if ($isConfigureAction) {
                        $itemProduct->load($itemProduct->getId());
                    }

                    if ((bool) $itemProduct->getDdqEnabled()) {
                        //if ((bool) $itemProduct->getDdqEnabled()) {
                        if (!$isConfigureAction) {
                            $ddqId = $item->getId();
                            $itemProduct->setDdqLayout('select');

                            // we memorize clean versions of the quantity lists we modify below using the
                            // correctPriceCalculationsByCartItem function. This is necessary because the
                            // lists are saved on the product which is tied to the item, which is
                            // apparently shared between subsequent items based on the same product,
                            // which means that price calculations will otherwise aggregate.
                            $cleanQtyLists = Mage::registry('mp_ddq_cart_clean_qty_lists');

                            if (!$cleanQtyLists) {
                                $cleanQtyLists = array();
                            }

                            if (!isset($cleanQtyLists[$itemProduct->getId()])) {
                                $cleanQtyLists[$itemProduct->getId()] = $itemProduct->getDdqQtyList();
                            }

                            Mage::unregister('mp_ddq_cart_clean_qty_lists');
                            Mage::register('mp_ddq_cart_clean_qty_lists', $cleanQtyLists);

                            // correct price calculations for cart item quantity list (to account for
                            // option based prices).
                            $itemProduct->setDdqQtyList($this->_getHelper()->correctPriceCalculationsByCartItem((isset($cleanQtyLists[$itemProduct->getId()]) ? $cleanQtyLists[$itemProduct->getId()] : $itemProduct->getDdqQtyList()), $item));
                        }

                        if (!$isConfigureAction || $itemProduct->getTypeId() !== 'configurable') {
                            $itemProduct->setDdqPreselected($item->getQty());
                        }

                        $this->setData('product', $itemProduct->setDdqId($ddqId));
                    }

                    $this->setData('cart_item', $item);
                }
            } else if (!$this->hasData('product')) {
                $product = Mage::registry('product');

                if (!is_object($product) || !$product->getId()) {
                    if ($this->_getHelper()->isOnProductDetailPage()) {
                        $id = (int) Mage::app()->getRequest()->getParam('id');

                        if ($id) {
                            $product = Mage::getModel('catalog/product')->load($id);
                        }
                    }

                    if (!is_object($product) || !$product->getId()) {
                        $product = Mage::registry(MageParts_Ddq_Helper_Data::CUSTOM_PRODUCT_RECORD_REGISTRY_KEY);
                    }
                }

                $this->setData('product', $product);
            }

            if ($this->getProduct() && $this->getProduct()->getId() && !$isConfigureAction && (!$this->_getHelper()->isInCart(false) || $cacheCartItems)) {
                // append cache info to block
                if ($cache->isCacheEnabled()) {
                    $cacheKeyPieces = array(
                        Mage_Catalog_Model_Product::CACHE_TAG,
                        $this->getProduct()->getId(),
                        'ddq',
                        $this->getProduct()->getDdqPreselected(),
                        $this->getProduct()->getDdqLayout()
                    );

                    if ($this->_getHelper()->isInCart(false) && !empty($itemHash)) {
                        $cacheKeyPieces['item_hash'] = $itemHash;
                    }

                    $this->addData(array(
                        'cache_lifetime' => $cache->_getConfigLifetime(),
                        'cache_tags'     => $cache->getProductTags($this->getProduct()),
                        'cache_key'      => $cache->generateCacheKey($cacheKeyPieces)
                    ));
                }
            }
        }
    }

    /**
     * Check whether or not we are on the cart item configure page.
     *
     * @return boolean
     */
    public function isCartConfigureItemAction()
    {
        if (is_null($this->_isConfigureCartItemAction)) {
            $this->_isConfigureCartItemAction = (Mage::helper('ddq')->getUrlHelper()->getMca() == 'checkout/cart/configure');
        }
        return $this->_isConfigureCartItemAction;
    }

    /**
     * Retrieve JSON configuration object.
     *
     * @param boolean $jsonEncodeResult
     * @return string
     */
    public function getJsConfig($jsonEncodeResult=true)
    {
        if (is_null($this->_jsConfig)) {
            $result = array();

            if ($this->_getHelper()->isEnabled() && $this->getProduct() && $this->getProduct()->getDdqEnabled()) {
                // data collected for result
                $preselectedArr = array();
                $qtyListArr     = array();
                $layoutArr      = array();
                $enabledArr     = array();
                $idArr          = array();

                // used later for comparison checks with configurable products
                $defaultQtyList = null;

                // simple product tied to parent product in cart
                $simpleProduct = null;

                if (is_null($this->_products)) {
                    if ($this->getProduct()->getTypeId() == 'grouped') {
                        $this->_products = $this->getProduct()->getTypeInstance(true)->getAssociatedProducts($this->getProduct());
                    } else if ($this->getProduct()->getTypeId() == 'configurable') {
                        $this->_products[] = $this->getProduct();

                        $useAjax = Mage::getStoreConfigFlag('ddq/ajax/enabled');

                        if ($this->isCartConfigureItemAction() && $this->getCartItem()) {
                            $simpleProduct = $this->_getHelper()->getSimpleProductFromItemOptions($this->getCartItem());

                            if ($simpleProduct && $simpleProduct->getId()) {
                                $simpleProduct->setDdqPreselected($this->getCartItem()->getQty());
                            }

                            if ($useAjax) {
                                $this->_products[] = $simpleProduct;
                            }
                        }

                        if (!$useAjax) {
                            $this->_products = array_merge($this->_products, Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $this->getProduct()));
                        }
                    } else {
                        $this->_products = array($this->getProduct());
                    }
                }

                $avoidDuplicateProducts = array();

                if (count($this->_products)) {
                    foreach ($this->_products as $key => &$product) {
                        // avoid duplicate products
                        if (in_array($product->getId(), $avoidDuplicateProducts)) {
                            unset($this->_products[$key]);
                            continue;
                        }

                        $avoidDuplicateProducts[] = $product->getId();

                        // ddqId is either product or item id and is only used as a unique identifier, never as a database reference
                        $id = $product->getDdqId() ? $product->getDdqId() : $product->getId();

                        // retrieve ddq data for this product record
                        $list = $product->getDdqQtyList();

                        if (is_array($list) && count($list)) {
                            foreach ($list as $k => $v) {
                                $list[$k] = is_array($list[$k]) && isset($list[$k]['p']) ? $list[$k]['p'] : array();
                            }
                        } else {
                            $product->setDdqEnabled(false);
                        }

                        // if this is a simple product which is a child of a configurable one check if there settings are equal, and if so skip rendering the simple product element (fallback to configurable).
                        if ($this->getProduct()->getTypeId() == 'configurable' && $product->getTypeId() != 'configurable') {
                            if ($this->isCartConfigureItemAction() && !is_null($simpleProduct) && $simpleProduct->getId() && $product->getId() == $simpleProduct->getId()) {
                                $product->setDdqPreselected($simpleProduct->getDdqPreselected());
                            }

                            if ($this->_getHelper()->ddqIsEqual($this->getProduct(), $product)) {
                                // completely skip rendering
                                unset($this->_products[$key]);
                            } else {
                                if (!$this->_getHelper()->ddqIsEqual($this->getProduct(), $product, array('ddq_enabled'))) {
                                    $enabledArr[$id] = (bool) $product->getDdqEnabled();
                                }

                                if (!$this->_getHelper()->ddqIsEqual($this->getProduct(), $product, array('ddq_layout'))) {
                                    $layoutArr[$id] = $product->getDdqLayout();
                                }

                                if (!$this->_getHelper()->ddqIsEqual($this->getProduct(), $product, array('ddq_preselected'))) {
                                    $preselectedArr[$id] = $this->getCorrectPreselectionValue($list, $product->getDdqPreselected());
                                }

                                if (!$this->_getHelper()->ddqIsEqual($this->getProduct(), $product, array('ddq_qty_list'))) {
                                    $qtyListArr[$id] = $list;
                                }

                                if (isset($layoutArr[$id]) || isset($preselectedArr[$id]) || isset($qtyListArr[$id]) || isset($enabledArr[$id])) {
                                    $idArr[] = $id;
                                }

                                // skip rendering if the produced HTML would be the same as the parent product's
                                if (!$product->getDdqEnabled() || $this->_getHelper()->ddqIsEqual($this->getProduct(), $product, null, false, true)) {
                                    unset($this->_products[$key]);
                                }
                            }
                        } else {
                            if (!(bool) $product->getDdqEnabled()) {
                                if ($product->getTypeId() == 'configurable') {
                                    $enabledArr[$product->getId()] = false;
                                }

                                unset($this->_products[$key]);
                                continue;
                            } else {
                                // correct preselection value
                                $product->setDdqPreselected($this->getCorrectPreselectionValue($list, $product->getDdqPreselected()));

                                // assign ddq config data
                                $layoutArr[$id]  = $product->getDdqLayout();
                                $qtyListArr[$id] = $list;
                                $idArr[]         = $id;

                                if ($product->getTypeId() == 'configurable') {
                                    $enabledArr[$product->getId()] = $product->getDdqEnabled();
                                    $preselectedArr[$product->getId()] = $product->getDdqPreselected();
                                }
                            }
                        }
                    }
                }

                $result = array(
                    'qtyOptions'        => $qtyListArr,
                    'layouts'           => $layoutArr,
                    'productIds'        => $idArr,
                    'hasProductOptions' => $this->getProduct()->hasOptions(),
                    'productType'       => $this->getProduct()->getTypeId(),
                    'defaultId'         => $this->getProduct()->getId()
                );

                if ($this->getProduct()->getTypeId() == 'configurable') {
                    $result['preselected'] = $preselectedArr;
                    $result['enabled'] = $enabledArr;
                }
            }

            $this->_jsConfig = $result;
        }

        return $jsonEncodeResult ? Zend_Json::encode($this->_jsConfig) : $this->_jsConfig;
    }

    /**
     * Retrieve corrected preselection value.
     *
     * The extension attempts to correct this value during the product
     * loading sequence, however we need to run an additional check
     * check in case options have been removed due to the
     * "Hide Unavailable Quantity Options" setting. If that has been
     * invoked and there are no options left at all, and the
     * "No Options Actions" setting is configured as "single_option"
     * this means the preselection value will be wrong and it will
     * cause problems with the JavaScript.
     *
     * @param array $list
     * @param float $value
     * @return float
     */
    public function getCorrectPreselectionValue($list=null, $value)
    {
        $result = $value;

        if (is_array($list) && count($list)) {
            if (!isset($list[$this->_getHelper()->getQtyKey($value)])) {
                reset($list);
                $result = (float) key($list);
            }
        }

        return $result;
    }

    /**
     * Retrieve array of products used for this instance.
     *
     * @return array
     */
    public function _getProducts()
    {
        return $this->_products;
    }

    /**
     * Retrieve table row HTML.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $option
     * @return string
     */
    public function getTableRow(Mage_Catalog_Model_Product $product, array $option)
    {
        $result = '';

        $cols = Mage::helper('ddq')->getLayoutTableColumns();

        if (is_array($option) && count($option) && count($cols)) {
            $result.= '<tr>';

            $preselected = $product && $product->getId() ? $product->getDdqPreselected() : 0;

            foreach ($cols as $col) {
                $result.= '<td>';

                switch ($col['type']) {
                    case 'radio':
                        $result.= '<input type="radio" value="' . $option['qty'] . '"' . (($preselected > 0 && $preselected == $option['qty']) ? ' checked=true' : '') . ' disabled />';
                        break;
                    case 'qty':
                        $result.= isset($option['qty']) ? $option['qty'] : $this->_optionUnknownDataPlaceHolder;
                        break;
                    case 'label':
                        $result.= Mage::helper('ddq')->getOptionLabel($option, $product);
                        break;
                    case 'stock_status':
                        $result.= $this->_getHelper()->getStockStatus($this->getProduct(), $option['qty']);
                        break;
                    case 'price':
                        $result.= isset($option['p']) && isset($option['p']['p']) ? Mage::helper('core')->formatPrice($option['p']['p'], false) : $this->_optionUnknownDataPlaceHolder;
                        break;
                    case 'price_old':
                        $result.= isset($option['p']) && isset($option['p']['o']) ? Mage::helper('core')->formatPrice($option['p']['o'], false) : ((isset($option['p']) && isset($option['p']['p'])) ? Mage::helper('core')->formatPrice($option['p']['p'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                    case 'price_excl_tax':
                        $result.= (isset($option['p']) && isset($option['p']['e'])) ? Mage::helper('core')->formatPrice($option['p']['e'], false) : ((isset($option['p']) && isset($option['p']['p'])) ? Mage::helper('core')->formatPrice($option['p']['p'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                    case 'price_incl_tax':
                        $result.= (isset($option['p']) && isset($option['p']['i'])) ? Mage::helper('core')->formatPrice($option['p']['i'], false) : ((isset($option['p']) && isset($option['p']['p'])) ? Mage::helper('core')->formatPrice($option['p']['p'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                    case 'price_unit':
                        $result.= isset($option['p']) && isset($option['p']['pu']) ? Mage::helper('core')->formatPrice($option['p']['pu'], false) : $this->_optionUnknownDataPlaceHolder;
                        break;
                    case 'price_unit_old':
                        $result.= isset($option['p']) && isset($option['p']['ou']) ? Mage::helper('core')->formatPrice($option['p']['ou'], false) : ((isset($option['p']) && isset($option['p']['pu'])) ? Mage::helper('core')->formatPrice($option['p']['pu'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                    case 'price_unit_excl_tax':
                        $result.= (isset($option['p']) && isset($option['p']['eu'])) ? Mage::helper('core')->formatPrice($option['p']['eu'], false) : ((isset($option['p']) && isset($option['p']['pu'])) ? Mage::helper('core')->formatPrice($option['p']['pu'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                    case 'price_unit_incl_tax':
                        $result.= (isset($option['p']) && isset($option['p']['iu'])) ? Mage::helper('core')->formatPrice($option['p']['iu'], false) : ((isset($option['p']) && isset($option['p']['pu'])) ? Mage::helper('core')->formatPrice($option['p']['pu'], false) : $this->_optionUnknownDataPlaceHolder);
                        break;
                }

                $result.= '</td>';
            }
        }

        return $result . '</tr>';
    }

    /**
     * Retrieve product object instance.
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');

        if (!$product || !$product->getId()) {
            $product = parent::getProduct();
        }

        return $product;
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

    /**
     * Checks whether or not we should override the initial CSS from the General
     * block used to hide the quantity text box. This would be the case if we
     * are viewing a product where the extension is disabled.
     *
     * @return boolean
     */
    public function overrideHiddenCssRules()
    {
        return !(bool) $this->getProduct()->getDdqEnabled() && !in_array($this->getProduct()->getTypeId(), array('grouped'));
    }

}
