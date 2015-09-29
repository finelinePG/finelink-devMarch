<?php

class Chili_Web2print_Model_Api extends Mage_Core_Model_Abstract
{
    /**
     * Defining constants webservice functions
     */
    const CHILI_GET_PERSONAL_DOCUMENT     = 'ResourceItemCopy';
    const CHILI_MOVE_RESOURCE_ITEM        = 'ResourceItemMove';
    const CHILI_GET_RESOURCE_IMAGE_URL    = 'ResourceItemGetURLForAnonymousUser';
    const CHILI_GET_RESOURCE_LIST         = 'ResourceList';
    const CHILI_SEARCH_FOR_RESOURCE       = 'ResourceSearch';
    const CHILI_CREATE_PDF                = 'DocumentCreatePDF';
    const CHILI_CREATE_PDF_TASK_PRIORITY  = 1;
    const CHILI_GET_RESOURCE_ITEM_XML     = 'ResourceItemGetXML';
    const CHILI_GET_RESOURCE_ITEM_BY_NAME = 'ResourceItemGetByName';
    const CHILI_GET_TASK_STATUS           = 'TaskGetStatus';
    const CHILI_GET_TASK_LIST             = 'TasksGetList';
    const CHILI_GET_RESOURCE_TREE         = 'ResourceGetTree';
    const CHILI_GET_RESOURCE_TREE_LEVEL   = 'ResourceGetTreeLevel';
    const CHILI_GET_EDITOR_URL            = 'DocumentGetEditorURL';
    const CHILI_GET_DOCUMENT_VARIABLES_DEFINITIONS  = 'DocumentGetVariableDefinitions';
    const CHILI_SET_WORKSPACE_ADMINISTRATION = 'SetWorkspaceAdministration';

    private $environmentUrl;
    private $environmentWsdl;
    private $environmentUsername;
    private $environmentPassword;
    private $_apiExpireTime;
    private $_websiteId;
    private $_website;

    private $_accessible = false;

    public $instance;
    public $apiKey;
    
    private $_exception = null;

    public function __construct() {
        // add caching
        ini_set('soap.wsdl_cache_enabled', Mage::helper('web2print/data')->getChiliCaching()); 
        ini_set('soap.wsdl_cache_ttl', 86400);

        $this->environmentUrl		    = Mage::helper('web2print/data')->getChiliEnvironment();
        $this->environmentWebserviceUrl	= Mage::helper('web2print/data')->getChiliWebserviceUrl();
        $this->environmentWsdl		    = Mage::helper('web2print/data')->getChiliWsdl();
        $this->environmentUsername	    = Mage::helper('web2print/data')->getChiliUsername();
        $this->environmentPassword	    = Mage::helper('web2print/data')->getChiliPassword();

        //0 = default website
        $this->_websiteId = 0;
        $this->_website = null;
        
        //Get soap instance
        $this->getInstance();
        
        $this->getApiKey();

        parent::_construct();
        $this->_init('web2print/api');
        
    }
    
    public function isServiceAvailable() {
        if ($this->instance instanceof SoapClient) {
            return true;
        }
        return false;
    }
    
    /**
     * Loads store specific website configuration for webservice
     * @param type $websiteId 
     */
    public function setWebsite($websiteId){
        if($websiteId){
            $website = Mage::getModel('core/website')->load($websiteId);
            //Set the website to this website id
            $this->_websiteId = $website->getId();
            $this->_website = $website;
        }else{
            $website = null;
        }

        $this->environmentUrl		= Mage::helper('web2print/data')->getChiliEnvironment($website);
        $this->environmentWebserviceUrl	= Mage::helper('web2print/data')->getChiliWebserviceUrl($website);
        $this->environmentWsdl		= Mage::helper('web2print/data')->getChiliWsdl($website);
        $this->environmentUsername	= Mage::helper('web2print/data')->getChiliUsername($website);
        $this->environmentPassword	= Mage::helper('web2print/data')->getChiliPassword($website);
        
        $this->getApiKey();
    }
        
    /**
     * Connects to the chili webservice
     * @throws Exception Not valid API key
     */
    public function connect() {
        if ($this->instance === false) {
            return null;
        }
        $apiKeyArray = Mage::getSingleton('core/session')->getChiliApiKeyArray();
        
        if($this->_website){
            //If website exists, check config
            if(Mage::helper('web2print/data')->isSameAsDefaultConfig($this->_website)){
                //If config is same as default, use default api key
                if(!array_key_exists(0, $apiKeyArray) && time() >= strtotime($apiKeyArray[0]['expireTime'])){
                    $apiKeyArray[0] = $this->_generateApiKey();
                }
                $apiKeyArray[$this->_website->getId()] = $apiKeyArray[0];
            }else{
                //If not the same, generate and save new api key
                $apiKeyArray[$this->_website->getId()] = $this->_generateApiKey();
            }
        }else{
            //Create (default) api key as [0] element of array
            $apiKeyArray[0] = $this->_generateApiKey();
        }
            
        Mage::getSingleton('core/session')->setChiliApiKeyArray($apiKeyArray);
    }
    
    /**
     * Generate new API key for the current initialized website. 
     * @return string apiKey
     * @throws Exception 
     */
    private function _generateApiKey(){
        if ($this->instance === false) {
            return null;
        }
        // ask for an api key
        $array = array(
            'environmentNameOrURL' => $this->environmentUrl,
            'userName' => $this->environmentUsername,
            'password' => $this->environmentPassword,
        );
        //get Key
        $key = $this->instance->GenerateApiKey($array);

        $result = array();
        $result['key'] = $this->_getDomResult($key->GenerateApiKeyResult, 'apiKey', 'key');
        $result['expireTime'] = $this->_getDomResult($key->GenerateApiKeyResult, 'apiKey', 'validTill');
        
        if($result['key'] == ''){
            throw new Exception('Could not get valid API key from webservice. Not a valid combination of environment, username and password');
        }
        
        return $result;
    }

    /**
     * Creates a new soap client instance
     * @return object SoapClient
     * @todo add check to ensure the soapclient is only created once
     */
    public function getInstance() {
        if ($this->instance === null) {
            // Make sure the SOAP location is properly set
            $soapParameters['location'] = $this->environmentWebserviceUrl;

            try {
                $this->instance = @new SoapClient($this->environmentWsdl, $soapParameters);
            }catch(Exception $e) {
                $this->instance = false;
                Mage::helper('web2print/data')->log("Webservice Exception: ".print_r($e->getMessage(),true));
            }
        }
        return $this->instance;
    }
        
    /**
     * Get api key of webservice
     * @return string $apiKey 
     */
    public function getApiKey() { 

        $apiKeyArray = Mage::getSingleton('core/session')->getChiliApiKeyArray();
        
        //Check if array is in session
        if(is_array($apiKeyArray) && array_key_exists($this->_websiteId, $apiKeyArray)){
            $apiKey = $apiKeyArray[$this->_websiteId]['key'];
            $apiKeyExpireTime = $apiKeyArray[$this->_websiteId]['expireTime'];
        }else{
            $apiKey = null;
        }
	
        if( !$apiKey || (time() >= strtotime($apiKeyExpireTime)) ) {
            $this->connect();
        }
        
        return $apiKey;
    }
	
    /**
     * Webservice call function
     * @param string $method
     * @param array $settings
     * @return string $response
     */
    protected function callFunction($method, $settings){
        $functionResponse = '';
        if ($this->instance === false) {
            return null;
        }
        Mage::dispatchEvent('web2print_api_call_before', array('method'=>$method, 'settings'=>$settings));
        
        try {
            Mage::helper('web2print/data')->log('------------ Start call ' . $method .' ------------');
            Mage::helper('web2print/data')->log($settings);
            $functionResponse = $this->instance->$method($settings);
            Mage::helper('web2print/data')->log(print_r($functionResponse, true));
        }catch(Exception $e) {
            Mage::helper('web2print/data')->log("Webservice Exception: ".print_r($e->__toString(),true));
            $functionResponse = null;
            $this->_exception = $e;
            
        }
		        
        Mage::dispatchEvent('web2print_api_call_after', array('method'=>$method, 'settings'=>$settings, 'response'=>$functionResponse));
        		
        return $functionResponse;
    }

    /**
     * create new document based on a document
     */
    public function getPersonalDocument($documentId, $documentName, $resourceName = 'Documents' ) {
        if ($this->instance === false) {
            return null;
        }        
        $array = array(
            'apiKey' => $this->getApiKey(),
            'resourceName' => $resourceName,
            'itemID' => $documentId,
            'newName' => $documentName,
            'folderPath' => Mage::helper('web2print/data')->getChiliSavePath('temp'),
        );

        $document = $this->callFunction(self::CHILI_GET_PERSONAL_DOCUMENT, $array);
        return $this->_getDomResult( $document->ResourceItemCopyResult, 'item', 'id');
    }
    
    /**
     * @param type $documentId
     * @param type $documentName
     * @param type $resourceName
     * @param type $type
     * @return type 
     */
    public function moveResourceItem( $documentId, $documentName, $resourceName = 'Documents', $type = 'quote' ) {
        $array = array(
            'apiKey' => $this->getApiKey(),
            'resourceName' => $resourceName,
            'itemID' => $documentId,
            'newName' => $documentName,
            'newFolderPath' => Mage::helper('web2print/data')->getChiliSavePath( $type ),
        );

        $document = $this->callFunction(self::CHILI_MOVE_RESOURCE_ITEM, $array);
        $newDocumentId = $this->_getDomResult( $document->ResourceItemMoveResult, 'item', 'id');
        
        return $newDocumentId;
    }
    
    /**
     * get the image url of document
     * @param type $documentId
     * @param type $type
     * @param type $resourceName
     * @param type $pageNum
     * @return string imageUrl 
     */
    public function getResourceImageUrl($documentId, $type, $resourceName = 'Documents', $pageNum = '1'){ 
        if($this->getApiKey()){
            $array = array(
                'apiKey'       => $this->getApiKey(),
                'resourceName' => $resourceName,
                'itemID'       => $documentId,
                'type'         => $type,
                'pageNum'      => $pageNum                
            );
        
            $urlDocument = $this->callFunction(self::CHILI_GET_RESOURCE_IMAGE_URL, $array);
            
            if($urlDocument) {
                $url = $this->_getDomResult($urlDocument->ResourceItemGetURLForAnonymousUserResult, "urlInfo", "url");
                return $url."&t=".time();
            }else{
                return null;
            }
        }
    }

    /**
     * @deprecated
     */
    public function getResourceList(){
        if($this->getApiKey()){
            $array = array(
                'apiKey'       => $this->getApiKey()           
            );

            $result = $this->callFunction(self::CHILI_GET_RESOURCE_LIST, $array);
        }
    }

    /**
     * @done
     * @param type $resourceName
     * @param type $name
     * @return type 
     */
    public function searchForResource($resourceName = 'Documents', $name = ''){
        if($this->getApiKey()){
            $array = array(
                'apiKey'       => $this->getApiKey(),
                'resourceName' => $resourceName,
                'name'       => $name             
            ); 
        }
        
        $result = $this->callFunction(self::CHILI_SEARCH_FOR_RESOURCE, $array);

        return $result->ResourceSearchResult;
    }        
    
    /**
     * create pdf from document
     * @param pdf object
     */
    public function createPdfTask($pdf){    
        $order = Mage::getModel('sales/order')->loadByIncrementId($pdf->getOrderIncrementId());
        $this->setWebsite($order->getStore()->getWebsiteId());
        
        $errorMessage = "";
        $pdfRecordIsValid = true;

        if ($pdf->getDocumentId() == "") {
            $errorMessage .= 'No Document ID supplied';
            $pdfRecordIsValid = false;
        }
        
        if ($pdf->getExportProfile() == "") {
            $errorMessage .= 'No ExportProfile supplied';
            $pdfRecordIsValid = false;
        }
        
        if(!$pdfRecordIsValid) {
            $pdf->setStatus('error-data-incomplete');
            $pdf->setMessage($errorMessage);
            $pdf->setUpdatedAt(date("Y-m-d H:i:s"));
            $pdf->save();
        }

        if($pdfRecordIsValid && $this->getApiKey()){
            if($pdf->getDocumentId() && $pdf->getExportProfile()){
                $xml = $this->getResourceItemXML(Mage::helper('web2print/data')->getItemId($pdf->getExportProfile()), 'PdfExportSettings');
                $result = null;
                if($xml !== null){
                    $array = array(
                        'apiKey'       => $this->getApiKey(),
                        'itemID'       => $pdf->getDocumentId(),
                        'settingsXML'  => $xml,
                        'taskPriority' => self::CHILI_CREATE_PDF_TASK_PRIORITY,
                    );
                
                    $result = $this->callFunction(self::CHILI_CREATE_PDF, $array);
                    
                }
                
                if (($xml == null || $result == null) && $this->_exception != null && $this->_exception instanceof \Exception) {
                    $pdf->setStatus('error-chili');
                    $pdf->setUpdatedAt(date("Y-m-d H:i:s"));
                    if (preg_match("/Item not found: Documents/i", $this->_exception->getMessage())) {
                        $pdf->setMessage('Document not found on Chili server');
                    } elseif (preg_match("/Item not found: PdfExportSettings/i", $this->_exception->getMessage())) {
                        $pdf->setMessage('PdfExportSettings does not exists.');
                    } else {
                        $pdf->setMessage($this->_exception->getMessage());
                    }
                    $pdf->save();
                    $this->_exception = null;
                    return $pdf->getStatus();
                }

                if($result !== null){
                    $taskId = $this->_getDomResult($result->DocumentCreatePDFResult, 'task', 'id');
                    
                    if($taskId){
                        $pdf->setTaskId($taskId);
                        $pdf->setStatus('requested');
                    }else{
                        $pdf->setStatus('error-create-task-no-task-id');
                    }
                }else{
                    $pdf->setStatus('error-create-task-failed');
                }
                
                $pdf->setUpdatedAt(date("Y-m-d H:i:s"));
                $pdf->save();
            }
        }
        
        return $pdf->getStatus();
    }

    /**
     * @param $documentId
     * @param $exportType
     *
     * @return int
     */
    public function createPdfTaskForAjax($documentId, $exportType)
    {
        $exportProfile = Mage::helper('web2print')->getItemId($exportType);
        $xml = $this->getResourceItemXML($exportProfile, 'PdfExportSettings');

        if (!$documentId || !$xml) {
            return false;
        }

        $array = array(
            'apiKey'       => $this->getApiKey(),
            'itemID'       => $documentId,
            'settingsXML'  => $xml,
            'taskPriority' => self::CHILI_CREATE_PDF_TASK_PRIORITY,
        );

        $result = $this->callFunction(self::CHILI_CREATE_PDF, $array);
        $taskId = $this->_getDomResult($result->DocumentCreatePDFResult, 'task', 'id');

        return $taskId;
    }

    /**
     * get resourceitem by id
     * @param type $itemId
     * @param type $resourceName
     */
    public function getResourceItemXML($itemId, $resourceName = 'Documents'){
        if($this->getApiKey()){
            $dom = null;
             
            $array = array(
                'apiKey'       => $this->getApiKey(),
                'itemID'       => $itemId,
                'resourceName' => $resourceName
            );

            $xml = $this->callFunction(self::CHILI_GET_RESOURCE_ITEM_XML, $array);
            
            if($xml !== null){
                $dom = $xml->ResourceItemGetXMLResult;  
            }
            
            return $dom;
        }
    }

    /**
     * @done
     * get resourceitem by name
     * @param type $itemName
     * @param type $resourceName
     * @return type 
     */
    public function getResourceItemByName($itemName, $resourceName = 'Documents'){
         if($this->getApiKey()){
            $array = array(
                'apiKey'       => $this->getApiKey(),
                'itemName'       => $itemName,
                'resourceName' => $resourceName
            );

            $xml = $this->callFunction(self::CHILI_GET_RESOURCE_ITEM_BY_NAME, $array);
            $dom = $xml->ResourceItemGetByNameResult;  

            return $dom;
         }
    }

    /**
     * get task status
     * @done
     * @param type $taskID
     * @return type g
     */
    public function getTaskStatus($taskID){
        if($this->getApiKey()){
        	$array = array(
                'apiKey'       => $this->getApiKey(),
                'taskID'       => $taskID
            );

            $taskstatus = $this->callFunction(self::CHILI_GET_TASK_STATUS, $array);
            return $taskstatus->TaskGetStatusResult;   
        }
    }

    /**
     * get task list
     * @done
     * @param type $includeRunningTasks
     * @param type $includeWaitingTasks
     * @param type $includeFinishedTasks
     * @return type 
     */
    public function getTaskList($includeRunningTasks = 'false', $includeWaitingTasks = 'false', $includeFinishedTasks = 'true'){
         if($this->getApiKey()){
            $array = array(
                'apiKey'       => $this->getApiKey(),
                'includeRunningTasks'       => $includeRunningTasks,
                'includeWaitingTasks'       => $includeWaitingTasks,
                'includeFinishedTasks'      => $includeFinishedTasks
            );

             $tasksXML = $this->callFunction(self::CHILI_GET_TASK_LIST, $array);

             return $tasksXML->TasksGetListResult;
         }
    }

    /**
     * get resource tree
     * @done
     * @param type $type
     * @return type 
     */
    public function getResourceTree($type){
        if($this->getApiKey()){
            $array = array(
                'apiKey' => $this->getApiKey(),
                'resourceName'=> $type,
                'parentFolder'=> '',
                'includeSubDirectories'=> true,
                'includeFiles'=> false,
            );
            $resourcesXML = $this->callFunction(self::CHILI_GET_RESOURCE_TREE, $array);
            return $resourcesXML->ResourceGetTreeResult;
        }
    }

    /**
     * get resource tree by level
     * @done
     * @param type $type
     * @return type 
     */
    public function getLevelResourceTree($type, $parentFolder = ''){
        if($this->getApiKey()){
            $array = array(
                'apiKey' => $this->getApiKey(),
                'resourceName'=> $type,
                'parentFolder'=> $parentFolder,
                'numLevels'=> 1,
            );
            $resourcesXML = $this->callFunction(self::CHILI_GET_RESOURCE_TREE_LEVEL, $array);
            return $resourcesXML->ResourceGetTreeLevelResult;
        }
    }

    /**
     * set user workspace rights
     */
    private function setAllowWorkspaceAdministration(){
        if($this->getApiKey()){
            $array = array(
                'apiKey' => $this->getApiKey(),
                'allowWorkspaceAdministration' => Mage::helper('web2print/data')->allowSimulateWorkspace(), //true or false
            );

            $result = $this->callFunction(self::CHILI_SET_WORKSPACE_ADMINISTRATION, $array);
        }
    }

    /**
     * get iframe source url of the document's editor
     * @param   string  document reference id
     */
    public function getEditorUrl( $documentId, $productId = false ) {
        $this->setAllowWorkspaceAdministration();

        $array = array(
            'apiKey' => $this->getApiKey(),
            'itemID' => $documentId,
            'workSpaceID' => Mage::helper('web2print/data')->getCurrentWorkspacePreference( $productId ),
            'viewPrefsID' => Mage::helper('web2print/data')->getCurrentViewPreference( $productId ),
            'constraintsID' => Mage::helper('web2print/data')->getCurrentDocumentConstraint( $productId ),
            'viewerOnly' => '',
            'forAnonymousUser' => false
        );

        $editorUrlRequestXml = $this->callFunction(self::CHILI_GET_EDITOR_URL, $array);
        $url = $this->_getDomResult($editorUrlRequestXml->DocumentGetEditorURLResult, "urlInfo", "url" );
        $url .= '&enableFolding=true';

        return $url;
    }

    /**
     * @done
     * get document variables
     */
    public function getDocumentVariableDefinitions($itemId){
        if($this->getApiKey()){
            $array = array(
                'apiKey' => $this->getApiKey(),
                'itemID' => $itemId
            );

            $variableDefinitions = $this->callFunction(self::CHILI_GET_DOCUMENT_VARIABLES_DEFINITIONS, $array);
            if($variableDefinitions){
                return $variableDefinitions->DocumentGetVariableDefinitionsResult;
            }
        }
    }

	/**
	 * return the xml result for any chili request
	 * @param	xml result
	 * @param	string tag
	 * @param	string attribute
	 * @param	int item
	 * @return	string result
	 */
	private function _getDomResult( $result, $tag, $attribute, $item = 0 )
    {
        try {
            $dom = new DOMDocument();
            $dom->loadXML( $result );
            return $dom->getElementsByTagName( $tag )->item($item)->getAttribute($attribute);
        } catch(Exception $e) {
            throw new Exception("Unable to get dom result: ".$e->getMessage());
            return false;
        }

	}

    /**
     * prepare the savePath
     * @param   int id of the order / quote id
     * @param   string  type  
     */
    private function _prepareSavePath( $id, $type = 'quote' ) {
        
        $object = Mage::getModel( 'sales/' . ucfirst( $type ))->load( $id );
        $replacers = array(
            '%order_id%' => $object->getEntityId(),
            '%quote_id%' => $object->getEntityId(),
            '%customer_group%' => Mage::getSingleton('customer/session')->getCustomerGroupCode()
        );
        
        return Mage::helper('web2print/data')->getChiliSavePath( $type, $replacers );
    }

}
