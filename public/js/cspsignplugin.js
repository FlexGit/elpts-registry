/*
 * cspsignplugin JavaScript Library v1.0.0.1
 * http://www.signal-com.ru
 * Copyright (C) 2016 Signal-COM
 * $Id: cspsignplugin.js 17411 2016-11-23 09:21:42Z tvm $
 */
 
(function() {

    var mimetype = "application/x-cspsign";

    function detectBrowsers() {
		var ua = navigator.userAgent;
		var result = { opera:false, firefox:false, safari:false, msie:false, msedge:false, chrome:false, blink:false, shortVersion:0, fullVersion:0 };
        // Opera 8.0+
        result.opera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        // Firefox 1.0+
        result.firefox = typeof InstallTrigger !== 'undefined';
        // At least Safari 3+: "[object HTMLElementConstructor]"
        result.safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
        // Internet Explorer 6-11
        result.msie = /*@cc_on!@*/false || !!document.documentMode;
        // Edge 20+
        result.msedge = !result.msie && !!window.StyleMedia;
        // Chrome 1+
        result.chrome = !!window.chrome && !!window.chrome.webstore;
        // Chrome 71+
        if(!result.chrome) result.chrome = !!window.chrome && (ua.indexOf("Chrome/") !== -1);
        // Blink engine detection
        result.blink = (result.chrome || result.opera) && !!window.CSS;
		
		if( result.opera ){
			if(ua.indexOf("Version/") !== -1){
				result.fullVersion = ua.split("Version/")[1];
			}else if(ua.indexOf("OPR/") !== -1){
				result.fullVersion = ua.split("OPR/")[1];
			}else{
				result.fullVersion = 0;
			}
		}else if(result.firefox){
			if(ua.indexOf("Firefox/") !== -1){
				result.fullVersion = ua.split("Firefox/")[1];
			}else{
				result.fullVersion = 0;
			}
		}else if(result.safari){
			if(ua.indexOf("Version/") !== -1){
				result.fullVersion = (ua.split("Version/")[1]).split(" ")[0];
			}else{
				result.fullVersion = 0;
			}			
		}else if(result.msie){
			if(ua.indexOf("MSIE ") !== -1){
				result.fullVersion = (ua.split("MSIE ")[1]).split(";")[0];
			}else if(ua.indexOf("; rv:") !== -1){
				result.fullVersion = (ua.split("; rv:")[1]).split(")")[0];
			}else{
				result.fullVersion = 0;
			}
		}else if(result.msedge){
			if(ua.indexOf("Edge") !== -1){
				result.fullVersion = (ua.split("Edge")[1]).split("/")[1];
			}else{
				result.fullVersion = 0;
			}
		}else if(result.chrome){
			if(ua.indexOf("Chrome/") !== -1){
				result.fullVersion = (ua.split("Chrome/")[1]).split(" ")[0];
			}else{
				result.fullVersion = 0;
			}
		}
		
		if(result.fullVersion !== 0){
			result.shortVersion = result.fullVersion.split(".")[0];
		}else{
			result.shortVersion = 0;
		}
		
		return result;
    }

    function initPlugin() {
        //Create a var that basically saves a random number to guarantee an unique identifier for a function. _plugin_ can be any other name.
        var callbackFn = "_plugin_" + Math.floor(Math.random() * 100000000);

        window[callbackFn] = function(data) {
            //Retrieve the wyrmhole factory for later creation
            var helper = data.wyrmhole;

            setHelper(helper);
        };
        /*Post a message to the extension, telling it to instantiate a a wyrmhole.
         FBDevTeam should be the name of your company inside the plugin configuration. For the echoTestPlugin its FBDevTeam.
         callbackFn is the function that will be called once the result of the postMessage is returned.
         */
        window.postMessage({
            firebreath: 'ru.signalcom.cspsign',
            callback: callbackFn
        }, "*");
    }

    function setHelper(helper) {
        //Using the wyrmholeFactory we create a wyrmhole.
        helper.create(mimetype).then(
                function(wyrmhole) {
                    //With the created wyrmhole we instantiate a new FireWyrmJS object that will allow us to create the plugin.
                    var FWJS = window.FireWyrmJS;
                    //Create pluginFactory that will allow the plugin creation.
                    window.pluginFactory = new FWJS(wyrmhole);
                    pluginFactory.create(mimetype, {/*some params*/}).then(
                            function(pluginObj) {
                                //Save the plugin to a gloal var for later access
                                window.cspsignplugin = pluginObj;
                            },
                            function(error) {
                                console.log("An Unexpected Error has ocurred: ", error);
                            }
                    );
                },
                function(error) {
                    console.log("An Unexpected Error has ocurred: ", error);
                }
        );
    }

    function pluginLoaded(pluginObj) {
        window.cspsignplugin = pluginObj;
    }

    window.pluginLoaded = pluginLoaded;

    if (window.addEventListener) {
        window.addEventListener("load", function(event) {
            if (detectBrowsers().chrome || detectBrowsers().opera || (detectBrowsers().firefox && detectBrowsers().shortVersion >= 53) ) {
                initPlugin();
            } else if (detectBrowsers().msie) {
				var objcspsign = '<object type="application/x-cspsign"><param name="onload" value="pluginLoaded"/></object>';
				window.document.body.insertAdjacentHTML('beforeEnd', objcspsign);
            } else {
                var objcspsign = '<object type="application/x-cspsign"><param name="onload" value="pluginLoaded"/></object>';
                window.document.body.insertAdjacentHTML('beforeEnd', objcspsign);
            }
        }, false);
    } else {
        window.attachEvent("onload", function(event) {
            if (detectBrowsers().chrome || detectBrowsers().opera || (detectBrowsers().firefox && detectBrowsers().shortVersion >= 53) ) {
                initPlugin();
            } else if (detectBrowsers().msie) {
				var objcspsign = '<object type="application/x-cspsign"><param name="onload" value="pluginLoaded"/></object>';
				window.document.body.insertAdjacentHTML('beforeEnd', objcspsign);            
            } else {
                var objcspsign = '<object type="application/x-cspsign"><param name="onload" value="pluginLoaded"/></object>';
                window.document.body.insertAdjacentHTML('beforeEnd', objcspsign);
            }
        });
    }
})();
