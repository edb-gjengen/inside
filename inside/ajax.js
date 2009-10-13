function requestConcertInfo(callerid, target, programid) {
  var iId = document.getElementById(callerid).value;
  var pattern = /\d+/;
  var match = pattern.exec(callerid);
  var gigNo = parseInt(match[0]);
  
  var oXmlHttp = zXmlHttp.createRequest();
  oXmlHttp.open("get", "getconcertinfo.php?gigid=" + iId + "&store=" + gigNo + "&programid=" + programid, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
        displayConcertInfo(oXmlHttp.responseText, target);
      }else {
        displayConcertInfo("An error occured: " + oXmlHttp.statusText, target);
      }
    }
  };  
  oXmlHttp.send(null);
}

function grantCardNumber(userid) {
  var form = document.getElementById("user_"+userid+"_cardno_form");
  form.style.display = "none";

  var oXmlHttp = zXmlHttp.createRequest();
  var sURL = "grantcardno.php?userid=" + userid;
  oXmlHttp.open("get", sURL, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
      	//alert(oXmlHttp.responseXML);
		//alert(oXmlHttp.responseText);
		var response = oXmlHttp.responseXML.documentElement;
		var cardno  = response.getElementsByTagName('cardno')[0].firstChild.data;
		var expires = response.getElementsByTagName('expires')[0].firstChild.data;
        document.getElementById('user_'+userid+'_cardno').innerHTML = cardno;
        document.getElementById('user_'+userid+'_expires').innerHTML = expires;
      	
		form.parentNode.innerHTML = "Kortnummer "+cardno+" er tildelt";
      }else {
        alert("An error occured: " + oXmlHttp.statusText);
      }
    }
  };  
  oXmlHttp.send(null);
}

function setHasCard(userid) {
  var form = document.getElementById("user_"+userid+"_hascard_form");
  form.style.display = "none";
  
  var oXmlHttp = zXmlHttp.createRequest();
  var sURL = "sethascard.php?userid=" + userid;
  oXmlHttp.open("get", sURL, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
      	//alert(oXmlHttp.responseXML);
		//alert(oXmlHttp.responseText);
		var response = oXmlHttp.responseXML.documentElement;
		var lastSticker = response.getElementsByTagName('laststicker')[0].firstChild.data;
        document.getElementById('user_'+userid+'_laststicker').innerHTML = lastSticker;
      	
		form.parentNode.innerHTML = "Medlemskort er produsert";
      }else {
        alert("An error occured: " + oXmlHttp.statusText);
      }
    }
  };  
  oXmlHttp.send(null);
}

function updateUserExpiry(userid) {
  var input = document.getElementById("newExpiryDate_"+userid);
  var value = input.value;
  var v = document.getElementById("user_"+userid+"_expires");
  v.innerHTML = value;
 
  var oXmlHttp = zXmlHttp.createRequest();
  var sURL = "updateuserexpiry.php?userid=" + userid + "&value="+value;
  oXmlHttp.open("get", sURL, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
      	//alert(oXmlHttp.responseXML);
		//alert(oXmlHttp.responseText);
		//var response = oXmlHttp.responseXML.documentElement;
      }else {
        alert("An error occured: " + oXmlHttp.statusText);
      }
    }
  };  
  oXmlHttp.send(null);
}

function updateLastSticker(userid) {
  var input = document.getElementById("newStickerDate_"+userid);
  var value = input.value;
  var v = document.getElementById("user_"+userid+"_laststicker");
  v.innerHTML = value;
 
  var oXmlHttp = zXmlHttp.createRequest();
  var sURL = "updatelaststicker.php?userid=" + userid + "&value="+value;
  oXmlHttp.open("get", sURL, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
      	//alert(oXmlHttp.responseXML);
		//alert(oXmlHttp.responseText);
		//var response = oXmlHttp.responseXML.documentElement;
      }else {
        alert("An error occured: " + oXmlHttp.statusText);
      }
    }
  };  
  oXmlHttp.send(null);
}

/***********************************
 * set order status to delivered
 ***********************************/
function setOrderDeliveryStatus(orderid) {
	var form = document.getElementById("order_"+orderid+"_update_form");
	form.style.display = "none";
	var input = document.getElementById("newDeliveryStatus_"+orderid);
	var value = input.value;
	
	var oXmlHttp = zXmlHttp.createRequest();
	var sURL = "setorderdelivery.php?orderid=" + orderid + "&newstatusid=" + value;
	oXmlHttp.open("get", sURL, true);
	oXmlHttp.onreadystatechange = function() {
		if (oXmlHttp.readyState == 4) {
			if (oXmlHttp.status == 200) {
				//alert(oXmlHttp.responseXML);
				//alert(oXmlHttp.responseText);
				var response = oXmlHttp.responseXML.documentElement;
				var status = response.getElementsByTagName('deliverystatus')[0].firstChild.data;
			
				if (status == 1) {
					document.getElementById('order_'+orderid+'_deliverystatus').innerHTML = "levert";
					form.parentNode.innerHTML = "Orderen er levert";
				} else {
					document.getElementById('order_'+orderid+'_deliverystatus').innerHTML = "ikke levert";
					form.parentNode.innerHTML = "Orderen er ikke levert";
				}
			} else {
				alert("An error occured: " + oXmlHttp.statusText);
			}
		}
	};
	oXmlHttp.send(null);
}

function setWeekText(sText, iId, sType) {
  
  var oXmlHttp = zXmlHttp.createRequest();
  sText = escape(sText);
  var sURL = "setweekprogramtext.php?wid=" + iId + "&text=" + sText + "&type=" + sType;
  oXmlHttp.open("get", sURL, true);
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
      	//alert(oXmlHttp.responseText);
      }else {
        alert("An error occured: " + oXmlHttp.statusText);
      }
    }
  };  
  oXmlHttp.send(null);
}

function displayConcertInfo(sText, target) {
  var divConcertInfo = document.getElementById(target);
  divConcertInfo.innerHTML = sText;
}

function saveResult(sMessage) {
  var divStatus = document.getElementById("divStatus");
  divStatus.innerHTML = "Request completed: " + sMessage;
}

function getRequestBody(oForm) {
  var aParams = new Array();
  
  for (var i = 0; i < oForm.elements.length; i++){
    var sParam = encodeURIComponent(oForm.elements[i].name);
    sParam += "=";
    sParam += encodeURIComponent(oForm.elements[i].value);
    aParams.push(sParam);
  }
  return aParams.join("&");
}

function sendRequest() {
  var oForm = document.forms[0];
  var sBody = getRequestBody(oForm);
  
  var oXmlHttp = zXmlHttp.createRequest();
  oXmlHttp.open("post", oForm.action, true);
  oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  
  oXmlHttp.onreadystatechange = function() {
    if (oXmlHttp.readyState == 4){
      if (oXmlHttp.status == 200) {
        saveResult(oXmlHttp.responseText);
      }else {
        saveResult("An error occured: " + oXmlHttp.statusText);
      }
    }
  
  };
  oXmlHttp.send(sBody);
}  
