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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// old tables
$ddqRuleTable = 'mageparts_ddq_rule';
$ddqRuleStoreTable = 'mageparts_ddq_rule_store';

$readAdapter = $installer->getConnection('core_read');
$writeAdapter = $installer->getConnection('core_write');

try {
    // set current store id, otherwise we cannot modify existing product records
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    // convert configuration values
    $mapSettings = array(
        'ddq/default_settings/qty_values'           => 'ddq/quantity_options/quantities',
        'ddq/default_settings/preselected'          => 'ddq/quantity_options/preselected',
        'ddq/default_settings/hide_unavailable_qty' => 'ddq/quantity_options/hide_unavailable_qty',
        'ddq/automatic_list_gen/enabled'            => 'ddq/incremental/enabled',
        'ddq/automatic_list_gen/field'              => 'ddq/incremental/field',
        'ddq/automatic_list_gen/option_limit_type'  => 'ddq/incremental/option_limit_type',
        'ddq/automatic_list_gen/option_limit'       => 'ddq/incremental/custom_option_limit',
        'ddq/drop_down/option_prefix'               => 'ddq/layout/label_prefix',
        'ddq/drop_down/option_suffix'               => 'ddq/layout/label_suffix',
        'ddq/drop_down/use_top_option'              => 'ddq/layout/select_header',
        'ddq/drop_down/top_option_title'            => 'ddq/layout/select_header_text',
        'ddq/price_updates/enabled'                 => 'ddq/general/price_updates_enabled'
    );

    $stores = Mage::app()->getStores();

    if (!isset($stores[0])) {
        $stores[0] = 'default';
    }

    if (count($stores)) {
        foreach ($stores as $storeId => $storeVal) {

            foreach ($mapSettings as $origPath => $upgradePath) {
                $data = Mage::getStoreConfig($origPath, $storeId);
                $defaultData = Mage::getStoreConfig($origPath, Mage_Core_Model_App::ADMIN_STORE_ID);

                if (!is_null($data) && ($storeId == Mage_Core_Model_App::ADMIN_STORE_ID || $data !== $defaultData)) {
                    if ($origPath == 'ddq/default_settings/qty_values') {
                        // create quantity list array
                        $qtys = explode(',', $data);

                        $data = array();

                        if (is_array($qtys) && count($qtys)) {
                            foreach ($qtys as $qty) {
                                $data[Mage::helper('ddq')->getQtyKey($qty)] = array(
                                    'qty' => $qty
                                );
                            }
                        }

                        $data = serialize($data);
                    }

                    Mage::app()->getConfig()->saveConfig($upgradePath, $data, ($storeId > 0 ? 'stores' : 'default'), $storeId);
                }
            }
        }
    }

    // convert product specific settings to new version
    if ($readAdapter && $writeAdapter && $installer->getConnection()->isTableExists($ddqRuleTable) && $installer->getConnection()->isTableExists($ddqRuleStoreTable)) {
        // retrieve rules from old table
        $rules = $readAdapter->fetchAll('SELECT * FROM `' . $ddqRuleTable . '`');

        if (count($rules)) {
            foreach ($rules as $rule) {
                // retrieve store connections for old rule
                $stores = $readAdapter->fetchAll('SELECT *  FROM `' . $ddqRuleStoreTable . '` WHERE `rule_id` = "' . $rule['rule_id'] . '"');

                if (count($stores)) {
                    foreach ($stores as $store) {
                        /* @var $product Mage_Catalog_Model_Product */
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId($store['store_id'])
                            ->load($rule['product_id']);

                        if ($product && $product->getId()) {
                            $enabled = false;

                            $qtyListConfig = $rule['use_config_qtys'] == 1 ? true : false;
                            $preselectedConfig = $rule['use_config_preselected'] == 1 ? true : false;

                            if (!$qtyListConfig) {
                                $list = array();

                                if ($rule['use_ddq_value'] == 1) {
                                    $enabled = 0;
                                } else if ($rule['use_ddq_value'] == 2) {
                                    $enabled = 1;
                                }

                                // create quantity list array
                                $qtys = explode(',', $rule['qtys']);

                                if (is_array($qtys) && count($qtys)) {
                                    foreach ($qtys as $qty) {
                                        $list[Mage::helper('ddq')->getQtyKey($qty)] = array(
                                            'qty' => $qty
                                        );
                                    }
                                }

                                $list = Mage::helper('ddq')->cleanQuantityData($list);

                                $product->setDdqQtyList(serialize($list));
                            } else {
                                $product->setDdqQtyList(false);
                            }

                            if (!$preselectedConfig) {
                                $product->setDdqPreselected($rule['preselected']);
                            } else {
                                $product->setDdqPreselected(false);
                            }

                            // update quantity attribute on product to match old rule settings
                            $product->setDdqEnabled($enabled)
                                ->setDdqHideUnavailableQty((int) $rule['hide_unavailable_qty'])
                                ->setDdqIncremental((int) $rule['automatic_list_gen'])
                                ->setDdqLayout(false)
                                ->save();
                        }
                    }
                }

                // delete old rule record
                $installer->deleteTableRow($ddqRuleTable, 'rule_id', $rule['rule_id']);
                $installer->deleteTableRow($ddqRuleStoreTable, 'rule_id', $rule['rule_id']);
            }

            // remove old tables
            $writeAdapter->dropTable($ddqRuleTable);
            $writeAdapter->dropTable($ddqRuleStoreTable);
        }
    }

    // enable extension cache
    $cacheTypes = Mage::app()->useCache();
    $cacheTypes['mageparts_ddq'] = 1;

    if (!array_key_exists('mageparts_base', $cacheTypes)) {
        $cacheTypes['mageparts_base'] = 1;
    }

    Mage::app()->saveUseCache($cacheTypes);

    // clean cache
    Mage::app()->cleanCache();
} catch (Exception $e) {
    die("Something went wrong while the upgrade of the Drop-Down Quantity extension was being performed. To prevent further errors that could damage the settings of your products we have halted the upgrade. To temporarily disable the extension you can rename the file app/etc/modules/MageParts_Ddq.xml, add a hyphen, underscore or similar to the end of the filename, or simply move the file from that directory. Please contact us at info@mageparts.com and we will help you resolve this right away, please include this in your message to us: " . $e->getMessage());
}

$installer->endSetup();
