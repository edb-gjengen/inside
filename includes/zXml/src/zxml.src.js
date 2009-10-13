/*------------------------------------------------------------------------------
 * JavaScript zXml Library
 * Version 1.0
 * by Nicholas C. Zakas, http://www.nczonline.net/
 * Copyright (c) 2004-2005 Nicholas C. Zakas. All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 *------------------------------------------------------------------------------
 */  

 
var zXml = {
    useActiveX: (typeof ActiveXObject != "undefined"),
    useDom: document.implementation && document.implementation.createDocument,
    useXmlHttp: (typeof XMLHttpRequest != "undefined")
};

zXml.ARR_XMLHTTP_VERS = ["MSXML2.XmlHttp.5.0", "MSXML2.XmlHttp.4.0", 
                         "MSXML2.XmlHttp.3.0", "MSXML2.XmlHttp",
                         "Microsoft.XmlHttp"];

zXml.ARR_DOM_VERS = ["MSXML2.DOMDocument.5.0", "MSXML2.DOMDocument.4.0", 
                     "MSXML2.DOMDocument.3.0", "MSXML2.DOMDocument",
                     "Microsoft.XmlDom"];
                   
/**
 * Static class for handling XMLHttp creation.
 * @class
 * @scope public
 */                     
function zXmlHttp() {
}

/**
 * Creates an XMLHttp object.
 * @static
 * @scope public
 * @return An XMLHttp object.
 */
zXmlHttp.createRequest = function ()/*:XMLHttp*/ {

    if (zXml.useXmlHttp) {
        return new XMLHttpRequest();
    } else if (zXml.useActiveX) {
  
        if (!zXml.XMLHTTP_VER) {
            for (var i=0; i < zXml.ARR_XMLHTTP_VERS.length; i++) {
                try {
                    new ActiveXObject(zXml.ARR_XMLHTTP_VERS[i]);
                    zXml.XMLHTTP_VER = zXml.ARR_XMLHTTP_VERS[i];
                    break;
                } catch (oError) {                
                }
            }
        }
        
        if (zXml.XMLHTTP_VER) {
            return new ActiveXObject(zXml.XMLHTTP_VER);
        } else {
            throw new Error("Could not create XML HTTP Request.");
        }
    } else {
        throw new Error("Your browser doesn't support an XML HTTP Request.");
    }

};

/**
 * Indicates if XMLHttp is available.
 * @static
 * @scope public
 * @return True if XMLHttp is available, false if not.
 */
zXmlHttp.isSupported = function ()/*:Boolean*/ {
    return zXml.useXmlHttp || zXml.useActiveX;
};


/**
 * Static class for handling XML DOM creation.
 * @class
 * @scope public
 */
function zXmlDom() {

}

/**
 * Creates an XML DOM document.
 * @static
 * @scope public
 * @return An XML DOM document.
 */
zXmlDom.createDocument = function () /*:XMLDocument*/{

    if (zXml.useDom) {

        var oXmlDom = document.implementation.createDocument("","",null);

        oXmlDom.parseError = {
            valueOf: function () { return this.errorCode; },
            toString: function () { return this.errorCode.toString() }
        };
        
        oXmlDom.__initError__();
                
        oXmlDom.addEventListener("load", function () {
            this.__checkForErrors__();
            this.__changeReadyState__(4);
        }, false);

        return oXmlDom;        
        
    } else if (zXml.useActiveX) {
        if (!zXml.DOM_VER) {
            for (var i=0; i < zXml.ARR_DOM_VERS.length; i++) {
                try {
                    new ActiveXObject(zXml.ARR_DOM_VERS[i]);
                    zXml.DOM_VER = zXml.ARR_DOM_VERS[i];
                    break;
                } catch (oError) {                
                }
            }
        }
        
        if (zXml.DOM_VER) {
            return new ActiveXObject(zXml.DOM_VER);
        } else {
            throw new Error("Could not create XML DOM document.");
        }
    } else {
        throw new Error("Your browser doesn't support an XML DOM document.");
    }

};

/**
 * Indicates if an XML DOM is available.
 * @static
 * @scope public
 * @return True if XML DOM is available, false if not.
 */
zXmlDom.isSupported = function ()/*:Boolean*/ {
    return zXml.useDom || zXml.useActiveX;
};

//Code to make Mozilla DOM documents act more like MS DOM documents.
var oMozDocument = null;
if (typeof XMLDocument != "undefined") {
    oMozDocument = XMLDocument;
} else if (typeof Document != "undefined") {
    oMozDocument = Document;
}

if (oMozDocument && !window.opera) {

    oMozDocument.prototype.readyState = 0;
    oMozDocument.prototype.onreadystatechange = null;

    oMozDocument.prototype.__changeReadyState__ = function (iReadyState) {
        this.readyState = iReadyState;

        if (typeof this.onreadystatechange == "function") {
            this.onreadystatechange();
        }
    };

    oMozDocument.prototype.__initError__ = function () {
        this.parseError.errorCode = 0;
        this.parseError.filepos = -1;
        this.parseError.line = -1;
        this.parseError.linepos = -1;
        this.parseError.reason = null;
        this.parseError.srcText = null;
        this.parseError.url = null;
    };
    
    oMozDocument.prototype.__checkForErrors__ = function () {

        if (this.documentElement.tagName == "parsererror") {

            var reError = />([\s\S]*?)Location:([\s\S]*?)Line Number (\d+), Column (\d+):<sourcetext>([\s\S]*?)(?:\-*\^)/;

            reError.test(this.xml);
            
            this.parseError.errorCode = -999999;
            this.parseError.reason = RegExp.$1;
            this.parseError.url = RegExp.$2;
            this.parseError.line = parseInt(RegExp.$3);
            this.parseError.linepos = parseInt(RegExp.$4);
            this.parseError.srcText = RegExp.$5;
        }
    };
            
    oMozDocument.prototype.loadXML = function (sXml) {
    
        this.__initError__();
    
        this.__changeReadyState__(1);
    
        var oParser = new DOMParser();
        var oXmlDom = oParser.parseFromString(sXml, "text/xml");
 
        while (this.firstChild) {
            this.removeChild(this.firstChild);
        }

        for (var i=0; i < oXmlDom.childNodes.length; i++) {
            var oNewNode = this.importNode(oXmlDom.childNodes[i], true);
            this.appendChild(oNewNode);
        }
        
        this.__checkForErrors__();
        
        this.__changeReadyState__(4);

    };
    
    oMozDocument.prototype.__load__ = oMozDocument.prototype.load;

    oMozDocument.prototype.load = function (sURL) {
        this.__initError__();
        this.__changeReadyState__(1);
        this.__load__(sURL);
    };
    
    Node.prototype.__defineGetter__("xml", function () {
        var oSerializer = new XMLSerializer();
        return oSerializer.serializeToString(this, "text/xml");
    });

    Node.prototype.__defineGetter__("text", function () {
        var sText = "";
        for (var i = 0; i < this.childNodes.length; i++) {
            if (this.childNodes[i].hasChildNodes()) {
                sText += this.childNodes[i].text;
            } else {
                sText += this.childNodes[i].nodeValue;
            }
        }
        return sText;

    });

}

/**
 * Static class for handling XSLT transformations.
 * @class
 * @scope public
 */
function zXslt() {
}

/**
 * Transforms an XML DOM to text using an XSLT DOM.
 * @static
 * @scope public
 * @param oXml The XML DOM to transform.
 * @param oXslt The XSLT DOM to use for the transformation.
 * @return The transformed version of the string.
 */
zXslt.transformToText = function (oXml /*:XMLDocument*/, oXslt /*:XMLDocument*/)/*:String*/ {
    if (typeof XSLTProcessor != "undefined") {
        var oProcessor = new XSLTProcessor();
        oProcessor.importStylesheet(oXslt);
    
        var oResultDom = oProcessor.transformToDocument(oXml);
        var sResult = oResultDom.xml;
    
        if (sResult.indexOf("<transformiix:result") > -1) {
            sResult = sResult.substring(sResult.indexOf(">") + 1, 
                                        sResult.lastIndexOf("<"));
        }
    
        return sResult;     
    } else if (zXml.useActiveX) {
        return oXml.transformNode(oXslt);
    } else {
        throw new Error("No XSLT engine found.");
    }
};

/**
 * Static class for handling XPath evaluation.
 * @class
 * @scope public
 */
function zXPath() {

}

/**
 * Selects the first node matching a given XPath expression.
 * @static
 * @scope public
 * @param oRefNode The node from which to evaluate the expression.
 * @param sXPath The XPath expression.
 * @param oXmlNs An object containing the namespaces used in the expression. Optional.
 * @return An XML node matching the expression or null if no matches found.
 */
zXPath.selectNodes = function (oRefNode, sXPath, sXmlNs) {
    if (typeof XPathEvaluator != "undefined") {
    
        oXmlNs = oXmlNs || {};
        
        var nsResolver = function (sPrefix) {
    			  return oXmlNs[sPrefix];
        };
		
        var oEvaluator = new XPathEvaluator();
        var oResult = oEvaluator.evaluate(sXPath, oRefNode, nsResolver, 
                                          XPathResult.ORDERED_NODE_ITERATOR_TYPE, 
                                          null);

        var aNodes = new Array;
        
        if (oResult != null) {
            var oElement = oResult.iterateNext();
            while(oElement) {
                aNodes.push(oElement);
                oElement = oResult.iterateNext();
            }
        }
        
        return aNodes;
        
    } else if (zXml.useActiveX) {
    		if (oXmlNs) {
            var sXmlNs = "";
            for (var sProp in oXmlNs) {
                sXmlNs += "xmlns:" + sProp + "=" + oXmlNs[sProp] + " ";
            }
    			  oRefNode.ownerDocument.setProperty("SelectionNamespaces", sXmlNs);
    		}  		
        return oRefNode.selectNodes(sXPath);
    } else {
        throw new Error("No XPath engine found.");
    }

};

/**
 * Selects the first node matching a given XPath expression.
 * @static
 * @scope public
 * @param oRefNode The node from which to evaluate the expression.
 * @param sXPath The XPath expression.
 * @param oXmlNs An object containing the namespaces used in the expression.
 * @return An XML node matching the expression or null if no matches found.
 */
zXPath.selectSingleNode = function (oRefNode, sXPath, oXmlNs) {
    if (typeof XPathEvaluator != "undefined") {            
	
        oXmlNs = oXmlNs || {};
        
        var nsResolver = function (sPrefix) {
    			  return oXmlNs[sPrefix];
        };
    
        var oEvaluator = new XPathEvaluator();
        var oResult = oEvaluator.evaluate(sXPath, oRefNode, nsResolver,
                                          XPathResult.FIRST_ORDERED_NODE_TYPE, null);
    
        if (oResult != null) {
            return oResult.singleNodeValue;
        } else {
            return null;
        }              
    
    } else if (zXML.useActiveX) {
    		if (oXmlNs) {
            var sXmlNs = "";
            for (var sProp in oXmlNs) {
                sXmlNs += "xmlns:" + sProp + "=" + oXmlNs[sProp] + " ";
            }
    			  oRefNode.ownerDocument.setProperty("SelectionNamespaces", sXmlNs);
    		}    
        return oRefNode.selectSingleNode(sXPath);
    } else {
        throw new Error("No XPath engine found.")
    }

};

/**
 * General purpose XML serializer.
 * @class
 */
function zXMLSerializer() {

}

/**
 * Serializes the given XML node into an XML string.
 * @param oNode The XML node to serialize.
 * @return An XML string.
 */
zXMLSerializer.prototype.serializeToString = function (oNode /*:Node*/)/*:String*/ {

    var sXml = "";
    
    switch (oNode.nodeType) {
        case 1: //element
            sXml = "<" + oNode.tagName;
            
            for (var i=0; i < oNode.attributes.length; i++) {
                sXml += " " + oNode.attributes[i].name + "=\"" + oNode.attributes[i].value + "\"";
            }
            
            sXml += ">";
            
            for (var i=0; i < oNode.childNodes.length; i++){
                sXml += this.serializeToString(oNode.childNodes[i]);
            }
            
            sXml += "</" + oNode.tagName + ">";
            break;
            
        case 3: //text node
            sXml = oNode.nodeValue;
            break;
        case 4: //cdata
            sXml = "<![CDATA[" + oNode.nodeValue + "]]>";
            break;
        case 7: //processing instruction
            sXml = "<?" + oNode.nodevalue + "?>";
            break;
        case 8: //comment
            sXml = "<!--" + oNode.nodevalue + "-->";
            break;
        case 9: //document
            for (var i=0; i < oNode.childNodes.length; i++){
                sXml += this.serializeToString(oNode.childNodes[i]);
            }
            break;
            
    }  
    
    return sXml;
};