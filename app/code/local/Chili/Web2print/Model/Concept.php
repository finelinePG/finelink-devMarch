<?php
class Chili_Web2print_Model_Concept extends Mage_Core_Model_Abstract {

    public function _construct()
    {
        parent::_construct();
        $this->_init('web2print/concept');
    }

    public function storeConcept($product, $session, $chiliDocumentId, $customOptions)
    {
        if($product->getId() != "" && $chiliDocumentId != "") {
            $this->setProductId($product->getId())
                ->setCustomerId($session->getId())
                ->setStoreId(Mage::app()->getStore()->getId())
                ->setChiliId($chiliDocumentId)
                ->setOptions($customOptions)
                ->save();

        } else {
            throw new Exception("Invalid parameters supplied");
        }
    }

}