<?php
class MGS_Profiles_Block_Widget extends Mage_Core_Block_Template {

    public function getProfiles() {
		$profileIds = $this->getProfileIds();
		$profileIds = explode(',',$profileIds);
        $profiles = Mage::getModel('profiles/profile')->getCollection()
			->addFieldToFilter('profile_id', array('in'=>$profileIds))
			->addFieldToFilter('status', 0);
        return $profiles;
    }

}
