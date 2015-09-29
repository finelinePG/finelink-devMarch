<?php

    class Chili_Web2print_Block_Adminhtml_Pdfs_Grid extends Mage_Adminhtml_Block_Widget_Grid{
        public function __construct(){
          
           $this->setId('pdfsGrid');
           $this->setDefaultSort('pdf_id');
           $this->setDefaultDir('DESC');
           
           $this->setSaveParametersInSession(true);
           parent::__construct();
           
           try{
                Mage::getModel('web2print/pdf')->updatePdfs();
           }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/"));
           }
          
        }
        
        protected function _prepareCollection() {
            $collection = Mage::getModel('web2print/pdf')->getCollection();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
        
        protected function _prepareColumns() {
            $this->addColumn('pdf_id', array(
                'header'    =>  Mage::helper('web2print')->__('ID'),
                'align'     =>  'right',
                'width'     =>  '50px',
                'index'     =>  'pdf_id'
            ));
            
            
            
            $this->addColumn('status', array(
                'header'    =>  Mage::helper('web2print')->__('PDF status'),
                'align'     =>  'left',
                'index'     =>  'status',
                'type'     =>  'options',
                'options' => array(
                    'queued' => 'Queued',
                    'requested' => 'Requested',
                    'running' => 'Running',
                    'completed' => 'Completed',
                    'download-error' => 'Download Error',
                    'task-error' => 'Task Error',
                    'no-pdf-export-settings-found' => 'No PDF export settings found',
                    'error-data-incomplete' => 'Creating task failed, data error',
                    'error-chili' => 'CHILI error',
                    'error-create-task-no-task-id' => 'Creating task failed, no task ID',
                    'error-create-task-failed' => 'Creating task failed'
                    )
            ));
            
            $this->addColumn('path', array(
                'header'    =>  Mage::helper('web2print')->__('PDF path'),
                'align'     =>  'left',
                'index'     =>  'path',
                'renderer'  => 'Chili_Web2print_Block_Adminhtml_Pdfs_Grid_Renderer_DocumentUrl'
            ));
            
            $this->addColumn('order_id', array(
                'header'    =>  Mage::helper('web2print')->__('PDF Order ID'),
                'align'     =>  'left',
                'index'     =>  'order_increment_id'
            ));
            
            $this->addColumn('order_item_id', array(
                'header'    =>  Mage::helper('web2print')->__('PDF Orderitem id'),
                'align'     =>  'left',
                'index'     =>  'order_item_id'
            ));
            
            $this->addColumn('export_profile', array(
                'header'    =>  Mage::helper('web2print')->__('Export profile'),
                'align'     =>  'left',
                'index'     =>  'export_profile'
            ));
            
            $this->addColumn('export_type', array(
                'header'    =>  Mage::helper('web2print')->__('Export type'),
                'align'     =>  'left',
                'index'     =>  'export_type',
                'type'     =>  'options',
                'options' => array('frontend' => 'Frontend', 'backend' => 'Backend')
            ));
            
            $this->addColumn('doc id', array(
                'header'    =>  Mage::helper('web2print')->__('Document id'),
                'align'     =>  'left',
                'index'     =>  'document_id'
            ));
            
            $this->addColumn('task id', array(
                'header'    =>  Mage::helper('web2print')->__('Task id'),
                'align'     =>  'left',
                'index'     =>  'task_id'
            ));
            
            /*$this->addColumn('created_at', array(
                'header'    =>  Mage::helper('web2print')->__('Created at'),
                'align'     =>  'left',
                'index'     =>  'created_at',
                'type' => 'datetime'
            ));*/
            
            $this->addColumn('message', array(
                'header'    =>  Mage::helper('web2print')->__('Message'),
                'align'     =>  'left',
                'index'     =>  'message'
            ));
            
            $this->addColumn('updated_at', array(
                'header'    =>  Mage::helper('web2print')->__('Updated at'),
                'align'     =>  'left',
                'index'     =>  'updated_at',
                'type' => 'datetime'
            ));
             
            
            return parent::_prepareColumns();
        }
        
        
         protected function _prepareMassaction()
         {
             $this->setMassactionIdField('pdf_id');
             $this->getMassactionBlock()->setFormFieldName('pdf');

//             $this->getMassactionBlock()->addItem('download', array(
//                'label'    => Mage::helper('web2print')->__('Download PDF\'s'),
//                'url'      => $this->getUrl('*/*/massDownload'),
//             ));
             
//             $this->getMassactionBlock()->addItem('regenerate', array(
//             'label'    => Mage::helper('web2print')->__('Regenerate PDF\'s'),
//             'url'      => $this->getUrl('*/*/massRegenerate'),
//             ));

//             $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('web2print')->__('Remove PDF\'s'),
//             'url'      => $this->getUrl('*/*/massDel'),
//             ));

             return $this;
         }
         
         public function getRowUrl($row) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getOrderId()));
        }
    }
?>
