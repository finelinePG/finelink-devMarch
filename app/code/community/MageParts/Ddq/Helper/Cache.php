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

class MageParts_Ddq_Helper_Cache extends MageParts_Base_Helper_Cache
{

    /**
     * Overwrites base value.
     *
     * @var array
     */
    protected $_globalTags = array('mageparts_ddq');

    /**
     * Define module name, used by various Base helper functions.
     *
     * @var string
     */
    protected $_moduleName = 'ddq';

    /**
     * Retrieve whether or not to keep cache unique to customer group.
     *
     * @param int $storeId
     * @return boolean
     */
    public function isCacheUniqueToCustomerGroup($storeId=null)
    {
        return Mage::getStoreConfigFlag('ddq/cache/customer_group_unique', (is_null($storeId) ? $this->getStoreId() : $storeId));
    }

    /**
     * Generate and return cache key.
     *
     * @param array $pieces
     * @param boolean $includeCommonData
     * @return string
     */
    public function generateCacheKey(array $pieces, $includeCommonData=true)
    {
        if ($includeCommonData) {
            $pieces[] = $this->getCurrencyCode();
            $pieces[] = $this->getStoreId();

            // customer group id
            if ($this->isCacheUniqueToCustomerGroup()) {
                $pieces[] = $this->getCustomerGroup();
            }

            if (Mage::helper('ddq')->getPriceUpdatesEnabled()) {
                // tax request data
                $taxRateRequest = Mage::getSingleton('tax/calculation')->getRateRequest();

                if ($taxRateRequest && $taxRateRequest->getData()) {
                    $pieces = array_merge($pieces, $taxRateRequest->getData());
                }

                // module / controller / action data (tier prices etc. if for example not included on product lists, thus the cache needs to be unique)
                $mca = Mage::helper('mageparts_base/url')->getMca();

                if ($mca == 'catalog/product/view') {
                    $pieces[] = 'product_view';
                } else if (strpos($mca, 'checkout/cart') === 0) {
                    $pieces[] = 'cart';
                } else {
                    $pieces[] = 'product_default';
                }
            }
        }

        return parent::generateCacheKey($pieces, true);
    }

}
