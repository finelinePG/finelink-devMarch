<?php

class Chili_Web2print_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{

    /**
     * Get item configure url to editor
     * If the product has any document reference id to CHILI the editor should be the url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        // Default behavior
        $url = 'checkout/cart/configure';
        $item = $this->getItem();
        $params = array('id' => $item->getId());

        // Change configure URL in case of a CHILI enabled product
        if ($item->getOptionByCode('chili_document_id')) {
            $url = 'web2print/editor/load';
            $params['type'] = 'quoteitem';
        }

        return $this->getUrl($url, $params);
    }

}
