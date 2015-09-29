<?php

class Chili_Web2print_Model_Editor extends Mage_Core_Model_Abstract {

    protected $api;
    protected $_mode;
    protected $_product;
    protected $_quoteItem;
    protected $_concept;
    protected $_chiliEditorUrl;
    
    public function __construct() {
        parent::__construct();
        $this->api = Mage::getModel('web2print/api');
    }

    /**
     * @return mixed
     */
    public function isServiceAvailable() {
        return $this->api->isServiceAvailable();
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * @param $mode can be product, concept or quoteitem (create new product, update concept, update quote item)
     * @return $this
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

    /**
     * Return the current product the editor is working for
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @param $product
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Return the current quote item the editor is working for
     */
    public function getQuoteItem()
    {
        return $this->_quoteItem;
    }

    /**
     * @param $quoteItem
     */
    public function setQuoteItem($quoteItem)
    {
        $this->_quoteItem = $quoteItem;
        return $this;
    }

    /**
     * Return the current concept the editor is working for
     */
    public function getConcept()
    {
        return $this->_concept;
    }

    /**
     * @param $quoteItem
     */
    public function setConcept($concept)
    {
        $this->_concept = $concept;
        return $this;
    }

    /**
     * Return the current concept the editor is working for
     */
    public function getChiliDocumentId()
    {
        return $this->_chiliDocumentId;
    }

    /**
     * @param $chiliDocumentId
     */
    public function setChiliDocumentId($chiliDocumentId)
    {
        $this->_chiliDocumentId = $chiliDocumentId;
        return $this;
    }

    /**
     * @return string URL to Chili editor
     */
    public function getChiliEditorUrl()
    {
        return $this->_chiliEditorUrl;
    }

    /**
     * @return string URL to Chili editor
     */
    public function setChiliEditorUrl($url)
    {
        $this->_chiliEditorUrl = $url;
        return $this;
    }

    /**
     * All necessary Chili information is requested up front (editor URL + document ID and stored)
     * @param string $type
     * @return boolean
     */
    public function requestChiliEditorData($editType = 'product')
    {
        // Only request a new CHILI document is the user is performing an initial product edit
        if($editType == 'product')
        {
            $chiliDocumentId = Mage::helper('web2print')->getItemId($this->_product->getWeb2printDocumentId());
            $chiliDocumentName = $this->_product->getUrlKey();

            $workingChiliDocumentId = $this->api->getPersonalDocument($chiliDocumentId, $chiliDocumentName, 'Documents');

            $this->setChiliDocumentId($workingChiliDocumentId);
        }

        if($this->getChiliDocumentId() && $this->getProduct())
        {
            $editorUrl = $this->api->getEditorUrl($this->getChiliDocumentId(), $this->getProduct()->getId());
            $this->setChiliEditorUrl($editorUrl);
        }

        return true;
    }

}