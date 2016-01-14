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
 * @author 	   MageParts Crew
 */

class MageParts_Ddq_Block_Adminhtml_Catalog_Product_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Field "namespace". Basically the head key of the associative array
     * submitted to the controller when saving data. This changes between
     * the product page and the page where attributes are updated for
     * multiple products at once, hence we need to be able to se it.
     *
     * @var string
     */
    protected $_fieldNameSpace;

    /**
     * Set blcok template.
     *
     * @var string
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mageparts/ddq/catalog/product/form.phtml');
    }

    /**
     * Return current product instance.
     *
     * @return Mage_Catalog_Model_Product
     */
    public function _getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * We need this function just to setup out fieldset to which
     * we will later be moving our price groups attribute fields
     * through javascript (the the form set in the constructor)
     * above for more information on that.
     *
     * @return Mage_Adminhtml_Block_Widget_Form|void
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->addFieldset('ddq', array('legend' => Mage::helper('ddq')->__('Quantity Options')));
        $this->setForm($form);
    }

    /**
     * Retrieve initializing JS code.
     *
     * @return string
     */
    public function getInitJs()
    {
        $result = '';

        $values = array();

        if ($this->_getProduct()) {
            $values = $this->_getProduct()->getDdqQtyList();

            if (Mage::helper('ddq')->isSerialized($values)) {
                $values = @unserialize($values);
            }
        }

        if (is_array($values) && count($values)) {
            foreach ($values as $row) {
                $result.= "mpDdq.addRow('" . $row['qty'] . "', '" . (isset($row['price']) ? $row['price'] : '') . "', '" .  (isset($row['label']) ? $row['label'] : '') . "', '" . (isset($row['order']) ? $row['order'] : '') . "');\n";
            }
        }

        return $result;
    }

    /**
     * Retrieve field namespace.
     *
     * @return string
     */
    public function _getFieldNamespace()
    {
        if (is_null($this->_fieldNameSpace)) {
            $this->_fieldNameSpace = !$this->getFieldNamespace() ? 'product' : $this->getFieldNamespace();
        }

        return $this->_fieldNameSpace;
    }

}
