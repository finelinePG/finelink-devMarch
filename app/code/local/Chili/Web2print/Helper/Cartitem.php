<?php

class Chili_Web2print_Helper_Cartitem extends Mage_Core_Helper_Abstract
{


    /**
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getProductOptions(Mage_Sales_Model_Quote_Item $item)
    {
        $helper = Mage::helper('catalog/product_configuration');
        return $helper->getCustomOptions($item);
    }


    /**
     * Generate serialized product options as in POST or GET parameters
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getProductOptionsParameters(Mage_Sales_Model_Quote_Item $item)
    {
        $product = $item->getProduct();
        $options = $this->getProductOptions($item);

        $data = array();
        foreach ($options as $option) {
            $id = $option['option_id'];
            $objOption = $product->getOptionById($id);
            $objValues = $objOption->getValuesCollection()->getItems();
            $value = $this->_mapValueToOptionValue($option['value'], $objValues);

            // overwrite multi-options
            if (in_array($option['option_type'], array('multiple', 'checkbox'))) {
                $values = array_filter(explode(', ', $value));
                $value = array();
                foreach ($values as $optionId) {
                    $value[] = $this->_mapValueToOptionValue($optionId, $objValues);
                }
            }

            // overwrite date-time options
            if (in_array($option['option_type'], array('date_time', 'date', 'time'))) {
                $timestamp = strtotime($value);
                $value = array();
                if (in_array($option['option_type'], array('date_time', 'date'))) {
                    $value['month'] = date('n', $timestamp);
                    $value['day'] = date('j', $timestamp);
                    $value['year'] = date('Y', $timestamp);
                }
                if (in_array($option['option_type'], array('date_time', 'time'))) {
                    $value['hour'] = date('g', $timestamp);
                    $value['minute'] = ltrim(date('i', $timestamp), '0');
                    $value['minute'] = $value['minute'] ? $value['minute'] : '0';
                    $value['day_part'] = date('a', $timestamp);

                }
            }

            $data[$id] = $value;
        }

        return $data;
    }

    /**
     * Get the option_type_id  based on the entered option value
     *
     * @param $value
     * @param $optionValues
     *
     * @return mixed
     */
    protected function _mapValueToOptionValue($value, $optionValues)
    {
        foreach ($optionValues as $key => $option) {
            if ($option['title'] == $value) {
                return $option['option_type_id'];
            }
        }
        return $value;
    }

    /**
     * Get the productOptionParameters as URL encoded parameters
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string base64 encoded
     */
    public function getSerializedProductOptionsParameters(Mage_Sales_Model_Quote_Item $item)
    {
        $data = $this->getProductOptionsParameters($item);
        $result = '';
        foreach ($data as $key => $value) {
            $optionId = 'options[' . $key . ']';
            if (is_array($value)) {
                foreach ($value as $recordKey => $record) {
                    $optionRecordId = is_numeric($recordKey) ? $optionId . '[]' : $optionId . '[' . $recordKey . ']';
                    $result.= $optionRecordId . '=' . urlencode($record) . '&';
                }
            } else {
                $result.= $optionId . '=' . urlencode($value) . '&';
            }
        }
        return base64_encode(substr($result, 0, -1));
    }
}

