function makeRequest(url) {
    if(window.XMLHttpRequest) {
		request = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		request = new ActiveXObject("MSXML2.XMLHTTP");
	}	
	sendRequest(url);
}

function sendRequest(url) {
	request.onreadystatechange = onResponse;
	request.open("GET", url, true);
	request.send(null);
}

function checkReadyState(obj) {
	if(obj.readyState == 0) { document.getElementById('notice').innerHTML = "Sender foresp?rsel..."; }
	if(obj.readyState == 1) { document.getElementById('notice').innerHTML = "Henter data..."; }
	if(obj.readyState == 2) { document.getElementById('notice').innerHTML = "Laster data..."; }
	if(obj.readyState == 3) { document.getElementById('notice').innerHTML = "Data lastet ferdig..."; }
	if(obj.readyState == 4)	{
		if(obj.status == 200) {
			return true;
		}else if(obj.status == 404)	{
			// Add a custom message or redirect the user to another page
			document.getElementById('notice').innerHTML = "File not found";
		}else {
			document.getElementById('notice').innerHTML = "There was a problem retrieving the XML.";
		}
	}
}

function onResponse(){
	if(checkReadyState(request)){
        //alert(request.responseXML);
		//alert(request.responseText);
		var response = request.responseXML.documentElement;
		var posId    = response.getElementsByTagName('positionid')[0].firstChild.data;
		var title    = response.getElementsByTagName('title')[0].firstChild.data;
		var text     = response.getElementsByTagName('text')[0].firstChild.data;
        document.getElementById('positionid').value = posId;
        document.getElementById('name').value = title;
		document.getElementById('text').value = text;
        tinyMCE.updateContent('text');
	}
}