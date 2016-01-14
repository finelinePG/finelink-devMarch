<?php

class Chili_Web2print_Block_Editor extends Mage_Catalog_Block_Product_View {

    private $_editor;

    public function __construct() {
        parent::__construct();
        $this->_editor = Mage::getSingleton('web2print/editor');
    }

    public function getChiliEditorUrl() {
        return $this->_editor->getChiliEditorUrl();
    }

    public function getMode() {
        return $this->_editor->getMode();
    }

    public function getChiliDocumentId() {
        return $this->_editor->getChiliDocumentId();
    }

    public function isChiliVariableHtmlFormEnabled() {
        if ($this->_editor->getProduct()->getData('enable_editor_form')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get form based on the document variables
     * @return bool|string
     */
    public function getFormHtml(){

        if(!$this->getChiliDocumentId()) {
            return false;
        }

        $chiliDocumentId = $this->getChiliDocumentId();
        $form = '';
        $variables = '';

        //checken if we can get the form from the cache
        try {
            if($this->getCacheEditor($chiliDocumentId) && $this->getMode() == 'product') {
                $variables = new SimpleXMLElement($this->getCacheEditor($chiliDocumentId));

            } else {
                $variables = new SimpleXMLElement(Mage::getModel('web2print/api')->getDocumentVariableDefinitions($chiliDocumentId));

                if(is_numeric(Mage::helper('web2print')->getCacheLifetimeEditor()) && Mage::app()->getCacheInstance()->canUse('chili_forms')) {
                    $cacheName = 'editorXML_'.$chiliDocumentId;
                    $cacheModel = Mage::getSingleton('core/cache');
                    $cacheModel->save((string)Mage::getModel('web2print/api')->getDocumentVariableDefinitions($chiliDocumentId), $cacheName, array("chili_forms"), Mage::helper('web2print')->getCacheLifetimeEditor());
                }
            }
        } catch (Exception $e) {
            Mage::throwException($this->__('Unable to fetch HTML from based on CHILI document variables: ').$e->getMessage());
        }

        if($variables->item->count()) {
            $form = $this->renderVariableTypes($variables);
        }

        return $form;
    }

    /**
     * Fetch a cached version of the editor form if available
     * @param $documentId
     * @return bool
     */
    private function getCacheEditor($documentId){
        $cacheName = 'editorXML_'.$documentId;
        $cacheModel  = Mage::getSingleton('core/cache');
        $cacheEditorXML = $cacheModel->load($cacheName);

        if($cacheEditorXML != "") {
            return $cacheEditorXML;
        } else {
            return false;
        }
    }

    /**
     * @param SimpleXMLElement $data
     * @return string
     */
    private function renderVariableTypes($data){

        $html =  "<form id='editorForm'>";
        $html .= "<table width='80%'>";

        $i = 0;

        foreach($data->item as $item){
            $attributes = $item->attributes();

            switch ($attributes['dataType']){
                case 'list':
                $html .= $this->getLayout()->createBlock('Mage_Core_Block_Template','editor_dropdown',array('template' => 'web2print/editor/dropdown.phtml'))->setVarNum($i)->setItem($item)->toHtml();
                break;

                case "checkbox":
                $html .= $this->getLayout()->createBlock('Mage_Core_Block_Template','editor_checkbox',array('template' => 'web2print/editor/checkbox.phtml'))->setVarNum($i)->setItem($item)->toHtml();
                break;

                case "longtext":
                $html .= $this->getLayout()->createBlock('Mage_Core_Block_Template','editor_textarea',array('template' => 'web2print/editor/textarea.phtml'))->setVarNum($i)->setItem($item)->toHtml();
                break;

                default:
                $html .= $this->getLayout()->createBlock('Mage_Core_Block_Template','editor_input',array('template' => 'web2print/editor/input.phtml'))->setVarNum($i)->setItem($item)->toHtml();
                break;
            }

            $i++;
        }

        $html .= "</table>";
        $html .= "</form>";

        return $html;
    }

    /**
     * @return 	string	html for the add to/update concept button
     */
    public function getConceptActionButtonHtml() {
        if(Mage::helper('web2print')->getIsConceptEnabled() && Mage::helper('customer')->isLoggedIn()){
        $html = '<span><button id="btn-concept" type="button" class="button"><span><span>';

        if ($this->_editor->getMode() == 'concept') {
            $html .= $this->__('Update Concept');
        } else {
            $html .= $this->__('Add to Concepts');
        }

        $html .= '</span></span></button></span>';
        }
        else{
            $html ="";
        }
        return $html;
    }

    /**
     * @return 	string	html for the add to/update shopping cart button
     */
    public function getShoppingcartActionButtonHtml() {

        $confirmationRequired = Mage::helper('web2print')->getCartConfirmationSetting();
        $html = '';

        if ($confirmationRequired) {
            $html .= '<label for="confirm_addtocart">' . $this->__('Confirm your design: ') . '</label>';
            $html .= '<input type="checkbox" name="confirm_addtocart" id="confirm_addtocart" checked="false" autocomplete="off"/>';
            $html .= '<button id="btn-cart" type="button" disabled="disabled" class="button disabled"><span><span>';
        } else {
            $html .= '<button id="btn-cart" type="button" class="button"><span><span>';
        }

        if ($this->_editor->getMode() == 'quoteitem') {
            $html .= $this->__('Save & back to shoppingcart');
        } else {
            $html .= $this->__('Add to Shopping Cart');
        }

        $html .= '</span></span></button>';

        return $html;
    }

    /**
     * @return options based on the post data or from the quote object
     */
    public function getProductOptionsData() {

        if ($this->_editor->getConcept()) {
            return unserialize($this->_editor->getConcept()->getOptions());

        } elseif ($this->_editor->getQuoteItem()) {
            $quoteItem = $this->_editor->getQuoteItem();
            $quoteItem = Mage::getModel('sales/quote_item')->load($quoteItem->getId());
            $quoteItem = Mage::getModel('sales/quote')->load($quoteItem->getQuoteId())->getItemById($quoteItem->getId());

            $options = $quoteItem->getBuyRequest()->toArray();

            unset($options['uenc']);
            unset($options['confirm_addtocart']);
            unset($options['original_qty']);

        } else {
            $options = $this->getRequest()->getPost();
        }
        $options['product_id'] = $this->_editor->getProduct()->getId();
        $options['chili_document_id'] = $this->_editor->getChiliDocumentId();
        array_walk_recursive($options, function (&$value) {
            $value = rawurlencode($value);
        });

        return $options;
    }

    public function getConceptUrl() {
        if ($this->_editor->getMode() == 'concept') {
            return $this->getUrl('web2print/concept/update', array('id' => $this->_editor->getConcept()->getId()));
        } else {
            return $this->getUrl('web2print/concept/add');
        }
    }

    public function getCartUrl() {
        if ($this->_editor->getMode() == 'quoteitem') {
            return $this->getUrl('checkout/cart/updateItemOptions', array('id' => $this->_editor->getQuoteItem()->getId()));
        } else {
            return $this->helper('checkout/cart')->getAddUrl($this->_editor->getProduct());
        }
    }

}