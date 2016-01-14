function editorLoad(action,iFrameInversedHeight)
{
    $$('.editor_input').invoke('observe', 'keyup', function(e){   
        var varnum = e.target.name;
        SetVariableValue(varnum, e.target, 'input');        
    }); 
    
    $$('.editor_select').invoke('observe', 'change', function(e){   
        var varnum = e.target.name;
        SetVariableValue(varnum, e.target, 'list');        
    });
    
    $$('.editor_checkbox').invoke('observe', 'change', function(e){   
        var varnum = e.target.name;
        SetVariableValue(varnum, e.target, 'checkbox');        
    });
    
    $$('.editor_textarea').invoke('observe', 'keyup', function(e){   
        var varnum = e.target.name;
        SetVariableValue(varnum, e.target, 'textarea');        
    });
    
    if(action == 'edit'){
        var form = $('editorForm');
        if(form != null){
            form.disable();
        }
    }
    
    // adapt height of the iframe
   var height = getWindowHeight()-iFrameInversedHeight;
   height = height+'px';
   if($('iframe')) {
       $('iframe').setStyle({'height': height});
   }

    //show popup
    if($('configure-options')) {
        $('configure-options').observe('click', function(e) {
            if($('productOptions').getStyle('display') == 'none') {
                $('productOptions').setStyle({'display': 'block'});

                if(Prototype.Browser.IE == true) {
                    $('iframe').setStyle({'display': 'none'});
                }
            } else {
                $('productOptions').setStyle({'display': 'none'});

                if(Prototype.Browser.IE == true) {
                    $('iframe').setStyle({'display': 'block'});
                }
            }
            e.stop();
        });
    }

    //hide popup
    if($('save-options')) {
        $('save-options').observe('click', function(e) {
            $('productOptions').setStyle({'display': 'none'});

            if(Prototype.Browser.IE == true) {
                $('iframe').setStyle({'display': 'block'});
            }
            e.stop();
        });
    }

    if($('confirm_addtocart')) {
        $('confirm_addtocart').observe('change', function(e){
            if(e.target.checked){
                $('btn-cart').enable();
                $('btn-cart').removeClassName('disabled');
            }else{
                $('btn-cart').disable();
                $('btn-cart').addClassName('disabled');
            }
        });
    }
}

/**
 * Get the current height of the window and adapt the height of the iframe
 */
function getWindowHeight()
{
    var myHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
    	//Non-IE
        myHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        myHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
    	myHeight = document.body.clientHeight;
    }
	return myHeight;
}

function SetVariableValue(varNum,target,targetType)
{
    if(window.fullyLoaded) {
        var val = "";
        switch (targetType)
        {
           case "checkbox":
                if (target.checked)
                    val = "true";
                else
                    val = "false";
                break;
            case "list":
                val = target.options[target.selectedIndex].value;
                break;
            default:
                val = target.value;
        }
    
        window.editor.SetProperty("document.variables[" + varNum + "]","value",val);  
    }
}

function setVariableAllValues(){
    $$('.editor_input').each(function(e){
        var varnum = $(e).getAttribute('name');
        SetVariableValue(varnum, $(e), 'input');        
    });

    $$('.editor_select').each(function(e){
        var varnum = $(e).getAttribute('name');
        SetVariableValue(varnum, $(e), 'list');        
    }); 
    
    $$('.editor_checkbox').each(function(e){
        var varnum = $(e).getAttribute('name');
        SetVariableValue(varnum, $(e), 'checkbox');        
    }); 
    
    $$('.editor_textarea').each(function(e){
        var varnum = $(e).getAttribute('name');
        SetVariableValue(varnum, $(e), 'textarea');        
    }); 
}

/*
 * Get navigation html
 */
function getNavigationMenu(){
   var numPages = window.editor.GetNumPages();
   var navigationHtml = '';
   if(numPages > 1){
       $('headerNav').setStyle({'display': 'block'});
       for (var i = 1; i <= numPages; i++) {
            var pageNum = i - 1;
            var title = window.editor.GetObject("document.pages[" + pageNum + "].bookMarkTitle");
            if(title != ''){
              navigationHtml += "<a href='#' id='"+i+"' onclick='setPage("+i+");return false;' class='item'>"+title+"</a>";  
            }else{
              navigationHtml += "<a href='#' id='"+i+"' onclick='setPage("+i+");return false;' class='item'>Page "+i+"</a>";  
            }
       }
   }

   //insert html into div
   $('headerNav').insert({after: navigationHtml});
}

/*
 * Change the editor's page
 */
function setPage(number){
    window.editor.SetSelectedPage(number);
}

/* CHILI editor functions */
function GetEditor()
{
    try
    {
        if (document.getElementsByTagName('iframe').length > 0)
        {
            if (document.getElementsByTagName('iframe')[0].src != "")
            {
                window.frameWindow = document.getElementsByTagName('iframe')[0].contentWindow;
                window.frameWindow.GetEditor(EditorLoaded);
            }
        }
    } catch(err) {

    }
}


function EditorLoaded(jsInterface)
{
    window.editor = window.frameWindow.editorObject;
}

function SaveDocument()
{
    window.editor.ExecuteFunction("document","Save");
}

function OnEditorEvent( type, targetID ) 
{
    switch (type) 
    {
        case "DocumentSaved":
            callMeAfterSave();		
        break;
        case "DocumentFullyLoaded":
            window.fullyLoaded = true;
            getNavigationMenu();
            
            if(window.actionName == 'start'){
                setVariableAllValues();
            }else{
                if($('editorForm') != null){
                    $('editorForm').enable();
                }
            }
        break;
    }
}

/* End CHILI editor functions */

/* Helper to easliy show messages in the front-end */
function showNotification(title, content, width, height) {
    var settings = {
        className: 'magento',
        title: title,
        width: width,
        height: height,
        minimizable: false,
        maximizable: false,
        draggable: false,
        showEffectOptions: {
            duration:0.4
        },
        hideEffectOptions: {
            duration:0.4
        }
    };

    notificationWindow = new Window(settings);
    notificationWindow.setHTMLContent(content);
    notificationWindow.setZIndex(100000000);
    notificationWindow.showCenter(true);
}

function populateForm(formname, data) {
    var params = data.toQueryParams();
    var formElements = $(formname).getElements();

    formElements.each(function(element) {
        // Make form populate compatible with checkboxes
        if(element.type == "checkbox" && params[element.name.replace("[]","[0]")] == element.value) {
            element.checked = true;
        }
         
        // Make form populate properly insert text fields
        Form.Element.setValue(element, decodeURIComponent(params[element.name]));
    });
}