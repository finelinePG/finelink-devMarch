<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.10.9
 * @license:     bJ9U1uR7Gejdp32uEI9Z7xOqHZ5UnP25Ct3xHTMyeC
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Model_Permissions_Order extends Aitoc_Aitpermissions_Model_Permissions_Abstract
{
    /**
     * @param null $roleId
     *
     * @return bool
     */
    public function canManageOrdersOwnProductsOnly($roleId = null)
    {
        return $this->getPermission('manage_orders_own_products_only', $roleId) &&
            $this->getPermission('can_edit_own_products_only', $roleId);
    }


    /**
     * Get ids for items (order, invoice, etc) for customer
     *
     * @param $itemName
     *
     * @return array|bool
     */
    public function getIdsForOwnerByItemsName($itemName)
    {
        $products = Mage::getModel('catalog/product')->getCollection();
        $idSubAdmin = Mage::getSingleton('admin/session')->getUser()->getId();

        $products->addAttributeToFilter('created_by', $idSubAdmin);
        $fieldParentName = 'parent_id';
        if($itemName == 'order')
        {
            $fieldParentName = 'order_id';
        }
        $select = $products->getSelect();
        $select->joinInner(
            array('items_table' => Mage::getSingleton('core/resource')->getTableName('sales_flat_'.$itemName.'_item')),
            'e.entity_id = items_table.product_id',
            array('items_table.'.$fieldParentName)
        );
        $idsFilter = array();
        foreach($select->query()->fetchAll() as $item)
        {
            $idsFilter[] = $item[$fieldParentName];
        }
        return $idsFilter;
    }
}