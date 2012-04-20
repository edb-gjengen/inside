function makeZipRequest(url) {
    if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}
	else if(window.ActiveXObject){
		request = new ActiveXObject("MSXML2.XMLHTTP");
	}
    sendZipRequest(url);
}

function sendZipRequest(url){
	request.onreadystatechange = zipOnResponse;
	request.open("GET", url, true);
	request.send(null);
}

function checkZipReadyState(obj){
	if(obj.readyState == 0) { document.getElementById('postarea').innerHTML = "Sender foresp?rsel..."; }
	if(obj.readyState == 1) { document.getElementById('postarea').innerHTML = "Henter data..."; }
	if(obj.readyState == 2) { document.getElementById('postarea').innerHTML = "Laster data..."; }
	if(obj.readyState == 3) { document.getElementById('postarea').innerHTML = "Data lastet ferdig..."; }
	if(obj.readyState == 4)	{
		if(obj.status == 200){
			return true;
		}
		else if(obj.status == 404){
			// Add a custom message or redirect the user to another page
			document.getElementById('postarea').innerHTML = "File not found";
		}else{
			document.getElementById('postarea').innerHTML = "There was a problem retrieving the XML.";
		}
		return false;
	}
}

function zipOnResponse() {
	if(checkZipReadyState(request)){
        //alert(request.responseXML);
		//alert(request.responseText);
		var response = request.responseXML.documentElement;
		var postarea  = response.getElementsByTagName('postarea')[0].firstChild.data;
        document.getElementById('postarea').innerHTML = postarea;
        if (postarea == "ugyldig postnummer"){
            document.getElementById("zipcode").focus();
        }
    }
}