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

class MageParts_Ddq_Block_Adminhtml_System_Config_Form_Field_Tablecolumns extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var MageParts_Ddq_Block_Adminhtml_System_Config_Form_Field_Type_Collection
     */
    protected $_typeCollectionRenderer;

    public function __construct()
    {
        $this->addColumn('type', array(
            'label' => Mage::helper('ddq')->__('Type'),
            'renderer' => $this->_getTypeCollectionRenderer()
        ));

        $this->addColumn('title', array(
            'label' => Mage::helper('ddq')->__('Title'),
            'style' => 'width:175px',
        ));

        $this->addColumn('order', array(
            'label' => Mage::helper('ddq')->__('Order'),
            'style' => 'width:30px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('ddq')->__('Add Column');

        parent::__construct();
    }

    /**
     * Retrieve type collection column renderer.
     *
     * @return MageParts_Ddq_Block_Adminhtml_System_Config_Form_Field_Type_Collection
     */
    protected function _getTypeCollectionRenderer()
    {
        if (!$this->_typeCollectionRenderer) {
            $this->_typeCollectionRenderer = Mage::getSingleton('core/layout')->createBlock(
                'ddq/adminhtml_system_config_form_field_type_collection', '',
                array('is_render_to_js_template' => true)
            );
            $this->_typeCollectionRenderer->setClass('type_select');
            $this->_typeCollectionRenderer->setExtraParams('style="width:170px"');
        }
        return $this->_typeCollectionRenderer;
    }

    /**
     * Prepare existing row data object.
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTypeCollectionRenderer()->calcOptionHash($row->getData('type')),
            'selected="selected"'
        );
    }

}
