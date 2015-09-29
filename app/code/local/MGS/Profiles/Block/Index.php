<?php

class MGS_Profiles_Block_Index extends Mage_Core_Block_Template {

    public function getProfiles() {
        $profiles = Mage::getModel('profiles/profile')->getCollection()
                ->addFieldToFilter('status', 0);
        return $profiles;
    }

}
