<!DOCTYPE html>
<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>

		<script type="text/javascript">
			//GeneralFunctions
			function XMLDocumentObject(a){a||(a="");var b;a:{if("undefined"!=typeof ActiveXObject)for(var c="Msxml2.DOMDocument.6.0 Msxml2.DOMDocument.5.0 Msxml2.DOMDocument.4.0 Msxml2.DOMDocument.3.0 MSXML2.DOMDocument MSXML.DOMDocument".split(" "),d=0;d<c.length;d++)try{b=new ActiveXObject(c[d]);break a}catch(e){}b=null}b?a&&(a=b.createElement(a),b.appendChild(a)):document.implementation.createDocument&&(b=document.implementation.createDocument("",a,null));return b} function xmlToString(a){return a.xml?a.xml:(new XMLSerializer).serializeToString(a)}function removeWhitespaceInXML(a){for(var b=0;b<a.childNodes.length;b++){var c=a.childNodes[b];1==c.nodeType&&removeWhitespaceInXML(c);!/\S/.test(c.nodeValue)&&3==c.nodeType?a.removeChild(a.childNodes[b--]):8==c.nodeType&&a.removeChild(a.childNodes[b--])}} function removeAllAttributesFromXMLNode(a,b,c){if(3==a.nodeType||8==a.nodeType)return!1;for(var d,e,f=a.attributes.length-1;0<=f;f--){d=a.attributes[f].name;e=!1;for(var g=0;g<c.length;g++)if(d==c[g]){e=!0;break}e||a.removeAttribute(d)}if(b)for(f=0;f<a.childNodes.length;f++)removeAllAttributesFromXMLNode(a.childNodes[f],!0,c)} function indentXMLString(a){for(var a=a.fl_replace("<![CDATA[","CDATA##->"),a=a.fl_replace("]]\>","<-##CDATA"),b="",a=a.replace(/(>)(<)(\/*)/g,"$1\r\n$2$3"),c=0,a=a.split("\r\n"),d=0,e=a.length;d<e;d++){var f=a[d],g=0;f.match(/.+<\/\w[^>]*>$/)?g=0:f.match(/^<\/\w/)?0!=c&&(c-=1):g=f.match(/^<\w[^>]*[^\/]>.*$/)?1:0;for(var k="",h=0;h<c;h++)k+="\t";b+=k+f+"\r\n";c+=g}b=b.fl_replace("CDATA##->","<![CDATA[");b=b.fl_replace("<-##CDATA","]]\>");return b.fl_trim()} function getTextFromNode(a,b,c){try{return a&&(b=getSingleNodeByTagName(a,b,0)),b?c?b.childNodes[0].nodeValue.fl_trim():b.childNodes[0].nodeValue:null}catch(d){return null}}function getSingleNodeByTagName(a,b,c){var a=a.childNodes,d=a.length;c||(c=0);for(var e=0,f=0;f<d;f++){var g=a[f];if(g.nodeName==b){if(e==c)return g;e++}}return null} function getAttribute(a,b,c,d){try{switch(c=a.getElementsByTagName(b)[0].getAttribute(c),d){case "int":return parseInt(c,10);case "float":return parseFloat(c);case "bool":return c.fl_toBool();case "array":return c.split(",");default:return c}}catch(e){return null}}function Variable(a){a||(a={});var b;this.get=function(){a.before_get&&a.before_get();return b};if(a.readOnly)return b=a.readOnly,this;this.set=function(c){return!a.before_set||a.before_set()?(b=c,a.after_set&&a.after_set(),b):null}} String.prototype.fl_trim=function(){return this.replace(/^\s+|\s+$/g,"")};String.prototype.fl_trimLeft=function(){return this.replace(/^\s+/,"")};String.prototype.fl_trimRight=function(){return this.replace(/\s+$/,"")};String.prototype.fl_splitIntoSegments=function(a){for(var b=this,c=[];b.length>a;)c.push(b.substr(0,a)),b=b.slice(a);b.length&&c.push(b);return c};String.prototype.fl_toBool=function(){var a=this.toLowerCase();return"y"==a||"true"==a||"yes"==a?!0:"n"==a||"false"==a||"no"==a?!1:null}; String.prototype.fl_toObject=function(a,b){a||(a=",");b&&(a=";");var c,d={},e=this.split(a),f=e.length,g=RegExp("'","g"),k=RegExp('"',"g");for(c=0;c<f;c++){var h=e[c].split(":"),i=h[0],i=i.replace(g,""),i=i.replace(k,""),i=i.fl_trim();(h=h[1])?(0==h.length?h=null:"null"==h?h=null:null!=h.fl_toBool()?h=h.fl_toBool():b&&(0==h.indexOf("[")&&h.lastIndexOf("]")==h.length-1)&&(h=h.slice(1,h.length-1),h=h.split(",")),d[i]=h):console.log("faulty value-key-pair is getting skipped:\n '"+i+"' -> "+this)}return d}; String.prototype.fl_toHash=function(){var a,b,c=0;if(0==this.length)return c;for(a=0;a<this.length;a++)b=this.charCodeAt(a),c=(c<<5)-c+b,c&=c;return c};String.prototype.fl_toCDataString=function(){return"<![CDATA["+this+"]]\>"};String.prototype.fl_fileExtension=function(){var a=this.split(".");return 1>=a.length?null:a.pop()};String.prototype.fl_fileExtensionAdd=function(a){return this.fl_fileExtension()==a?this:this+"."+a}; String.prototype.fl_fileExtensionRemove=function(){var a=this.split(".");a.pop();return a.join(".")};String.prototype.fl_lastPathComponent=function(){return this.split("/").pop()};String.prototype.fl_lastPathComponentRemove=function(a){var b=this.split("/");b.pop();return a?b.pop():b.join("/").fl_slashAdd()};String.prototype.fl_slashAdd=function(a){return a&&0==this.length?this+"/":this.lastIndexOf("/")!=this.length-1?this+"/":this}; String.prototype.fl_slashRemove=function(){return this.lastIndexOf("/")!=this.length-1?this:this.slice(0,this.length-1)};String.prototype.fl_replace=function(a,b){a=a.replace(RegExp("[.*+?|()\\[\\]{}\\\\]","g"),"\\$&");return this.replace(RegExp(a,"g"),b)};String.prototype.fl_decodeHTML=function(){if(0==this.length)return"";var a=document.createElement("div");a.innerHTML=this;return a.childNodes[0].nodeValue}; String.prototype.fl_escapeXMLAttribute=function(a){return this.replace(/\&|\u00df|\u00c4|\u00d6|\u00dc|\u00e4|\u00f6|\u00fc|\"|\'|<|>/g,function(b){for(var c="",d=0,e=b.length;d<e;d++)c+="&#x"+b.charCodeAt(d).toString(a?16:10)+";";return c})};String.prototype.fl_isBoolean=function(){return/^(y|n|true|false|yes|no)$/i.test(this)};String.prototype.fl_isInteger=function(){return/^\s*(\+|-)?\d+\s*$/.test(this)};String.prototype.fl_isFloat=function(){return/^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/.test(this)}; function isArray(a){return"[object Array]"===Object.prototype.toString.call(a)}Array.prototype.fl_removeDublicates=function(){var a,b,c=this.slice(0),d=c.slice(0,1);for(a=1;a<c.length;a++){var e=c[a],f=!0;for(b=0;b<d.length;b++)if(e==d[b]){f=!1;break}f&&d.push(e)}return d};Array.prototype.fl_sortAndRemoveDublicates=function(){var a,b=this.slice(0);b.sort();for(a=1;a<b.length;a++)b[a]===b[a-1]&&b.splice(a--,1);return b}; Array.prototype.fl_indexOf=function(a,b){if(this.indexOf)return this.indexOf(a,b);var b=b||0,c=this.length;0>b&&(b+=c);for(var d=b;d<c;d++)if(d in this&&this[d]===a)return d;return-1};Array.prototype.fl_compare=function(a){if(!a)return null;for(var b=this.slice(0),a=a.slice(0),c={both:[],first:null,second:null},d=b.length-1;0<=d;d--){var e=a.indexOf(b[d]);-1!=e&&(c.both.push(b[d]),b.splice(d,1),a.splice(e,1))}c.first=b;c.second=a;return c}; function objectToString(a,b,c){a=_stringify(a,b);c||(a="{"+a+"}");return a} function _stringify(a,b,c){function d(){var a=c?"":k+j+k+":",b;switch(typeof e[j]){case "string":b=h+e[j].replace(/'/g,"'")+h;break;default:b=e[j]}i+=(0<g?Array(g).join(" "):"")+a+(f?" ":"")+b+","+(f?"\n":"")}b||(b={});b={returnJSON:b.returnJSON||!1,returnStripped:b.returnStripped||!1,quoteKey:b.quoteKey||"",quoteValue:b.quoteValue||"'",padding:b.padding||!1,margin:b.margin||0};b.returnJSON&&(b.quoteKey='"',b.quoteValue='"',b.padding=!1);b.returnStripped&&(b.quoteKey="",b.quoteValue="",b.padding= !1);var e=("object"==typeof a||"function"==typeof a)&&null!=a?a:null,f=b.padding,g=b.padding&&0<b.margin?b.margin:0,k="boolean"==typeof b.quoteKey?'"':b.quoteKey,h="boolean"==typeof b.quoteValue?'"':b.quoteValue;if(null!=e){var i="",j;for(j in e)e.hasOwnProperty(j)&&("object"==typeof e[j]?e[j]?(a=c?"":k+j+k+":",isArray(e[j])?(b.margin=(0<g?g:1)+j.length+4,i=""==e[j].join(",")?i+((0<g?Array(g).join(" "):"")+a+(f?" ":"")+"{"+(f?"\n":"")+stringify(e[j],b)+(!0!=f?"":"\n"+Array((0<g?g:1)+j.length+2).join(" "))+ "},"+(f?"\n":"")):i+((0<g?Array(g).join(" "):"")+a+(f?" ":"")+"["+(f?"\n":"")+stringify(e[j],b,!0)+(!0!=f?"":"\n"+Array((0<g?g:1)+j.length+2).join(" "))+"],"+(f?"\n":""))):(b.margin=(0<g?g:1)+j.length+4,i+=(0<g?Array(g).join(" "):"")+a+(f?" ":"")+"{"+(f?"\n":"")+stringify(e[j],b)+(!0!=f?"":"\n"+Array((0<g?g:1)+j.length+2).join(" "))+"},"+(f?"\n":""))):d():d());e=0<i.length&&!0!=f?i.substring(0,i.length-1):2<i.length?i.substring(0,i.length-2):i}else e=a;return e} Number.prototype.fl_roundWithPositions=function(a){return Math.round(this*Math.pow(10,a))/Math.pow(10,a)};Number.prototype.fl_toString=function(a,b){var c=this.toString(b||10);if(a)for(;c.length<a;)c="0"+c;return c}; Number.prototype.fl_toFileSize=function(a){var b=0,c=0,d=this,e="byte KB MB GB TB PB EB".split(" ");if(a){if("byte"==a.toLowerCase())return this+"byte";for(var f=0;f<e.length;f++)if(a.toLowerCase()==e[f].toLowerCase()){c=f;break}}for(;999<d&&!(d/=1024,b++,b==c););return Math.max(d,0.1).fl_roundWithPositions(2)+e[b]}; function addImageFunctions(a){a.fl_width=function(){return this.naturalWidth?this.naturalWidth:this.width};a.fl_height=function(){return this.naturalHeight?this.naturalHeight:this.height};a.fl_setWidth=function(a){this.width=a;this.style.width=a+"px"};a.fl_setHeight=function(a){this.height=a;this.style.height=a+"px"}} function getStyle(a,b,c){var d;document.defaultView&&document.defaultView.getComputedStyle?d=document.defaultView.getComputedStyle(a,"").getPropertyValue(b):a.currentStyle&&(b=b.replace(/\-(\w)/g,function(a,b){return b.toUpperCase()}),d=a.currentStyle[b]);return c?parseFloat(d):d}function randomNumber(a,b){a=parseInt(a,10);b=parseInt(b,10);return a>b?-1:a==b?a:a+parseInt(Math.random()*(b-a+1),10)} function hitTest(a,b,c){var d=document.getElementById(c),c=parseInt(getStyle(d,"left"),10),e=d.clientWidth+c,f=parseInt(getStyle(d,"top"),10),d=d.clientHeight+f;return a>c&&a<e&&b>f&&b<d?!0:!1}function getTarget(a){var b;a||(a=window.event);a.target?b=a.target:a.srcElement&&(b=a.srcElement);3==b.nodeType&&(b=b.parentNode);return b}function stopBubbling(a){a||(a=window.event);a.stopPropagation?a.stopPropagation():a.cancelBubble=!0} function blockDefault(a){a||(a=window.event);a&&(a.preventDefault?a.preventDefault():a.returnValue=!1);return a} function preloadImage(a,b,c,d){"undefined"==typeof imageHolder&&(imageHolder={_toLoadCounter:0});if(!imageHolder[b]){imageHolder._toLoadCounter++;a=a.fl_slashAdd();b.lastIndexOf(".")==b.length-1&&(b=b.substring(0,b.length-2));0!=c.indexOf(".")&&(c="."+c);var e=new Image;addImageFunctions(e);e.onload=function(){imageHolder._toLoadCounter--};e.onerror=function(){imageHolder._toLoadCounter--};e.src=a+b+c;d||(d=b);imageHolder[d]=e}} function getDevicePixelRatio(){return window.devicePixelRatio?window.devicePixelRatio:1}function setFunction(a,b){detection.pc?a.onclick=b:a.ontouchend=b}function setFunc(a,b,c){detection.pc?a.onclick=createDelegate(b,c):a.ontouchend=createDelegate(b,c)}function createDelegate(a,b){return function(){b.apply(a,arguments)}}function urlPath(){var a=document.URL.replace(/\\/g,"/"),a=a.replace("file://","file:///"),a=a.replace("file:////","file:///"),a=a.split("/");a.pop();return a.join("/")+"/"} function makeSelectable(a,b){removeClass(a,"unselectable");addClass(a,"selectable");b||(a.style.cursor="default");a.unselectable="off";a.onselectstart=null}function makeUnselectable(a,b){removeClass(a,"selectable");addClass(a,"unselectable");b||(a.style.cursor="default");a.unselectable="on";a.onselectstart=function(){return false}}function hasClass(a,b){return RegExp("\\b"+b+"\\b").test(a.className)}function addClass(a,b){this.hasClass(a,b)||(a.className+=" "+b)} function removeClass(a,b){hasClass(a,b)&&(a.className=a.className.replace(RegExp("(\\s|^)"+b+"(\\s|$)")," ").fl_trim())}function getElementsByClassName(a,b,c){c||(c=document.getElementsByTagName("body")[0]);for(var d=[],c=c.getElementsByTagName("*"),e=c.length,f=0;f<e;f++)b?c[f].className==a&&d.push(c[f]):hasClass(c[f],a)&&d.push(c[f]);return d}function removeElement(a){a.parentNode.removeChild(a)} function insertImage(a){if(!a.image||!a.object)return!1;a.width=a.image.fl_width()/2;a.height=a.image.fl_height()/2;var b=document.createElement("div");switch(a.left){case "center":a.left=(a.object.clientWidth-a.width)/2}switch(a.top){case "center":a.top=(a.object.clientHeight-a.height)/2}"undefined"!=typeof a.id&&(b.id=a.id);if("undefined"!=typeof a.className)b.className=a.className;else if(b.style.width=parseInt(a.width,10)+"px",b.style.height=parseInt(a.height,10)+"px","undefined"!=typeof a.position&& (b.style.position=a.position),"undefined"!=typeof a.left&&(b.style.left=parseInt(a.left,10)+"px"),"undefined"!=typeof a.right&&(b.style.right=parseInt(a.right,10)+"px"),"undefined"!=typeof a.top&&(b.style.top=parseInt(a.top,10)+"px"),"undefined"!=typeof a.bottom)b.style.bottom=parseInt(a.bottom,10)+"px";a.object.appendChild(b);var c=new Image;addImageFunctions(c);c.src=a.image.src;c.ondragstart=function(){return false};c.fl_setWidth(a.width);c.fl_setHeight(a.height);b.appendChild(c)} function switchDragObject(a,b){a=blockDefault(a);b||(b=getTarget(a));!b.getAttribute("draggable")||!1==b.getAttribute("draggable").fl_toBool()?(b.setAttribute("draggable",!0),b.onmousemove=function(a){dragObject(a,this)},dragObject(a,b)):(b.setAttribute("draggable",!1),b.onmousemove=null,edit&&edit.groundplanManipulationFinished(b))} function dragObject(a,b){a=blockDefault(a);b||(b=getTarget(a));if(b.getAttribute("draggable")){var c=0,d=0;if(a.pageX||a.pageY)c=a.pageX,d=a.pageY;else if(a.clientX||a.clientY)c=a.clientX+document.body.scrollLeft+document.documentElement.scrollLeft,d=a.clientY+document.body.scrollTop+document.documentElement.scrollTop;c-=b.x;d-=b.y;b.style.left=b.offsetLeft+c-b.offsetWidth/2+"px";b.style.top=b.offsetTop+d-b.offsetHeight/2+"px";edit&&edit.groundplanHotspotMoved(b,b.offsetLeft+c,b.offsetTop+d)}} function switchRotateObject(a,b){a=blockDefault(a);b||(b=getTarget(a));!b.dataset.rotatable||!1==b.dataset.rotatable.fl_toBool()?(b.dataset.rotatable=!0,b.onmousemove=function(a){rotateObject(a,this)},rotateObject(a,b)):(b.dataset.rotatable=!1,b.onmousemove=null,edit&&edit.groundplanManipulationFinished(b))} function rotateObject(a,b){var c=0,d=0;if(a.pageX||a.pageY)c=a.pageX,d=a.pageY;else if(a.clientX||a.clientY)c=a.clientX+document.body.scrollLeft+document.documentElement.scrollLeft,d=a.clientY+document.body.scrollTop+document.documentElement.scrollTop;c=-1*parseFloat(180*Math.atan2(c-b.x-b.offsetWidth/2,d-b.y-b.offsetHeight/2)/Math.PI-270);360<=c&&(c-=360);b.style.transform="rotate("+c+"deg)";b.style.WebkitTransform="rotate("+c+"deg)";b.style.MozTransform="rotate("+c+"deg)";b.style.OTransform="rotate("+ c+"deg)";b.style.msTransform="rotate("+c+"deg)";edit&&edit.groundplanHotspotRotated(c)}function getFolderContent(a){a=encodeURIComponent("../"+a);a="dir="+a;return(new Requestor).oFetch({url:"_DEVELOP/list.php",returnText:!0,data:a}).fl_toObject(null,!0)} function convertHexToRGB(a){a=a.split(",");if(8==a[0].length&&"0x"==a[0].substr(0,2))a[0]=a[0].slice(2);else if(7==a[0].length&&"#"==a[0].substr(0,1))a[0]=a[0].slice(1);else if(6!=a[0].length)return console.log("hex value has to be 6 chars long!"),null;a[1]=a[1]?parseFloat(a[1]):1;for(var b=[],c=a[0].fl_splitIntoSegments(2),d=0;3>d;d++)b.push(parseInt(c[d],16));b.push(a[1]);return b} function convertToRGBAFilterValue(a){for(var a="string"==typeof stringOrRGBAArray?a.split(","):a,b=0;b<a.length;b++)a[b]=parseFloat(a[b],10);4>a.length&&(a[3]=1);return"rgba("+a[0]+","+a[1]+","+a[2]+","+a[3]+")"} function convertToIEFilterValue(a){for(var a="string"==typeof stringOrRGBAArray?a.split(","):a,b=0;b<a.length;b++)a[b]=parseFloat(a[b],10);4>a.length&&(a[3]=1);for(var a=[255*a.pop()].concat(a),b="",c=0;4>c;c++){var d=parseInt(a[c],10);0>d?d=0:255<d&&(d=255);b+=d.fl_toString(2,16)}return"#"+b};
			//Requestor
			function Requestor(){this.tries=3;this.delay=1E3;this.url=this.request=null;this.returnXML=!0;this.loadFunction=this.data=this.callback=null;if("undefined"!=typeof XMLHttpRequest)this.request=new XMLHttpRequest,this.loadFunction=this.load;else try{this.request=new ActiveXObject("Msxml2.XMLHTTP"),this.loadFunction=this.loadIE}catch(a){try{this.request=new ActiveXObject("Microsoft.XMLHTTP"),this.loadFunction=this.loadIE}catch(c){return null}}this.request||console.log("Browser not supported or JavaScript or ActiveX (IE) is not enabled!")} Requestor.prototype.oFetch=function(a){if(a.url)return this.fetch(a.url,a.returnText,a.callback,a.data,a.callbackParameters,a.requestMethod,a.forceReload);console.log("Request needs a url -> {url: 'http:\\\\www.example.com\\...'}")};Requestor.prototype.fetch=function(a,c,d,b,f,e,g){this.url=a;this.returnXML=c?!1:!0;this.callback=d;this.callbackParameters=f;this.data=b;this.requestMethod=e?e:this.data?"POST":"GET";this.forceReload=g?"?hash="+(new Date).getTime():"";return this.loadFunction()}; Requestor.prototype.load=function(){this.tries--;this.request.overrideMimeType&&this.request.overrideMimeType("text/xml");this.request.open(this.requestMethod,this.url+this.forceReload,this.callback?!0:!1);this.callback&&(this.request.onreadystatechange=createDelegate(this,function(){if(4==this.request.readyState){var a=this.returnXML?this.request.responseXML:this.request.responseText;200==this.request.status||0==this.request.status&&a?(this.tries=3,this.callback(a,this.callbackParameters)):(console.log("ERROR obtaining '"+ this.url+"' (Status: "+this.request.status+" - "+this.request.statusText+")"),this.tries&&0<this.tries?window.setTimeout(createDelegate(this,function(){this.load()}),this.delay):(this.tries=3,this.callback(null)))}}));this.data?this.request.setRequestHeader("Content-Type","application/x-www-form-urlencoded"):this.request.setRequestHeader("Content-Type","text/xml");this.request.setRequestHeader("Cache-Control","no-cache");this.request.setRequestHeader("Access-Control-Allow-Origin","*");this.request.send(this.data); if(!this.callback&&4==this.request.readyState){var a=this.returnXML?this.request.responseXML:this.request.responseText;if(200==this.request.status||0==this.request.status&&a)return this.tries=3,a;if(this.tries&&0<this.tries)this.load();else return this.tries=3,null}};Requestor.prototype.loadIE=function(){return!1}; function fileSave(a,c,d,b){a=encodeURIComponent("../"+a.fl_slashAdd());c=encodeURIComponent(c);d=encodeURIComponent(d);b={createBackup:b.createBackup||!1,delComments:b.delComments||!1,delWhitespaces:b.delWhitespaces||!1,callback:b.callback||null,callbackParameters:b.callbackParameters||null};a="path="+a+"&file="+c+"&content="+d+"&createBackup="+b.createBackup+"&delComments="+b.delComments+"&delWhitespaces="+b.delWhitespaces;(new Requestor).oFetch({url:"_DEVELOP/save.php",returnText:!0,data:a,callback:b.callback, callbackParameters:b.callbackParameters})}function fileDownload(a,c){a=encodeURIComponent("../"+a.fl_slashAdd());c=encodeURIComponent(c);window.location.href=urlPath()+"_DEVELOP/download.php?"+("path="+a+"&file="+c)};
			//Cookies
			function setCookie(d,c,e){var b=new Date,a=new Date(b);a.setMinutes(b.getMinutes()+e);document.cookie=d+"="+c+"; expires="+a.toGMTString()+"; path=/"}function getCookieValue(d){for(var c,e,b=document.cookie.split(";"),a=0;a<b.length;a++)if(c=b[a].substr(0,b[a].indexOf("=")),e=b[a].substr(b[a].indexOf("=")+1),c=c.replace(/^\s+|\s+$/g,""),c==d)return unescape(e);return null}function delCookie(d){document.cookie=d+"=; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/"} function testCookie(){setCookie("diginetmedia_test","test",1);return getCookieValue("diginetmedia_test")?(delCookie("diginetmedia_test"),!0):!1};
			//JSON2
			var JSON;JSON||(JSON={}); (function(){function k(a){return 10>a?"0"+a:a}function o(a){p.lastIndex=0;return p.test(a)?'"'+a.replace(p,function(a){var c=r[a];return"string"===typeof c?c:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function m(a,j){var c,d,h,n,g=e,f,b=j[a];b&&("object"===typeof b&&"function"===typeof b.toJSON)&&(b=b.toJSON(a));"function"===typeof i&&(b=i.call(j,a,b));switch(typeof b){case "string":return o(b);case "number":return isFinite(b)?""+b:"null";case "boolean":case "null":return""+b; case "object":if(!b)return"null";e+=l;f=[];if("[object Array]"===Object.prototype.toString.apply(b)){n=b.length;for(c=0;c<n;c+=1)f[c]=m(c,b)||"null";h=0===f.length?"[]":e?"[\n"+e+f.join(",\n"+e)+"\n"+g+"]":"["+f.join(",")+"]";e=g;return h}if(i&&"object"===typeof i){n=i.length;for(c=0;c<n;c+=1)"string"===typeof i[c]&&(d=i[c],(h=m(d,b))&&f.push(o(d)+(e?": ":":")+h))}else for(d in b)Object.prototype.hasOwnProperty.call(b,d)&&(h=m(d,b))&&f.push(o(d)+(e?": ":":")+h);h=0===f.length?"{}":e?"{\n"+e+f.join(",\n"+ e)+"\n"+g+"}":"{"+f.join(",")+"}";e=g;return h}}"function"!==typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+k(this.getUTCMonth()+1)+"-"+k(this.getUTCDate())+"T"+k(this.getUTCHours())+":"+k(this.getUTCMinutes())+":"+k(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf()});var q=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, p=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,e,l,r={"\u0008":"\\b","\t":"\\t","\n":"\\n","\u000c":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},i;"function"!==typeof JSON.stringify&&(JSON.stringify=function(a,j,c){var d;l=e="";if(typeof c==="number")for(d=0;d<c;d=d+1)l=l+" ";else typeof c==="string"&&(l=c);if((i=j)&&typeof j!=="function"&&(typeof j!=="object"||typeof j.length!=="number"))throw Error("JSON.stringify");return m("", {"":a})});"function"!==typeof JSON.parse&&(JSON.parse=function(a,e){function c(a,d){var g,f,b=a[d];if(b&&typeof b==="object")for(g in b)if(Object.prototype.hasOwnProperty.call(b,g)){f=c(b,g);f!==void 0?b[g]=f:delete b[g]}return e.call(a,d,b)}var d,a=""+a;q.lastIndex=0;q.test(a)&&(a=a.replace(q,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));if(/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){d=eval("("+a+")");return typeof e==="function"?c({"":d},""):d}throw new SyntaxError("JSON.parse");})})();
			//Script
            function log(e,t,n,r){var i=null,s=null,o=function(e,t,n){var r=document.createElement("div");r.style.paddingLeft=20*t+"px";r.innerHTML=""==e?"[empty string]":e;n&&r.appendChild(n);return r};if(n&&isArray(n)){s=document.createElement("div");s.style.display=r?"block":"none";for(var u=0,a=n.length;u<a;u++)s.appendChild(o(n[u],tour.logLevel+1));i=document.createElement("div");i.className=r?"arrow_up":"arrow_down";i.style.cursor="pointer";i.style.display="inline-block";i.style.marginLeft="5px";i.style.marginBottom="1px";i.onclick=function(){switch(getStyle(s,"display")){case"none":s.style.display="block";this.className="arrow_up";break;case"block":s.style.display="none",i.className="arrow_down"}}}if(t)switch(t.toLowerCase()){case"success":e='<span class="success">SUCCESS:</span>'+e;break;case"warning":e='<span class="warning"">WARNING:</span>'+e;break;case"attention":e='<span class="attention">ATTENTION:</span>'+e}t=document.getElementById("log");t.appendChild(o(e,tour.logLevel,i));s&&t.appendChild(s)}function logClear(){tour.logLevel=0;document.getElementById("log").innerHTML=null}function gatherInputFields(e,t){logClear();for(var n=[],r=e.parentNode.childNodes,i=0;i<r.length;i++){var s=r[i];switch(s.nodeName.toLowerCase()){case"input":s=s.value;if(-1!=s.indexOf("needed:")){log("Please enter '"+s+"'");return}-1!=s.indexOf("optional:")?n.push(null):n.push(s);break;case"select":n.push(s.options[s.selectedIndex].value)}}for(i=0;i<n.length;i++)"null"==n[i]&&(n[i]=null);n.push(e);t?t.apply(tour,n):console.log("This function is not available")}function Tour(){this.projectsListing=getProjectContent()?getProjectContent():{};this.projectsListing._getCurrentListing=function(){var e=tour.mainFolder.get().fl_lastPathComponentRemove(!0);return tour.projectsListing[e]?tour.projectsListing[e]:{}};this.projectsListing._getCurrentProject=function(){var e=tour.projectsListing._getCurrentListing(),t=tour.projectFolder.get().fl_slashRemove();return e[t]?e[t]:null};this.mainFolder=new Variable({readOnly:"./".fl_slashAdd()});this.projectFolder=new Variable;this.projectFolder.set("");this.xmlFolder=new Variable({readOnly:"_xml/".fl_slashAdd()});this.mapFolder=new Variable({readOnly:"maps/".fl_slashAdd()});this.unusedFolder=new Variable({readOnly:"_unused/".fl_slashAdd()});this.workFolder=function(){return this.mainFolder.get()+this.projectFolder.get()};this.tourVersion=this.tourFolder=null;this.indexFile="index.php";this.logLevel=0;this.project={loaded:{},filtered:{}}}function SettingsEditor(e){this.startPanoramas=tour._getAllStartFiles();this.xml=e;this.xmlObject=this.convertSettingsXML();this.xmlVerson=this.xml.documentElement.getAttribute("version")}Tour.prototype._loadXML=function(e,t){if(!e)return null;t||(t=this.workFolder()+this.xmlFolder.get());var n=(new Requestor).oFetch({url:t.fl_slashAdd()+e,forceReload:!0});removeWhitespaceInXML(n);return n};Tour.prototype._getAllPanoNodes=function(e){var t,n=e.getElementsByTagName("type"),r={all:[],visible:[],hidden:[]};for(e=0;e<n.length;e++)t=n[e],-1!=getTextFromNode(null,t,!0).indexOf("360")&&(t=t.parentNode,t.getAttribute("hidden")&&t.getAttribute("hidden").fl_toBool()?r.hidden.push(t):r.visible.push(t),r.all.push(t));return r};Tour.prototype._getAllStartFiles=function(){if(this.project.loaded.menuXML)for(var e=[],t=[],n=tour._getAllPanoNodes(tour.project.loaded.menuXML).all,r=0,i=n.length;r<i;r++)e.push(getTextFromNode(n[r],"file",!0)),t.push(e[r]+" ("+getTextFromNode(n[r].parentNode,"title",!0)+" -> "+getTextFromNode(n[r],"title",!0)+")");return 0==e.length?null:{files:e,filesAndCaption:t}};Tour.prototype.projectSelect=function(){var e=getFolderContent(this.mainFolder.get()),t=/^[A-z]{2}_[0-9]{5}(_[0-9-A-z]+)?$/,n=[],r=this.projectsListing._getCurrentListing(),e=e.folders.filter(function(e){if(t.test(e))return n.push(e+(r[e]&&r[e].name?" - "+r[e].name:"")),e},this);switch(e.length){case 0:document.body.innerHTML="NO PROJECTS FOUND!";break;default:var i=document.getElementById("projectSelection_select");this._fillSelectFields([i],e,n,"no Project found",this.projectFolder.get().fl_lastPathComponentRemove(!0));i.onchange=createDelegate(this,function(e){this.projectFolder.set(getTarget(e).value.fl_slashAdd());logClear();this.fillInput();this.generateLink()});""==this.projectFolder.get()&&this.projectFolder.set(e[0].fl_slashAdd());this.fillInput();this.generateLink()}};Tour.prototype.realignLog=function(){var e=document.getElementById("log"),t=document.getElementById("projectSelection_div")?document.getElementById("projectSelection_div").clientHeight:0,t=t+document.getElementById("functionContainer").clientHeight;e.style.top=t+"px";e.style.visibility="visible"};Tour.prototype.fillInput=function(){var e,t,n=getElementsByClassName("functionStarter");e=0;for(t=n.length;e<t;e++)""!=n[e].style.display&&(n[e].style.display="none");delete n;this.project.loaded.settingsXML=this._loadXML("_settings.xml",this.workFolder());this.project.loaded.menuXML=this._loadXML("menu.xml");delete e;delete t;this.realignLog()};Tour.prototype._fillSelectFields=function(e,t,n,r,i){n||(n=t);for(var s=0,o=e.length;s<o;s++)if(e[s].innerHTML=null,0<t.length)for(var u=0;u<t.length;u++){var a=document.createElement("option");a.innerHTML=n[u];a.setAttribute("value",t[u]);t[u]==i&&a.setAttribute("selected",!0);e[s].appendChild(a)}else a=document.createElement("option"),a.innerHTML=r,a.setAttribute("value",null),e[s].appendChild(a)};Tour.prototype.editSettingsXML=function(){(new SettingsEditor(this.project.loaded.settingsXML)).fillCanvas()};Tour.prototype.generateLink=function(){var e=new SettingsEditor(this.project.loaded.settingsXML),t=e.startPanoramas,n=e.xmlObject,r=urlPath(),i={};delete e;var s=0,e=document.getElementById("log");e.innerHTML="";var o=document.createElement("table");o.style.borderSpacing="0px";o.cellPadding="0";o.cellSpacing="0";var u=function(){var e=["projectPath:../"+this.projectFolder.get()],t=[],n;for(n in i)if(i.hasOwnProperty(n)&&0!=i[n])switch(n){case"path":break;default:e.push(n+":"+i[n])}t=0<t.length?"&"+t.join("&"):"";e=i.path+"_/"+this.indexFile+"?options="+encodeURIComponent(e.join(","))+t;document.getElementById("linkGenerator_result").href=e;document.getElementById("linkGenerator_result").innerHTML=e},a=function(e,t,n){i[e]=t[0];l=o.insertRow(s);c=l.insertCell(0);c.innerHTML=e;c=l.insertCell(1);c.innerHTML=":";c.style.paddingLeft="10px";c.style.paddingRight="10px";c=l.insertCell(2);for(var r=document.createElement("select"),a=0,f=t.length;a<f;a++){var h=document.createElement("option");h.innerHTML=n?n[a]:t[a];h.setAttribute("value",t[a]);r.appendChild(h)}r.onchange=function(){i[e]=this.options[this.selectedIndex].value;u.call(tour)};c.appendChild(r);delete r;delete h;s++},f=function(e){i[e]=!1;l=o.insertRow(s);c=l.insertCell(0);c.innerHTML=e;c=l.insertCell(1);c.innerHTML=":";c.style.paddingLeft="10px";c.style.paddingRight="10px";c=l.insertCell(2);var t=document.createElement("input");t.style.marginLeft="0px";t.setAttribute("type","checkbox");t.onchange=function(){i[e]=this.checked;u.call(tour)};c.appendChild(t);delete t;s++},l,c;i.path=r;l=o.insertRow(s);c=l.insertCell(0);c.innerHTML="path";c=l.insertCell(1);c.innerHTML=":";c.style.paddingLeft="10px";c.style.paddingRight="10px";c=l.insertCell(2);var h=document.createElement("input");h.value=r;h.style.width="100%";h.style.boxSizing="border-box";h.style.textAlign="left";h.style.border="none";h.style.margin="0px";h.style.padding="0px";h.setAttribute("type","text");h.setAttribute("size","100%");h.onchange=function(){var e=this.value.fl_slashAdd();this.value=e;i.path=e;u.call(tour)};c.appendChild(h);delete h;s++;a("file",t.files,t.filesAndCaption);a("language",isArray(n.language.available)?n.language.available:[n.language.available]);f("rearrangeMenu");f("hideMenu");f("removeMenu");f("hideOverlays");f("hideHotspots");f("disableSound");f("disableStartAnimation");f("slideShowAutoStart");e.appendChild(o);log("&nbsp;");log('<span style="color: #FF6600;">LINK:</span>');t=document.createElement("a");t.id="linkGenerator_result";e.appendChild(t);u.call(this)};SettingsEditor.prototype.convertSettingsXML=function(){for(var e={},t=this.xml.documentElement.childNodes,n=0,r=t.length;n<r;n++){var i=t[n],s=i.nodeName;"undefined"==typeof e[s]?e[s]={}:console.log("'"+s+"' already exists...should not happen...merging both");for(var i=i.attributes,o=0,u=i.length;o<u;o++){var a=i[o].name;if(-1==a.indexOf("_comment")){var f=i[o].value;e[s][a]=f.fl_isBoolean()?f.fl_toBool():f.fl_isInteger()?parseInt(f,10):f.fl_isFloat()?parseFloat(f):1>=f.split(",").length?""==f?null:f:f.split(",")}}}return e}
		</script>

		<!--UNWICHTIG-->
		<style type="text/css">
		<!--
		* {
			padding:0;
			margin:0;
		}
		body {
			font-family : Verdana, Geneva, sans-serif;
			overflow : auto;
			background-color : #000;
			color: #777;
		}
		#main {
			position:absolute;
			display:block;
			font-size: 16pt;
			font-weight:500;
			text-align: center;
			color:#fff;
			width:600px;
			left:50%;
			margin-left:-300px;
			height:70px;
			line-height:70px;
			top:50%;
			margin-top:-200px;
			padding:3px 0;

			background:#777777;

/*			background-image: -webkit-linear-gradient(left, #000, #777777, #000000);
			background-size: 100% 3px, 100% 3px;
			background-position:0 0, 0 100%;
			-moz-background-size:100% 3px;
			    background-repeat:no-repeat;
			background-image:    -moz-linear-gradient(left, #000, #777777, #000000);
			background-image:     -ms-linear-gradient(left, #000, #777777, #000000);
			background-image:      -o-linear-gradient(left, #000, #777777, #000000);
			background-image:         linear-gradient(left, #000, #777777, #000000);*/
		}
		.link:hover {
			color:#ff6600;
			cursor:pointer;
			-webkit-transition: color 0.35s ease-in-out;
			-moz-transition: color 0.35s ease-in-out;
			-ms-transition: color 0.35s ease-in-out;
			-o-transition: color 0.35s ease-in-out;
			transition: color 0.35s ease-in-out;
		}
		#inhalt {
			background-color:#000;
			margin-bottom:80px;
		}
		.orange {
			color:#ff6600;
			font-weight:600;
		}
		#footer {
			color:#777;
			position: fixed;
			top: 100%;
			margin-top: -55px;
			padding:10px 0;
			height:40px;
			text-align: center;
			width: 100%;
			font-size: 10pt;
			border-top: 1px solid #777;
		}
		#footer a {
			color:#777;
			text-decoration:none
		}
		#footer a:hover {
			color:#ff6600;
			-webkit-transition: color 0.35s ease-in-out;
			-moz-transition: color 0.35s ease-in-out;
			-ms-transition: color 0.35s ease-in-out;
			-o-transition: color 0.35s ease-in-out;
			transition: color 0.35s ease-in-out;
		}
		#header {
			position:absolute;
			color:#777;
			position: fixed;
			top: 0;
			padding-top:10px;
			height:40px;
			text-align: center;
			width: 100%;
			font-size: 10pt;
			border-top: 1px solid #777;
		}
		#logo {
			position:absolute;
			cursor:pointer;
			background-color:#000000;
			background-image:url('_/js/vendor/switch/logo_diginetmedia.png');
			background-repeat:no-repeat;
			background-position:6px 6px;
			width:326px;
			height:50px;
			right:40px;
			padding:6px;
		}
		#logo a {
			display:block;
			width:326px;
			height:50px;
		}
		#linie {
			position:relative;
			height:2px;
			background:#999;
			width:100%;
			top:31px;
		}
		#fb {
			position:absolute;
			cursor:pointer;
			background-color:#000000;
			background-image:url('_/js/vendor/switch/f_logo.png');
			background-repeat:no-repeat;
			width:26px;
			height:26px;
			right:40px;
			top:15px;
		}
		#fb a {
			display:block;
			width:26px;
			height:26px;
			background-position:0 0;
		}
		#fb:hover {
			background-position:0 -26px;
		}
		#content {
				position			: absolute;
				left				: 50%;
				top					: 50%;
				margin-left			: -457px; /*width/2 plus 7 for padding and border*/
				margin-top			: -197px; /*height/2 "plus 7 for padding and border*/
				width				: 900px;
				height				: 380px;
				background-color	: transparent;
				padding				: 5px;
				border				: 2px solid #FF6600;
		}
		-->
		</style>

		<style>
			body {
				width				: 100%;
				height				: 100%;
				margin				: 0;
				padding				: 0;
				border				: 0;
				font-family			: Verdana, Geneva, sans-serif;
				overflow			: auto;
				background-color	: #000;
				color				: #777;
			}
			.fontColor {
				color				: #FF6600;
				border-color		: #FF6600;
			}
			.backgroundColor {
				background-color	: black;
			}
			.font {
				font-family			: "courier new", "lucida console", arial, sans-serif;
				font-size			: 14px;
			}
			#functionContainer {
				padding				: 10px;
			}
			#functionContainer div form {
				height				: 40px;
			}
			#projectSelection_div {
				padding				: 10px;
				border-bottom		: 1px solid #FF6600;
			}
			.functionDiv {
				display				: inline-block;
				line-height			: 30px;
			}
			input, select {
				font-family			: "courier new", "lucida console", arial, sans-serif;
				font-size			: 14px;
				text-align			: center;
				background-color	: transparent;
				border				: 1px solid black;
				border-bottom		: 1px solid #FF6600;
				outline				: none;
				color				: #FF6600;
				background-color	: black;
				box-sizing			: border-box;
/*				-webkit-appearance	: none;
				-moz-appearance		: none;
				appearance			: none;*/
			}
			.tourStarter {
				height				: 30px;
				float				: right;
				margin-left			: 20px;
				padding-left		: 5px;
				padding-right		: 5px;
				text-align			: center;
				cursor				: pointer;
				border-width		: 1px;
				border-style		: solid;
			}
			.tourStarter:hover {
				border-color		: transparent;
				background-color	: #FF6600;
				color				: black;
			}
			.functionStarter {
				width				: 30px;
				height				: 30px;
				float				: right;
				margin-left			: 20px;
				text-align			: center;
				cursor				: pointer;
				border-width		: 1px;
				border-style		: solid;
			}
			.functionStarter:hover {
				border-color		: transparent;
				background-color	: #FF6600;
				color				: black;
			}
			#log {
				position			: absolute;
				left				: 0px;
				top					: 0px;
				right				: 0px;
				bottom				: 10px;
				margin				: 10px;
				margin-top			: 0px;
				padding				: 10px;
				border-width		: 1px;
				border-style		: solid;
				overflow-x			: hidden;
				overflow-y			: auto;
				visibility			: hidden;
			}
			#version {
				position			: absolute;
				right				: 0px;
				bottom				: 0px;
				margin-right		: 10px;
				margin-bottom		: 3px;
				font-size			: 10px;
				text-align			: right;
			}

			.previewRow {
				margin-bottom		: 12px;
			}
			.previewImage {
				margin-right		: 6px;
				vertical-align		: top;
			}
			.previewImageActive {
				outline				: 2px solid yellow;
			}
			.success {
				color				: yellow;
				font-weight			: bold;
				margin-right		: 6px;
			}
			.warning {
				color				: red;
				font-weight			: bold;
				margin-right		: 6px;
			}
			.attention {
				color				: orange;
				font-weight			: bold;
				margin-right		: 6px;
			}
			.arrow_up {
				width				: 0;
				height				: 0;
				border-left			: 5px solid transparent;
				border-right		: 5px solid transparent;
				border-bottom		: 5px solid #FF6600;
			}
			.arrow_down {
				width				: 0;
				height				: 0;
				border-left			: 5px solid transparent;
				border-right		: 5px solid transparent;
				border-top			: 5px solid #FF6600;
			}
			#linkGenerator_result {
				color				: #777;
				word-wrap			: break-word;
                font-size			: 12px;
			}
			#linkGenerator_result:hover {
				color				: white;
			}
		</style>

	<title>Link-Generator</title>
	</head>

	<body>

		<div id="header">
			<div id="linie"></div>
			<div id="logo"><a href="http://www.diginetmedia.de" title="zur diginetmedia-Homepage" target="_blank"></a></div>
		</div>

<!--		<iframe id="content" src="../__TOOLS/linkgenerator.html"></iframe>-->
<div id="content">

    <!--PROJECT SELECTION-->
    <div id="projectSelection_div">
        <form onSubmit="return false">
            <div class="functionDiv">
                Projekt ausw&auml;hlen:&nbsp<select id="projectSelection_select" name="folderProject"></select>
            </div>
        </form>

    </div>

    <div id="functionContainer"></div>

    <!--CANVAS-->
    <div id="log"></div>

</div>


<div id="footer">
    diginetmedia&nbsp;&nbsp;&bull;&nbsp;&nbsp;Prof.-Dr.-Konrad-Zuse-Str. 5&nbsp;&nbsp;&bull;&nbsp;&nbsp;08289 Schneeberg&nbsp;&nbsp;&bull;&nbsp;&nbsp;Tel.
    +49 3772 371679 0<br>Fax +49 3772 371679 50&nbsp;&nbsp;&bull;&nbsp;&nbsp;E-Mail: <a
        href="mailto:info@diginetmedia.de?subject=Kontaktanfrage%20%C3%BCber%20virtuelle%20360%C2%B0%20Tour"
        title="diginetmedia per E-Mail kontaktieren">info@diginetmedia.de</a>&nbsp;&nbsp;&bull;&nbsp;&nbsp;Internet: <a
        href="http://www.diginetmedia.de" title="zur diginetmedia-Homepage" target="_blank">www.diginetmedia.de</a>

    <div id="fb"><a href="https://www.facebook.com/diginetmedia360" target="_blank"></a></div>
</div>

<script type="text/javascript">
    //custom folderContent-function -> returns folders from root-folder
<?php

class ProjectIndexer {
    private $path = '';

    private static $projects = Array();

    function __construct()
    {
        $dir = explode('/', getcwd());
        $lastEntry = array_pop($dir);
        $this->path = getcwd();
        $this->dir = $lastEntry;
    }

    public function indexProjects() {
        self::indexDirectories();

        return self::$projects;
    }

    public function getFolders() {
        $folders = array();

        if (isset($_GET["folders"])) {
                        $folder = filter_var($_GET["folders"], FILTER_SANITIZE_STRING);
                        return "return 'folders:[" .$folder ."]'.fl_toObject(null, true);";
                    }
                    else {
                        if (self::$projects == array()) {
                            self::indexDirectories();
                        }

                        $projectFolders = self::$projects['.'];
                        foreach ($projectFolders as $key => $value) {
                           array_push($folders, $key);
                        }

                        return "return 'folders:[" . implode(",", $folders) ."]'.fl_toObject(null, true);";

                    }

    }

    private function indexDirectories() {
            $dirPattern = '/^([a-zA-Z]{2})\_(\d{5})/';
            $content = scandir($this->path);
            $project = Array();

            foreach ($content as $key => $value) {
                if (preg_match($dirPattern, $value)) {
                    $name = $this->getProjectName($value);

                    if ($name) {
                        $project[$value] = Array(
                            'name' => $name,
                        );

                        self::$projects['.'][$value] = $project[$value];
                        #array_push(self::$projects, $project[$value]);
                    }
                }
            }

        }

    private function getProjectName($projectID) {
        $xml = simplexml_load_file($this->path .'/'. $projectID . '/' . '_xml' . '/menu.xml');

        if (!$xml) {
            return null;
        } else {
            $xmlPath = $xml->xpath('/data/@title');
            $xmlString = (string) $xmlPath[0] ;
            return $xmlString;
        }
    }
}

$Projects = new ProjectIndexer();
$directory = $Projects->dir;

				?>

    function getFolderContent () {
        <?php
         echo $Projects->getFolders();

				?>
    }

    function getProjectContent() {
        <?php
        echo 'return ' . json_encode($Projects->indexProjects());

				?>
    }

    tour = new Tour();

    tour.projectSelect();
</script>

</body>
</html>
