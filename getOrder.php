<?php
 
 $proxy = new SoapClient('http://magento.finelink.com/api/v2_soap/?wsdl', array('trace' => 1)); 
$sessionId = $proxy->login('soaper', 'F!neline25'); 

$result = $proxy->salesOrderInvoiceList($sessionId);
var_dump($result);

?>