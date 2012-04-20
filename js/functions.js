function initializeUsersForm(){
	var username  = document.getElementById("username");
    var city      = document.getElementById("city");
    var state     = document.getElementById("state");
    var country   = document.getElementById("country");
    var postarea  = document.getElementById("postarea");

    var division  = document.getElementById("division");
    var group     = document.getElementById("group_id");

    var checkType = document.getElementById("addresstype");

    checkType.onclick = function() {
    	if (checkType.checked){
	        city.parentNode.parentNode.style.display = "";
    	    state.parentNode.parentNode.style.display = "";
        	country.parentNode.parentNode.style.display = "";       
	        postarea.parentNode.parentNode.style.display = "none";        
    	}else {
	        city.parentNode.parentNode.style.display = "none";
    	    state.parentNode.parentNode.style.display = "none";
        	country.parentNode.parentNode.style.display = "none";       
	        postarea.parentNode.parentNode.style.display = "";                	
    	}
    }

    if (checkType.getAttribute("checked") == "checked"){
        city.parentNode.parentNode.style.display = "";
        state.parentNode.parentNode.style.display = "";
        country.parentNode.parentNode.style.display = "";
        postarea.parentNode.parentNode.style.display = "none";        
    }else {
        city.parentNode.parentNode.style.display = "none";
        state.parentNode.parentNode.style.display = "none";
        country.parentNode.parentNode.style.display = "none";       
    }

	//division
    if (division != null){
	    var checkDiv = document.getElementById("active");
			
	    checkDiv.onclick = function() {
		    if (checkDiv.checked) {
	        	division.parentNode.parentNode.style.display = "";
	        }else {
		        division.parentNode.parentNode.style.display = "none";	
		    }
	    }
	
	    if (checkDiv.checked){
	        division.parentNode.parentNode.style.display = "";
	    }else {
	        division.parentNode.parentNode.style.display = "none";
	    }
	}	

	//groups
    if (group != null){
	    var checkGroup = document.getElementById("group");
	
	    checkGroup.onclick = function() {
		    if (checkGroup.checked) {
	        	group.parentNode.parentNode.style.display = "";
	        }else {
		        group.parentNode.parentNode.style.display = "none";	
		    }
	    }
	
	    if (checkGroup.checked){
	        group.parentNode.parentNode.style.display = "";
	    }else {
	        group.parentNode.parentNode.style.display = "none";
	    }
	}
}


function initializeJobForm(elem){
    var radioOn  = document.getElementById("job-true");
    var radioOff = document.getElementById("job-false");
    var div = document.getElementById(elem);

    radioOn.onclick = function() {
        cssjs('remove', div, 'disabled');
    }

    radioOff.onclick = function() {
        cssjs('add', div, 'disabled');
    }

}

function repeatElement(id){
    var element = document.getElementById(id);
    alert('repeat ' + element + "\nDenne funksjonen kommer snart!");
}

function validateSize(id, size){
  var element = document.getElementById(id);
  if (element.value.length > size){
   alert("Max "+size+" characters!");
   element.focus();
   return false;
  }else {
    return true;
  }
}

function validateDatetime(id){
  var element = document.getElementById(id);
  var date = element.value;
  
  var reg = /^(19|20\d{2})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|[3][01])\s(([01][0-9]|[2][0-3]):[0-5][0-9])$/g;
  var found = date.match(reg);
  if (found != null){
      return true;
  }else {
      alert("Ugyldig tidsformat!\n\nAngi tid som yyyy-mm-dd hh:mm\n\n" + 
            "F. eks.: 2005-10-02 18:13\n\nVennligst ogs? p?se at verdiene er gyldige.");
      element.focus();
      return false;
  }
}

function validateDate(id){
  var element = document.getElementById(id);
  var date = element.value;

  if (date.length == 8) {
  	date = date.substr(0, 4) + "-" + date.substr(4, 2) + "-" + date.substr(6, 2);
	element.value = date;
  }
  
  var reg = /^((19|20)\d{2})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|[3][01])$/g;
  var found = date.match(reg);
  if (found != null){
      return true;
  }else {
      alert("Ugyldig tidsformat!\n\nAngi tid som yyyy-mm-dd\n\n" + 
            "F. eks.: 2005-10-02\n\nVennligst ogs? p?se at verdiene er gyldige.");
      element.focus();
      return false;
  }
}

function validateTime(id){
  var element = document.getElementById(id);
  var time = element.value;
  var error = false;

  if (time.length == 1) {
	if (isNaN(time)){
		error = true;
	}else {
		time = "0" + time + ":00";
		element.value = time;
	}
  }else if (time.length == 2) {
  	if (isNaN(time) || time >= 24){
		error = true;
	}else {
		time = time + ":00";
		element.value = time;
	}
  }else if (time.length == 4) {
	if (isNaN(time)){ 
		error = true;
	}else { 
	 	if (time.substr(2, 2) > 59){
			time = time.substr(0,2) + "59";
		}
		time = time.substr(0, 2) + ":" + time.substr(2, 2);
		element.value = time;
		
	}  	
  }else if (time.length == 5) {
	if (isNaN(time.substr(0, 2)) || isNaN(time.substr(3, 2))){
		error = true;
	}else {
	 	if (time.substr(3, 2) > 59){
			time = time.substr(0,2) + ":59";
		}
		time = time.substr(0, 2) + ":" + time.substr(3, 2);
		element.value = time;
	}  	
  }else {
  	error = true;
  }

  if (!error){
      return true;
  }else {
      alert("Ugyldig tidsformat!\n\nAngi tid som hh:mm\n\n" + 
            "F. eks.: 18:13\n\nVennligst ogs&aring; p&aring;se at verdiene er gyldige.");
      element.focus();
      return false;
  }
}

function validateEmpty(id, tekst){
    var element = document.getElementById(id);
    if (element.value.length == 0){
        alert(tekst+" must be filled in!");
        element.focus();
        return false;
    }else {
        return true;
    }
}

function validateAllFieldsNotEmpty(id){
    var fields =  document.getElementById(id).getElementsByTagName('input');
    var errorMsg = '';
    for (var i = 0; i < fields.length; i++){
        if (fields[i].value == ''){
            errorMsg += fields[i].getAttribute('name') + " m? angis\n";
        }
    }

    if (errorMsg != ''){
        alert(errorMsg);
        return false;
    }else {
        return true;
    }
}

function checkUsername(username){
    makeUsernameRequest("../inside/ajax.php?action=checkUsername&username=" + username);
}

function checkZip(zipCode){
    var t = document.getElementById("addresstype").checked;
    if (t){
    	return true;
    }else {
        var z = zipCode.value;
        makeZipRequest("../inside/ajax.php?action=checkZip&zip=" + z);
    }
}

function addCategory(type){
    var title = document.getElementById("title").value;
    var text = document.getElementById("text").value;
    var url = "../inside/ajax.php?action=register-" + type + "category&title=" + title + "&text=" + text;
    makeCatRequest(url);
}

function deleteObject(type, id){
    var url = "../inside/ajax.php?action=delete-" + type + "&" +  type + "id=" + id;
    makeCatRequest(url);
}

function textCounter(field, id, maxlimit) {
  var c = document.getElementById(id);
  var f = document.getElementById(field);
  if (c != null){
      c.value = maxlimit - f.value.length;
  }
}

function validateURL(element){
  var str = element.value;
  if (element.value.substr(0, 7) == "http://"){
      //alert("URL skal ikke starte med 'http://'!");
    element.value = element.value.substr(7, element.value.length);
  }
}

function toggleDisplay(id){
	var element = document.getElementById(id);
  	if (element.style.display == "none"){
    	element.style.display = '';//default
  	}else {
    	element.style.display = "none";
  	}
}

function toggleDisplayObject(element){
  	if (element.style.display == "none"){
    	element.style.display = '';//default
  	}else {
    	element.style.display = "none";
  	}
}

function toggleElements(tag, className, caller) {
	if (caller.checked) {
		var value = "";
	}else {
		var value = "none";
	}
	var elements = document.getElementsByTagName(tag);
	for (var i = 0; i < elements.length; i++) {
		if (elements[i].className == className) {
			elements[i].style.display = value;
		}
	}
}

function toggleText(oObject, tA, tB) {
	if (oObject.innerHTML == tA) {
		oObject.innerHTML = tB;
	}else {
		oObject.innerHTML = tA;	
	}
}

function formFieldShowHelp(oEvent, oField, tMessage) {
	
	var oTd = oField.parentNode;
	oTd.style.position = "relative";
	var oDiv = document.createElement("div");
	oDiv.innerHTML = tMessage + '<div class="helpMessageClose" onclick="toggleDisplayObject(this.parentNode)">X</div>';
	oDiv.className = "formHelpMessage";
	if (oEvent.pageY) {
		var iTop = oEvent.pageY - 10;
		var iLeft = oEvent.pageX - 10;
		oDiv.style.position = "absolute";
		oDiv.style.top = iTop+"px";
		oDiv.style.left = iLeft+"px";
	}else {
		oDiv.style.position = "relative";
	}

	oTd.appendChild(oDiv);
}

function formFieldHideHelp(oField) {
	var oTd = oField.parentNode;	
	oTd.removeChild(oTd.lastChild);
}

function ajaxFindUser(oField) {
	var divParent = oField.parentNode;
	divParent.style.position = "relative";
	var oDiv = document.createElement("div");
	
	oDiv.innerHTML = oField.value + '<div class="helpMessageClose" onclick="toggleDisplayObject(this.parentNode)">X</div>';
	oDiv.className = "formHelpMessage";
	oDiv.style.position = "absolute";
	oDiv.style.top = "27px";
	oDiv.style.left = "100px";

	divParent.appendChild(oDiv);
}

function StringBuffer() {
    this.__strings__ = new Array;
}

StringBuffer.prototype.append = function(str){
    this.__strings__.push(str);
};

StringBuffer.prototype.toString = function(){
    return this.__strings__.join("");
};
