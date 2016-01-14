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

class MageParts_Ddq_Block_Adminhtml_Catalog_Product_Form_Renderer_Quantityoption
    extends Varien_Data_Form_Element_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Product instance model.
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mageparts/ddq/catalog/product/form/renderer/opt.phtml');
    }

    /**
     * This is a bit of a hack, the function below will set the template used
     * for this field to render it properly, however the renderer property is
     * used for all subsequent fields since the extending block is a singleton.
     * This means that we will need to set the template to our custom one, let
     * the block render, and then set the template back to the original one so
     * the same template isn't used for all subsequent fields. Ugly yes but
     * functional.
     *
     * @return mixed|string
     */
    public function toHtml()
    {
        if ($this->getRenderer()) {
            $orig = $this->getRenderer()->getTemplate();
            $this->getRenderer()->setTemplate('mageparts/ddq/catalog/product/form/renderer/opt.phtml');
            $result = parent::toHtml();
            $this->getRenderer()->setTemplate($orig);
        } else {
            $result = parent::toHtml();
        }

        return $result;
    }

    /**
     * This field contains a serialized value which converts to an array,
     * the parent function will through an error since it's expecting
     * a string and wont't typecast or validate the data type. This
     * override is meant to avoid the escape functionality for this
     * specific field.
     *
     * @param $string
     * @return array|string
     */
    protected function _escape($string)
    {
        return !is_array($string) ? parent::_escape($string) : $string;
    }

    /**
     * Return current product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function _getProduct()
    {
        if (is_null($this->_product)) {
            $product = Mage::registry('product');

            if (!$product || !($product instanceof Mage_Catalog_Model_Product)) {
                $product = Mage::getModel('catalog/product');
            }

            $this->_product = $product;
        }

        return $this->_product;
    }

    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Retrieve Add Group Item button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $button = Mage::app()->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('ddq')->__('Add Option'),
                'onclick'   => 'return mpDdq.addRow()',
                'class'     => 'add'
            ));

        return $button->toHtml();
    }

    /**
     * Retrieve the attribute model for this field.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getLabel()
    {
        return $this->_getProduct()->getResource()->getAttribute('ddq_qty_list')->getStoreLabel();
    }

    /**
     * Check whether we are on the "Update Attributes" page or not. We
     * need to check this because we need to include the "Change"
     * checkbox manually for this field due to the special renderer.
     *
     * @return boolean
     */
    public function getIsOnUpdateAttributePage()
    {
        return (Mage::app()->getRequest()->getControllerName() == 'catalog_product_action_attribute' && Mage::app()->getRequest()->getActionName() == 'edit');
    }

    /**
     * Whether or not we are using the default value for the field.
     *
     * @return boolean
     */
    public function qtyListFieldUsedDefault()
    {
        return !$this->_getProduct()->getExistsStoreValueFlag('ddq_qty_list');
    }

}
