<?php

class MGS_Profiles_Block_Adminhtml_Profile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("profiles_form", array("legend" => Mage::helper("profiles")->__("Profile information")));


        $fieldset->addField("name", "text", array(
            "label" => Mage::helper("profiles")->__("Name"),
            "class" => "required-entry",
            "required" => true,
            "name" => "name",
        ));

        $fieldset->addField("detail", "textarea", array(
            "label" => Mage::helper("profiles")->__("Detail"),
            "class" => "required-entry",
            "required" => true,
            "name" => "detail",
        ));

        $fieldset->addField('photo', 'image', array(
            'label' => Mage::helper('profiles')->__('Photo'),
            'name' => 'photo',
            'note' => '(*.jpg, *.png, *.gif)',
        ));
        $fieldset->addField("position", "text", array(
            "label" => Mage::helper("profiles")->__("Position"),
            "name" => "position",
        ));

//        $fieldset->addField("bio", "textarea", array(
//            "label" => Mage::helper("profiles")->__("Bio"),
//            "name" => "bio",
//        ));

        $fieldset->addField("address", "text", array(
            "label" => Mage::helper("profiles")->__("Address"),
            //"class" => "required-entry",
            //"required" => true,
            "name" => "address",
        ));

        $fieldset->addField("email", "text", array(
            "label" => Mage::helper("profiles")->__("Email"),
            "class" => "required-entry",
            "required" => true,
            "name" => "email",
        ));

        $fieldset->addField("facebook", "text", array(
            "label" => Mage::helper("profiles")->__("Facebook"),
            "name" => "facebook",
        ));

        $fieldset->addField("twitter", "text", array(
            "label" => Mage::helper("profiles")->__("Twitter"),
            "name" => "twitter",
        ));

        $fieldset->addField("linkedin", "text", array(
            "label" => Mage::helper("profiles")->__("Linkedin"),
            "name" => "linkedin",
        ));

        $fieldset->addField("instagram", "text", array(
            "label" => Mage::helper("profiles")->__("Instagram"),
            "name" => "instagram",
        ));

        $fieldset->addField("google_plus", "text", array(
            "label" => Mage::helper("profiles")->__("Google Plus"),
            "name" => "google_plus",
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('profiles')->__('Status'),
            'values' => MGS_Profiles_Block_Adminhtml_Profile_Grid::getValueArray12(),
            'name' => 'status',
            "class" => "required-entry",
            "required" => true,
        ));

        if (Mage::getSingleton("adminhtml/session")->getProfileData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getProfileData());
            Mage::getSingleton("adminhtml/session")->setProfileData(null);
        } elseif (Mage::registry("profile_data")) {
            $form->setValues(Mage::registry("profile_data")->getData());
        }
        return parent::_prepareForm();
    }

}
