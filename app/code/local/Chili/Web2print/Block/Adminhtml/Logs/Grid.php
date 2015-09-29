<?php

    class Chili_Web2print_Block_Adminhtml_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid{
        public function __construct(){
          
           $this->setId('logsGrid');
           $this->setDefaultSort('log_id');
           $this->setDefaultDir('DESC');
           
           $this->setSaveParametersInSession(true);
           parent::__construct();
          
        }
        protected function _prepareCollection() {
            $collection = Mage::getModel('web2print/log')->getCollection();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
        
        protected function _prepareColumns() {
            $this->addColumn('log_id', array(
                'header'    =>  Mage::helper('web2print')->__('ID'),
                'align'     =>  'right',
                'width'     =>  '50px',
                'index'     =>  'log_id'
            ));
            
            $this->addColumn('method', array(
                'header'    =>  Mage::helper('web2print')->__('Webservice method'),
                'align'     =>  'right',
                'width'     =>  '50px',
                'index'     =>  'method'
            ));
            
            $this->addColumn('parameters', array(
                'header'    =>  Mage::helper('web2print')->__('Webservice parameters'),
                'align'     =>  'left',
                'index'     =>  'parameters'
            ));
            
            $this->addColumn('created_at', array(
                'header'    =>  Mage::helper('web2print')->__('Created at'),
                'align'     =>  'left',
                'index'     =>  'created_at',
                'width'     =>  '50px',
                'type' => 'datetime'
            ));

            return parent::_prepareColumns();
        }
    }
?>
