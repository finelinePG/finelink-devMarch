var redirectUrl ='';

function ajaxPdfCall(url){
     new Ajax.Request(url, { method:'get',
        onSuccess: function(transport){
            var responseArray = transport.responseText.evalJSON();
            if(responseArray['status'] == 'success'){
                $('sales_order_view_tabs_order_documents_content').update(responseArray['result']);
            }else{
                alert('error occured while processing response');
            }
        }
    });
}