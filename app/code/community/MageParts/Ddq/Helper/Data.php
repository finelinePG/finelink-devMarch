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

class MageParts_Ddq_Helper_Data extends MageParts_Base_Helper_Data
{

    /**
     * Custom product record registry key name.
     */
    const CUSTOM_PRODUCT_RECORD_REGISTRY_KEY = 'mp_ddq_custom_product_record';

    /**
     * Registry key name for configurable product, to be used as parent product when assign default data to simple product during AJAX calls.
     */
    const REG_KEY_AJAX_CONFIGURABLE_PRODUCT = 'mp_ddq_ajax_configurable_product';

    /**
     * List of all simple products which have been assigned ddq data during load.
     * This list is kept to render ddq elements on product lists and in the
     * shopping cart. Since that functionality is only applied to simple products
     * we do not need to keep configurable, grouped or bundled products in mind.
     *
     * @var array
     */
    protected $_loadedProducts = array();

    /**
     * Define module name, used by various Base helper functions.
     *
     * @var string
     */
    protected $_moduleName = 'ddq';

    /**
     * Default quantity list.
     *
     * @var array
     */
    protected $_defaultQtyList = array();

    /**
     * Internal memory of layout restrictions to conserve loading time.
     *
     * @var array
     */
    protected $_layoutProductTypes = array();

    /**
     * Table layout columns.
     *
     * @var array
     */
    protected $_layoutTableColumns = null;

    /**
     * Active item products, used to correct price calculations in cart.
     *
     * @var array
     */
    protected $_activeItemParentProducts = array();

    /**
     * Retrieve whether or not the extension is enabled for product collections (product list / searches etc.).
     *
     * @param int $storeId
     * @return boolean
     */
    public function isEnabledForProductCollections($storeId=null)
    {
        return Mage::getStoreConfigFlag('ddq/general/enable_product_collection', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve default preselected quantity option from config.
     *
     * @param int $storeId
     * @return string
     */
    public function getDefaultConfigPreselected($storeId=null)
    {
        return (float) Mage::getStoreConfig('ddq/quantity_options/preselected', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve whether or not to hide unavailable qty options by default (from config).
     *
     * @param int $storeId
     * @return boolean
     */
    public function getDefaultConfigHideUnavailable($storeId=null)
    {
        return Mage::getStoreConfigFlag('ddq/quantity_options/hide_unavailable_qty', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve action to be taken if there are no available quantity options for a product.
     *
     * @param int $storeId
     * @return boolean
     */
    public function getNoOptionsAction($storeId=null)
    {
        return Mage::getStoreConfig('ddq/quantity_options/no_options_action', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve whether or not incremental quantities are enabled by default (from config).
     *
     * @param int $storeId
     * @return boolean
     */
    public function getIsIncrementalEnabled($storeId=null)
    {
        return Mage::getStoreConfigFlag('ddq/incremental/enabled', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve whether or not price updates are enabled.
     *
     * @param int $storeId
     * @return boolean
     */
    public function getPriceUpdatesEnabled($storeId=null)
    {
        return Mage::getStoreConfigFlag('ddq/general/price_updates_enabled', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Retrieve default qty options from config.
     *
     * @param int $storeId
     * @param boolean $raw Whether or not to get the unserialized value.
     * @return array
     */
    public function getDefaultConfigQtys($storeId = null, $raw = false)
    {
        $result = array();
        $storeId = is_null($storeId) ? $this->getStoreId() : $storeId;

        if (!isset($this->_defaultQtyList[$storeId])) {
            $data = array();

            $qtys = Mage::getStoreConfig('ddq/quantity_options/quantities', $storeId);

            $data['raw'] = $qtys;

            if ($this->isSerialized($qtys)) {
                $qtys = @unserialize($qtys);

                if (is_array($qtys)) {
                    $data['clean'] = $this->cleanQuantityData($qtys);
                }
            }

            $this->_defaultQtyList[$storeId] = $data;
        }

        if (isset($this->_defaultQtyList[$storeId])) {
            if (!$raw) {
                if (isset($this->_defaultQtyList[$storeId]['clean'])) {
                    $result = $this->_defaultQtyList[$storeId]['clean'];
                }
            } else if (isset($this->_defaultQtyList[$storeId]['raw'])) {
                $result = $this->_defaultQtyList[$storeId]['raw'];
            }
        }

        return $result;
    }

    /**
     * Retrieve quantity list from product record, fallback is configuration defaults.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data Quantity options array. If null, ddq_qty_list will be read from $product.
     * @param boolean $includePriceData
     * @return array
     */
    public function getQuantityListByProduct(Mage_Catalog_Model_Product $product, $data=null, $includePriceData=true)
    {
        $result = array();

        if ($product && $product->getId()) {
            if (!is_array($result) || !count($result)) {
                // retrieve quantity list from product instance
                $data = is_null($data) ? $product->getDdqQtyList() : $data;

                // unserialize quantity list
                if (!empty($data)) {
                    if ($this->isSerialized($data)) {
                        $result = unserialize($data);
                    } else if (is_array($data)) {
                        $result = $data;
                    } else {
                        $result = array();
                    }
                }

                if (is_array($result) && count($result)) {
                    // retrieve price model
                    $priceModel = $this->getProductPriceModel($product, $this->isInCart(false));
                    $origPrice = (float) $product->getPrice();

                    $parentProduct = $this->getActiveParentProduct($product);

                    if ($parentProduct && $parentProduct->getId() && $parentProduct->getTypeId() == 'configurable') {
                        $product = $parentProduct;
                    }

                    // retrieve stock model
                    $stock = Mage::helper('ddq')->getStockItemByProduct($product);
                    $isSimpleProduct = !in_array($product->getTypeId(), array('configurable', 'grouped', 'bundle'));
                    $avoidUnavailableQty = (bool) $product->getDdqHideUnavailableQty();

                    foreach ($result as $key => $option) {
                        // remove unavailable quantity options
                        if ($avoidUnavailableQty && $isSimpleProduct && $stock && (float) $stock->getQty() < $option['qty'] && $stock->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY && $stock->getBackorders() != Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                            unset($result[$key]);

                            if (!count($result) && $this->getNoOptionsAction() == 'single_option' && $stock->getQty() > 0) {
                                $key = $this->getQtyKey($stock->getQty());

                                $newResult = array(
                                    $key => array(
                                        'qty' => (float) $stock->getQty()
                                    )
                                );

                                $result = $this->cleanQuantityData($newResult);

                                $option = $result[$key];
                            } else {
                                continue;
                            }
                        }

                        // append price data to option
                        if ($includePriceData) {
                            $priceArr = array();

                            $unitPrice = $origPrice;

                            if (isset($option['price']) || !is_null($option['price']) || strval($option['price']) != '') {
                                $unitPrice = (float) $option['price'];

                                if ($unitPrice > 0) {
                                    $unitPrice = $unitPrice / floatval($option['qty']);
                                }
                            }

                            $product->setPrice($unitPrice);
                            $origQtyFinalPrice = (float) ($priceModel->getFinalPrice($option['qty'], $product) * $option['qty']);

                            // add unit price
                            $priceArr['pu'] = Mage::helper('core')->currency($priceModel->getFinalPrice($option['qty'], $product), false, false);

                            // add final price
                            $priceArr['p'] = $priceArr['pu'] * $option['qty'];

                            // add old price
                            $priceOld = Mage::helper('core')->currency(($product->getPrice() * $option['qty']), false, false);

                            if ($priceOld !== $priceArr['p']) {
                                $priceArr['o'] = $priceOld;
                                $priceArr['ou'] = $priceArr['o'] / $option['qty'];
                            }

                            // add price incl tax
                            $priceInclTax = Mage::helper('core')->currency(Mage::helper('tax')->getPrice($product, $origQtyFinalPrice, true, null, null, null, null, null, false), false, false);

                            if ($priceInclTax !== $priceArr['p']) {
                                $priceArr['i'] = $priceInclTax;
                                $priceArr['iu'] = $priceInclTax / $option['qty'];
                            }

                            // add price excl tax
                            $priceExclTax = Mage::helper('core')->currency(Mage::helper('tax')->getPrice($product, $origQtyFinalPrice, false, null, null, null, null, null, false), false, false);

                            if ($priceExclTax !== $priceArr['p']) {
                                $priceArr['e'] = $priceExclTax;
                                $priceArr['eu'] = $priceExclTax / $option['qty'];
                            }

                            // we need to reset the price once this has run through, otherwise its kept in memory for
                            // future products (example when this can happen is in the shopping cart, where different
                            // items have different quantities and settings, ie different prices).
                            $product->setPrice($origPrice);

                            Mage::helper('core')->currency($priceModel->getFinalPrice(1, $product), false, false);

                            // add price array to quantity list
                            $result[$key]['p'] = $priceArr;
                        }
                    }
                }
            }
        }

        return is_array($result) ? $result : array();
    }

    /**
     * Add active parent product from item.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return $this
     */
    public function addActiveItemParentProduct($item)
    {
        if ($item && $item->getId() && $item->getParentItemId()) {
            if ($item->getParentItem()->getProduct()->getTypeId() == 'configurable') {
                // for price calculations to become accurate we need to reload the product,
                // otherwise things like tier pricing won't be loaded on it and the calculations
                // will ne dup being incorrect.
                $this->_activeItemParentProducts[$item->getProduct()->getId()] = Mage::getModel('catalog/product')->load($item->getParentItem()->getProductId());
            }
        }

        return $this;
    }

    /**
     * Retrieve product price model.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $inCart Whether or not to base logic on shopping cart.
     * @return Mage_Catalog_Model_Product_Type_Price
     */
    public function getProductPriceModel(Mage_Catalog_Model_Product $product, $inCart=false)
    {
        $result = Mage::getModel('catalog/product_type_price');

        if ($product && $product->getTypeId()) {
            if ($product->getTypeId() == 'bundle') {
                $result = Mage::getModel('bundle/product_price');
            } else if ($product->getTypeId() == 'grouped') {
                $result = Mage::getModel('catalog/product_type_grouped_price');
            } else if ($product->getTypeId() == 'configurable' && $inCart) {
                $result = Mage::getModel('catalog/product_type_configurable_price');
            }
        }

        return $result;
    }

    /**
     * Correct quantity list price calculations by cart item.
     *
     * Basically, calculate the price of the item's options (ie. item price - base
     * product price).
     *
     * @param array $list
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function correctPriceCalculationsByCartItem(array $list, Mage_Sales_Model_Quote_Item $item)
    {
        if (count($list) && $item) {
            $qty = (float) $item->getQty();
            $qtyKey = $this->getQtyKey($qty);

            $optionsPrice = null;
            $optionsPriceInclTax = null;

            // if we have applied an overriding price on the item we can run an easier calculation method, se the else block for that
            if (!$item->hasCustomPrice()) {
                /* @var Mage_Catalog_Model_Product_Type_Price $priceModel */
                $priceModel = $this->getProductPriceModel($item->getProduct(), true);

                /* @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());

                // calculate normal product price based on item quantity
                $productPrice = Mage::helper('core')->currency($priceModel->getFinalPrice($qty, $product), false, false);
                $productPriceInclTax = Mage::helper('tax')->getPrice($product, $productPrice, true, null, null, null, null, null, false);

                // calculate option prices (item price - product base price)
                $optionsPrice = $item->getConvertedPrice() - $productPrice;
                $optionsPriceInclTax = $item->getPriceInclTax() - $productPriceInclTax;
            } else {
                // just fetch the overriding price which has been used from the quantity option list
                // and calculate the options price as item price - overriding price, that's it.
                if (isset($list[$qtyKey]) && isset($list[$qtyKey]['p']) && isset($list[$qtyKey]['p']['pu'])) {
                    $optionsPrice = $item->getConvertedPrice() - (float) $list[$qtyKey]['p']['pu'];
                }

                if (isset($list[$qtyKey]) && isset($list[$qtyKey]['p']) && isset($list[$qtyKey]['p']['iu'])) {
                    $optionsPriceInclTax = $item->getPriceInclTax() - (float) $list[$qtyKey]['p']['iu'];
                }
            }

            // if there was an additional option based price on the item, add that additional price to all quantity options, correcting their initial calculations
            if (!is_null($optionsPrice) && $optionsPrice > 0) {
                foreach ($list as $key => $option) {
                    $list[$key]['p']['p']+= ($optionsPrice * $option['qty']);
                    $list[$key]['p']['pu']+= $optionsPrice;

                    if (isset($list[$key]['p']['o'])) {
                        $list[$key]['p']['o']+= ($optionsPrice * $option['qty']);
                        $list[$key]['p']['ou']+= $optionsPrice;
                    }

                    if (isset($list[$key]['p']['e'])) {
                        $list[$key]['p']['e']+= ($optionsPrice * $option['qty']);
                        $list[$key]['p']['eu']+= $optionsPrice;
                    }

                    if (isset($list[$key]['p']['i']) && !is_null($optionsPriceInclTax) && $optionsPriceInclTax > 0) {
                        $list[$key]['p']['i']+= ($optionsPriceInclTax * $option['qty']);
                        $list[$key]['p']['iu']+= $optionsPriceInclTax;
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Override price of quote item.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param float $price
     * @param float $qty
     */
    public function overrideItemPrice(Mage_Sales_Model_Quote_Item &$item, $price=null, $qty=null)
    {
        if (!is_null($price)) {
            $item->setCustomPrice($price)->setOriginalCustomPrice($price);
        } else {
            // fetch quantity list, with prices corrected for the item (to take option based prices into consideration)
            $list = $item->getProduct()->getDdqQtyList();

            if (is_null($list)) {
                // we need to reload the product, otherwise the quantity option settings for
                // the product won't be correct. This is a strange problem and the only way
                // to resolve it seems to tbe to reload the product.
                $item->getProduct()->load($item->getProduct()->getId());

                // reload the quantity options list again
                $list = $item->getProduct()->getDdqQtyList();
            }

            if ($item->getProduct()->getDdqEnabled()) {
                if ($this->isSerialized($list)) {
                    $list = @unserialize($list);
                }

                if (is_array($list) && count($list)) {
                    $list = $this->correctPriceCalculationsByCartItem($list, $item);

                    // retrieve requested quantity
                    if (is_null($qty)) {
                        $qty = $item->getQtyToAdd() ? (float) $item->getQtyToAdd() : (float) $item->getQty();
                    }

                    $qtyKey = Mage::helper('ddq')->getQtyKey($qty);

                    // apply overriding price
                    if (is_array($list) && count($list) && isset($list[$qtyKey])) {
                        if (isset($list[$qtyKey]['price']) && !is_null($list[$qtyKey]['price']) && isset($list[$qtyKey]['p'])) {
                            $newPrice = (float) $list[$qtyKey]['p']['pu'];

                            if ($newPrice > 0 || Mage::getStoreConfigFlag('ddq/cart/allow_zero_price')) {
                                $item->setCustomPrice($newPrice)->setOriginalCustomPrice($newPrice);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Retrieve stock status of product based on quantity.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $qty
     * @return string
     */
    public function getStockStatus(Mage_Catalog_Model_Product $product, $qty)
    {
        $result = 'Out of Stock';

        if ($product && $product->getId()) {
            $stock = $this->getStockItemByProduct($product);

            if ($qty <= (float) $stock->getQty()) {
                $result = 'In Stock';
            } else if ($stock->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES) {
                $result = 'On Backorder';
            }
        }

        return Mage::helper('catalog')->__($result);
    }

    /**
     * Retrieve stock model by product.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_CatalogInventory_Model_Stock_Item|null
     */
    public function getStockItemByProduct(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
    }

    /**
     * Get the parent product of active product record, if any.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return mixed|null
     */
    public function getActiveParentProduct(Mage_Catalog_Model_Product $product)
    {
        $result = null;

        if (!in_array($product->getTypeId(), array('configurable', 'bundle', 'grouped'))) {
            if ($this->isInCart(false) && isset($this->_activeItemParentProducts[$product->getId()])) {
                $result = $this->_activeItemParentProducts[$product->getId()];
            } else {
                $activeProduct = Mage::registry('product');

                if (!$activeProduct || !$activeProduct->getId()) {
                    $activeProduct = Mage::registry(self::REG_KEY_AJAX_CONFIGURABLE_PRODUCT);
                }

                if ($activeProduct && $activeProduct->getId()) {
                    if (in_array($activeProduct->getTypeId(), array('configurable', 'bundle', 'grouped'))) {
                        $parentIds = $activeProduct->getTypeInstance(true)->getParentIdsByChild($product->getId());

                        if (is_array($parentIds) && count($parentIds)) {
                            $result = in_array($activeProduct->getId(), $parentIds) ? $activeProduct : null;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Assign default ddq data to product.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function assignDefaultProductData(Mage_Catalog_Model_Product &$product)
    {
        // keep track of loaded products, will be used for product lists / shopping cart.
        if (!in_array($product->getTypeId(), array('configurable', 'grouped', 'bundle'))) {
            if (!isset($this->_loadedProducts[$product->getId()])) {
                $this->_loadedProducts[$product->getId()] = $product;
            }
        }

        // skip option products.
        if (!($product->getOptionId() && $product->getParentProductId())) {
            /* @var $cache MageParts_Ddq_Helper_Cache */
            $cache = Mage::helper('ddq/cache');

            // keeps track of which values relies on config defaults
            $useConfig = array();

            // whether or not we are in the administration panel
            $isAdmin = $this->isAdmin();

            // load parent product record
            $parentProduct = !$isAdmin ? $this->getActiveParentProduct($product) : null;

            // whether or not extension is enabled
            if (is_null($product->getData('ddq_enabled'))) {
                $product->setData('ddq_enabled', ($parentProduct && $parentProduct->getId() ? $parentProduct->getDdqEnabled() : $this->isEnabled()));
                $useConfig['ddq_enabled'] = true;
            }

            // whether or not incremental quantities is enabled
            if (is_null($product->getData('ddq_incremental'))) {
                $product->setData('ddq_incremental', ($parentProduct && $parentProduct->getId() ? $parentProduct->getDdqIncremental() : $this->getIsIncrementalEnabled()));
                $useConfig['ddq_incremental'] = true;
            }

            // whether or not to hide unavailable quantity options at render
            if (is_null($product->getData('ddq_hide_unavailable_qty'))) {
                $product->setData('ddq_hide_unavailable_qty', ($parentProduct && $parentProduct->getId() ? $parentProduct->getDdqHideUnavailableQty() : $this->getDefaultConfigHideUnavailable()));
                $useConfig['ddq_hide_unavailable_qty'] = true;
            }
            // what layout to use
            if (is_null($product->getData('ddq_layout'))) {
                $product->setData('ddq_layout', ($parentProduct && $parentProduct->getId() ? $parentProduct->getDdqLayout() : Mage::getStoreConfig('ddq/layout/type')));
                $useConfig['ddq_layout'] = true;
            }

            // apply product type dependent layout restrictions
            if (!$isAdmin) {
                $this->applyLayoutTypeRestrictions($product, ($parentProduct && $parentProduct->getTypeId() ? $parentProduct->getTypeId() : null));
            }

            // quantity options
            $qtyList = null;

            $qtyListCacheKeyPieces = array(
                Mage_Catalog_Model_Product::CACHE_TAG,
                $product->getId(),
                'mp_ddq_quantity_list'
            );

            if ($parentProduct && $parentProduct->getId()) {
                $qtyListCacheKeyPieces[] = $parentProduct->getId();
            }

            $qtyListCacheKey = $cache->generateCacheKey($qtyListCacheKeyPieces);

            // attempt to load quantity list for the product from cache
            if (!$isAdmin && $cache->isCacheEnabled()) {
                $cachedQtyList = $cache->load($qtyListCacheKey);

                if (!empty($cachedQtyList) && $this->isSerialized($cachedQtyList)) {
                    $qtyList = @unserialize($cachedQtyList);
                    $product->setData('ddq_qty_list', $qtyList);
                }
            }

            if (is_null($qtyList)) {
                // on the frontend store view specific values aren't marked as such for child products when
                // viewing grouped, configurable or bundled products. For that reason, we must execute the
                // code below for Magento to setup the flag references in the next if block, which determines
                // whether or not to use store view specific values. This was added so that incremental values
                // wouldn't be overwritten over and over again.
                if (!$isAdmin) {
                    Mage::getResourceModel('catalog/product')->load($product, $product->getId(), array('ddq_qty_list'));
                }

                // load product specific, and store view specific, values
                if ($product->getExistsStoreValueFlag('ddq_qty_list')) {
                    $qtyList = $product->getDdqQtyList();

                    if (!empty($qtyList) && $this->isSerialized($qtyList)) {
                        $qtyList = @unserialize($qtyList);
                    }
                }

                // load incremental quantity options
                if (!$isAdmin && (!is_array($qtyList) || !count($qtyList))) {
                    // generate incremental quantities for this product
                    if ((bool) $product->getDdqIncremental()) {
                        $list = array();

                        // which field to rely on for the increment value
                        $field = Mage::getStoreConfig('ddq/incremental/field');
                        $increment = 0;

                        /* @var $stock Mage_CatalogInventory_Model_Stock_Item */
                        $stock = $this->getStockItemByProduct($product);

                        if (!empty($field)) {
                            if ($field == 'custom') {
                                $increment = (float) Mage::getStoreConfig('ddq/incremental/custom_increment');
                            } else {
                                if ($field == 'qty_increments' && $stock && $stock->getId() && (bool) $stock->getEnableQtyIncrements()) {
                                    $increment = (float) $stock->getQtyIncrements();
                                }
                            }
                        }

                        if ($increment > 0) {
                            // retrieve option limit
                            $limit = 0;

                            if (Mage::getStoreConfig('ddq/incremental/option_limit_type') == 'custom') {
                                $limit = intval(Mage::getStoreConfig('ddq/incremental/custom_option_limit')) * $increment;
                            } else {
                                $limit = floor(floatval($stock->getQty()) / $increment) * $increment;
                            }

                            // generate quantity options
                            if ($limit > 0) {
                                for ($i = $increment; $i <= $limit; $i += $increment) {
                                    $list[] = array(
                                        'qty' => $i
                                    );
                                }
                            }
                        }

                        if (count($list)) {
                            // add generated list to product model
                            $qtyList = $this->cleanQuantityData($list);

                            // save to product record
                            if (Mage::getStoreConfigFlag('ddq/incremental/store_on_product')) {
                                Mage::getSingleton('catalog/product_action')
                                    ->updateAttributes(array($product->getId()), array('ddq_qty_list' => serialize($qtyList)), $product->getStoreId());
                            }
                        }
                    }
                }

                // load product specific values (including default store view)
                if (!is_array($qtyList) || !count($qtyList)) {
                    $qtyList = $product->getDdqQtyList();

                    if (!empty($qtyList) && $this->isSerialized($qtyList)) {
                        $qtyList = @unserialize($qtyList);
                    }
                }

                // load fallback values
                if (!is_array($qtyList) || !count($qtyList)) {
                    // attempt to retrieve values from default store view
                    $defaultList = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'ddq_qty_list', 0);

                    if ($this->isSerialized($defaultList)) {
                        $defaultList = @unserialize($defaultList);
                    }

                    if (!is_array($defaultList) || !count($defaultList)) {
                        if (!$isAdmin && $parentProduct && $parentProduct->getId()) {
                            $parentQtys = $parentProduct->getDdqQtyList();

                            if ($this->isSerialized($parentQtys)) {
                                $parentQtys = @unserialize($parentQtys);
                            }

                            if (is_array($parentQtys)) {
                                $defaultList = $parentQtys;
                            }
                        }

                        if (!$defaultList) {
                            $defaultList = $this->getDefaultConfigQtys();
                            $useConfig['ddq_qty_list'] = true;
                        }
                    }

                    $qtyList = $defaultList;
                }

                if (!$isAdmin) {
                    $cleanProduct = $product;

                    if ($product->getCustomOption('info_buyRequest')) {
                        $cleanProduct = Mage::getModel('catalog/product')->load($product->getId());
                    }

                    $qtyList = $this->getQuantityListByProduct($cleanProduct, $qtyList, $this->getPriceUpdatesEnabled());
                    $product->setData('ddq_qty_list', $qtyList);

                    if ($cache->isCacheEnabled() && !empty($qtyListCacheKey)) {
                        $cache->save(serialize($qtyList), $qtyListCacheKey, (($parentProduct && $parentProduct->getId()) ? $cache->getProductTags($parentProduct) : $cache->getProductTags($product)));
                    }
                } else {
                    $product->setData('ddq_qty_list', serialize($qtyList));
                }
            }

            // apply "Out of Stock" no options action
            if ($product->getDdqEnabled() && (!is_array($qtyList) || !count($qtyList)) && $this->getNoOptionsAction() == 'out_of_stock') {
                $product->setData('is_salable', false);
            }

            // preselected value
            if (is_null($product->getData('ddq_preselected'))) {
                $preselectedQty = ($parentProduct && $parentProduct->getId() ? $parentProduct->getDdqPreselected() : $this->getDefaultConfigPreselected());

                if (!$isAdmin && is_array($qtyList) && count($qtyList) && !isset($qtyList[$this->getQtyKey($preselectedQty)]) && !($product->getDdqLayout() == 'select' && Mage::getStoreConfigFlag('ddq/layout/select_header', $product->getStoreId()))) {
                    reset($qtyList);
                    $preselectedQty = (float) key($qtyList);
                }

                $product->setData('ddq_preselected', $preselectedQty);
                $useConfig['ddq_preselected'] = true;
            }

            // assign config defaults array (to specify which attributes rely on config defaults)
            $product->setData('ddq_use_config', $useConfig);
        }
    }

    /**
     * Apply layout type restrictions to product object.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $parentType
     * @return $this
     */
    public function applyLayoutTypeRestrictions(Mage_Catalog_Model_Product &$product, $parentType=null)
    {
        if ($product && $product->getDdqLayout()) {
            $layout = $product->getDdqLayout();

            if (!isset($this->_layoutProductTypes[$layout])) {
                $conf = Mage::getStoreConfig('ddq/layout/' . $layout . '_product_types', $product->getStoreId());

                if (!empty($conf) && $this->isSerialized($conf)) {
                    $conf = @unserialize($conf);
                }

                $conf = explode(',', $conf);

                $this->_layoutProductTypes[$layout] = is_array($conf) ? $conf : array();
            }

            $type = $product->getTypeId();

            if (!is_null($parentType)) {
                $type = $parentType;
            }

            $override = true;

            if (in_array($type, $this->_layoutProductTypes[$layout])) {
                $override = false;
            }

            if ($override) {
                $product->setDdqLayout('select');
            }
        }

        return $this;
    }

    /**
     * Generate hash from cart item options data.
     *
     * @param Mage_Sales_Model_Quote_Item
     * @param array $avoidCodes
     * @return string
     */
    public function getCartItemOptionHash(Mage_Sales_Model_Quote_Item $item, array $avoidCodes)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $item->getProduct();

        if ($product && $product->getId()) {
            $options = $item->getOptions();
            $optionHash = $product->getId();

            if (count($options)) {
                foreach ($options as $o) {
                    if (!in_array($o->getCode(), $avoidCodes)) {
                        $optionHash.= $o->getCode() . '_' . $o->getValue();
                    }
                }
            }
        }

        if (!is_null($optionHash)) {
            $optionHash = sha1($optionHash);
        }

        return $optionHash;
    }

    /**
     * Retrieve list of loaded products, for use on product lists / cart page.
     *
     * @return array
     */
    public function getLoadedProducts()
    {
        return $this->_loadedProducts;
    }

    /**
     * Clean quantity array data (make sure all keys are included, assign
     * default values, sort it etc.).
     *
     * @param array $data
     * @return array
     */
    public function cleanQuantityData(array $data)
    {
        $result = array();
        $order = array();

        if (!is_array($data) && $this->isSerialized($data)) {
            $data = @unserialize($data);
        }

        if (is_array($data) && count($data)) {
            foreach ($data as $v) {
                $qty = floatval($v['qty']);

                if ($qty > 0) {
                    if (isset($v['delete']) && $v['delete'] == '1') {
                        continue;
                    }

                    $key = $this->getQtyKey($qty);

                    $result[$key] = array(
                        'qty'   => floatval($v['qty']),
                        'price' => (isset($v['price']) && strval($v['price']) != '') ? floatval($v['price'])  : null,
                        'label' => (isset($v['label']) && !empty($v['label'])) ? $v['label']                  : null,
                        'order' => (isset($v['order']) && !empty($v['order'])) ? $v['order']                  : null
                    );

                    $order[$key] = (int) $result[$key]['order'];
                }
            }
        }

        array_multisort($order, SORT_ASC, $result);

        return $result;
    }

    /**
     * Retrieve URL helper.
     *
     * @return MageParts_Base_Helper_Url
     */
    public function getUrlHelper()
    {
        return Mage::helper('mageparts_base/url');
    }

    /**
     * Check if requested url is an injection point for product collections.
     *
     * @param int $storeId
     * @param string $url
     * @return boolean
     */
    public function isCollectionInjectionUrl($storeId=null, $url='')
    {
        if (!is_string($url) || empty($url)) {
            $url = $this->getUrlHelper()->getCurrentUrl();
        }

        return $this->getUrlHelper()->urlInTable('ddq/general/collection_injection', $url, $storeId);
    }

    /**
     * Compare two quantity list arrays to see if they differ.
     *
     * @param array $array1
     * @param array $array2
     * @param boolean $checkKeysOnly
     * @return boolean
     */
    public function qtListDiff($array1, $array2, $checkKeysOnly=false)
    {
        $result = false;

        foreach ($array1 as $qty => $priceData) {
            if (!isset($array2[$qty])) {
                $result = true;
                break;
            }

            if (!$checkKeysOnly && array_diff($priceData, $array2[$qty])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Retrieve configured quantity box element path.
     *
     * @param boolean $qtyBoxesOnly Only include paths to quantity boxes
     * @param boolean $jsonEncode
     * @return string|array
     */
    public function getQtyBoxElPaths($qtyBoxesOnly=false, $jsonEncode=true)
    {
        $result = array(
            'simple' => Mage::getStoreConfig('ddq/element_paths/qty_box'),
            'configurable' => Mage::getStoreConfig('ddq/element_paths/qty_box_configurable'),
            'bundle' => Mage::getStoreConfig('ddq/element_paths/qty_box_bundle')
        );

        if (!$qtyBoxesOnly) {
            $result['cartForm'] = Mage::getStoreConfig('ddq/element_paths/cart_form');
        }

        return $jsonEncode ? Zend_Json::encode($result) : $result;
    }

    /**
     * Compare the ddq config of two products and check if they are equal.
     *
     * @param Mage_Catalog_Model_Product $default
     * @param Mage_Catalog_Model_Product $compare
     * @param array $settings Specific settings to check (leave empty to compare all settings)
     * @param boolean $checkQtysOnly Check if the two products have the same quantities and nothing else
     * @param boolean $complexQtyCheck Used to compare qty lists in order to avoid generating multiple duplicates of same HTML element
     * @return boolean
     */
    public function ddqIsEqual(Mage_Catalog_Model_Product $default, Mage_Catalog_Model_Product $compare, array $settings=null, $checkQtysOnly=false, $complexQtyCheck=false)
    {
        $result = true;

        if ($default && $default->getId() && $compare && $compare->getId()) {
            if ($checkQtysOnly) {
                $settings = array(
                    'ddq_qty_list'
                );
            } else if (!is_array($settings) || !count($settings)) {
                $settings = array(
                    'ddq_layout',
                    'ddq_preselected',
                    'ddq_qty_list',
                    'ddq_incremental',
                    'ddq_hide_unavailable_qty',
                    'ddq_enabled'
                );
            }

            foreach ($settings as $s) {
                if ($s == 'ddq_qty_list') {
                    $defaultQtys = $default->getData('ddq_qty_list');
                    $compareQtys = $compare->getData('ddq_qty_list');

                    if (count($defaultQtys) !== count($compareQtys)) {
                        $result = false;
                        break;
                    }

                    if ($checkQtysOnly) {
                        $result = array_diff(array_keys($defaultQtys), array_keys($compareQtys));

                        if (count($result)) {
                            $result = false;
                            break;
                        }
                    } else if ($complexQtyCheck) {
                        foreach ($defaultQtys as $key => $data) {
                            if (!isset($compareQtys[$key])) {
                                $result = false;
                                break 2;
                            }

                            $compareData = $compareQtys[$key];

                            $defaultP = isset($data['p']) ? $data['p'] : array();
                            $compareP = isset($compareData['p']) ? $compareData['p'] :array();
                            $defaultPrice = $data['price'];
                            $comparePrice = $compareData['price'];

                            unset($data['p']);
                            unset($data['price']);
                            unset($compareData['p']);
                            unset($compareData['price']);

                            if (serialize($data) !== serialize($compareData)) {
                                $result = false;
                                break 2;
                            }

                            if ((is_array($defaultP) && count($defaultP)) || (is_array($compareP) && count($compareP))) {
                                foreach (array('/{{price/', '/{{unit_price/') as $pattern) {
                                    if (preg_match($pattern, $this->getOptionLabel($data, null, false))) {
                                        if (serialize($defaultP) !== serialize($compareP) || $defaultPrice !== $comparePrice) {
                                            $result = false;
                                            break 3;
                                        } else {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $defaultQtys = serialize($defaultQtys);
                        $compareQtys = serialize($compareQtys);

                        if ($defaultQtys !== $compareQtys) {
                            $result = false;
                            break;
                        }
                    }
                } else if ($default->getData($s) !== $compare->getData($s)) {
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve quantity option label.
     *
     * @param array $option
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $parse Whether or not to parse the labels template portion.
     * @param string $default Returns if label turns up empty.
     * @param string $pricePlaceHolder Replacement for missing price data.
     * @return string
     */
    public function getOptionLabel($option=null, $product=null, $parse=true, $default='', $pricePlaceHolder='?')
    {
        $result = '';
        $option = !is_array($option) ? array() : $option;
        $default = empty($default) && isset($option['qty']) ? $option['qty'] : $default;

        // add label prefix
        $result.= Mage::getStoreConfig('ddq/layout/label_prefix');

        // add label content
        if (isset($option['label'])) {
            $result.= $option['label'];
        } else {
            $labelTemplate = Mage::getStoreConfig('ddq/layout/label_template');

            if (!empty($labelTemplate)) {
                $result.= $labelTemplate;
            } else if (isset($option['qty'])) {
                $result.= $option['qty'];
            }
        }

        // add label suffix
        $result.= Mage::getStoreConfig('ddq/layout/label_suffix');

        // parse label template
        if (!empty($result) && $parse && stripos($result, '{{') > -1) {
            $templateData = array();

            if ((stripos($result, '{{qty}}') > -1) && isset($option['qty'])) {
                $templateData['/{{qty}}/i'] = $option['qty'];
            }

            if ((stripos($result, '{{stock_status}}') > -1) && $product && ($product instanceof Mage_Catalog_Model_Product) && $product->getId()) {
                $templateData['/{{stock_status}}/i'] = $this->getStockStatus($product, $option['qty']);
            }

            if ($this->getPriceUpdatesEnabled() && isset($option['p'])) {
                if (stripos($result, '{{price}}') > -1) {
                    $templateData['/{{price}}/i'] = isset($option['p']['p']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['p'], false), '/') : $pricePlaceHolder;
                }

                if (stripos($result, '{{price_old}}') > -1) {
                    $templateData['/{{price_old}}/i'] = isset($option['p']['o']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['o'], false), '/') : (isset($option['p']['p']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['p'], false), '/') : $pricePlaceHolder);
                }

                if (stripos($result, '{{price_excl_tax}}') > -1) {
                    $templateData['/{{price_excl_tax}}/i'] = isset($option['p']['e']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['e'], false), '/') : (isset($option['p']['p']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['p'], false), '/') : $pricePlaceHolder);
                }

                if (stripos($result, '{{price_incl_tax}}') > -1) {
                    $templateData['/{{price_incl_tax}}/i'] = isset($option['p']['i']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['i'], false), '/') : (isset($option['p']['p']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['p'], false), '/') : $pricePlaceHolder);
                }

                if (stripos($result, '{{unit_price}}') > -1) {
                    $templateData['/{{unit_price}}/i'] = isset($option['p']['pu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['pu'], false), '/') : $pricePlaceHolder;
                }

                if (stripos($result, '{{unit_price_old}}') > -1) {
                    $templateData['/{{unit_price_old}}/i'] = isset($option['p']['ou']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['ou'], false), '/') : (isset($option['p']['pu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['pu'], false), '/') : $pricePlaceHolder);
                }

                if (stripos($result, '{{unit_price_excl_tax}}') > -1) {
                    $templateData['/{{unit_price_excl_tax}}/i'] = isset($option['p']['eu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['eu'], false), '/') : (isset($option['p']['pu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['pu'], false), '/') : $pricePlaceHolder);
                }

                if (stripos($result, '{{unit_price_incl_tax}}') > -1) {
                    $templateData['/{{unit_price_incl_tax}}/i'] = isset($option['p']['iu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['iu'], false), '/') : (isset($option['p']['pu']) ? preg_quote(Mage::helper('core')->formatPrice($option['p']['pu'], false), '/') : $pricePlaceHolder);
                }
            }

            if (count($templateData)) {
                $result = preg_replace(array_keys($templateData), array_values($templateData), $result);
                $result = str_replace("\\", "", $result);
            }
        }

        return empty($result) ? $default : $result;
    }

    /**
     * Retrieve simple product record from item record.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return Mage_Catalog_Model_Product
     */
    public function getProductByItem(Mage_Sales_Model_Quote_Item $item) {
        return $item->getOptionByCode('simple_product') ? $item->getOptionByCode('simple_product')->getProduct() : $item->getProduct();
    }

    /**
     * Get qty array key.
     *
     * @param float $qty
     * @return string
     */
    public function getQtyKey($qty)
    {
        return strval($this->formatQty($qty));
    }

    /**
     * Format quantity value properly.
     *
     * @param float $qty
     * @return float
     */
    public function formatQty($qty)
    {
        return number_format((float) $qty, 4, '.', '');
    }

    /**
     * Check whether or not we are using the shopping cart controller.
     *
     * @param boolean $includeConfigurePage
     * @return boolean
     */
    public function isInCart($includeConfigurePage=true)
    {
        $result = (Mage::app()->getRequest()->getModuleName() == 'checkout' && Mage::app()->getRequest()->getControllerName() == 'cart');

        if ($result && !$includeConfigurePage && Mage::app()->getRequest()->getActionName() == 'configure') {
            $result = false;
        }

        return $result;
    }

    /**
     * Check whether or not we are on the product detail page.
     *
     * @return boolean
     */
    public function isOnProductDetailPage()
    {
        return (Mage::app()->getRequest()->getModuleName() == 'catalog' && Mage::app()->getRequest()->getControllerName() == 'product' && Mage::app()->getRequest()->getActionName() == 'view');
    }

    /**
     * Retrieve simple product from cart item option.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return Mage_Catalog_Model_Product|null
     */
    public function getSimpleProductFromItemOptions(Mage_Sales_Model_Quote_Item $item)
    {
        $result = null;

        if ($item) {
            $o = $item->getOptionByCode('simple_product');

            if ($o && $o->getProductId()) {
                $result = Mage::getModel('catalog/product')->load($o->getProductId());
            }
        }

        return $result;
    }

    /**
     * Retrieve columns used for layout type Table.
     *
     * @return array
     */
    public function getLayoutTableColumns()
    {
        if (is_null($this->_layoutTableColumns)) {
            $result = array();
            $order = array();

            $columns = Mage::getStoreConfig('ddq/layout/table_columns');

            if ($this->isSerialized($columns)) {
                $columns = unserialize($columns);
            }

            if (is_array($columns) && count($columns)) {
                $index = 0;

                foreach ($columns as $c) {
                    $index++;

                    $result[$index] = $c;
                    $order[$index] = (int) $c['order'];
                }

                array_multisort($order, SORT_ASC, $result);
            }

            $this->_layoutTableColumns = $result;
        }

        return $this->_layoutTableColumns;
    }

}
