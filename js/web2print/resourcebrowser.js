/**
 * Open generic resource browser for each type
 */
function openResourceBrowser(baseUrl,type,callback){
    new Ajax.Request(baseUrl, { 
        method:'get',
        parameters: 'jow=jow',
        onSuccess: function(transport, json){
            var response = transport.responseText.evalJSON();

            windowResource = new Window({className:'magento',title:'Resource browser',width:600,height:400,minimizable:false,maximizable:false,showEffectOptions:{duration:0.4},hideEffectOptions:{duration:0.4}});
            windowResource.setHTMLContent(response.result);
            windowResource.setZIndex(100);
            windowResource.showCenter(true);
            document.activeResourceWindow = windowResource.getId();
        }
    });
}
function openResourceBrowserProduct(baseUrl,type,callback){
    var websiteId = null;
    var websiteSelectCount = 0;
    var elements = Form.getElements($('product_edit_form'));
    for (var row in elements) {
        if (elements[row].id && elements[row].name == 'product[website_ids][]') {
            if(elements[row].checked){
                websiteSelectCount ++;
                websiteId = elements[row].value;
            }
        }
    }
    if(websiteSelectCount > 1){
        websiteId = null;
        alert('A web2print product can only contain to 1 website');
    }else{
        new Ajax.Request(baseUrl, { 
            method:'get',
            parameters: 'website='+websiteId,
            onSuccess: function(transport, json){
                var response = transport.responseText.evalJSON();

                windowResource = new Window({className:'magento',title:'Resource browser',width:600,height:400,minimizable:false,maximizable:false,showEffectOptions:{duration:0.4},hideEffectOptions:{duration:0.4}});
                windowResource.setHTMLContent(response.result);
                windowResource.setZIndex(100);
                windowResource.showCenter(true);
                document.activeResourceWindow = windowResource.getId();
            }
        });
    }
}


function resourceTreeTrigger(element, callback){   
    var childTree = element.up().childElements('ul');
    childTree.each(function(child) {
        if (child.hasClassName('open')) {
            child.removeClassName('open');
            child.addClassName('hidden');
        } else {
            if (!child.up().hasClassName('loaded')) {
                loadItems(child, callback);
                child.up().addClassName('loaded');
            }
            child.removeClassName('hidden');
            child.addClassName('open');
        }
    });
}


function resourceWindowSelect(val,element){
    var humanReadableArray = val.split('|');
    var humanReadableValue = humanReadableArray[0]
    
    $(element).value = val;
    $(element+'_value').value = humanReadableValue;
    Windows.close(document.activeResourceWindow);
}

function clearValue(element){
    var id =element.id+'_value';
    $(element).value = '';
    $(id).value = '';
}

function loadItems(element, callback) {
    new Ajax.Request(callback, { method:'get',
  onSuccess: function(transport, json){
      var response = transport.responseText.evalJSON();
      
      element.up().innerHTML += response.result;
  }
    });    

}