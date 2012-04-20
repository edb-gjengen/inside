function makeUsernameRequest(url) {
    if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}
	else if(window.ActiveXObject){
		request = new ActiveXObject("MSXML2.XMLHTTP");
	}
    sendUsernameRequest(url);
}

function sendUsernameRequest(url){
	request.onreadystatechange = usernameOnResponse;
	request.open("GET", url, true);
	request.send(null);
}

function checkUsernameReadyState(obj){
	if(obj.readyState == 4)	{
		if(obj.status == 200){
			return true;
		}
		else if(obj.status == 404){
			// Add a custom message or redirect the user to another page
			alert("error checking username");
		}else{
			alert("There was a problem retrieving the XML.");
		}
		return false;
	}
}

function usernameOnResponse() {
	if(checkUsernameReadyState(request)){
        //alert(request.responseXML);
		//alert(request.responseText);
		var response = request.responseXML.documentElement;
		var status  = response.getElementsByTagName('status')[0].firstChild.data;
		if (status == "false") {
			alert("Dette brukernavnet er i bruk p� forumet.\n\nOm du har denne brukeren p� forumet, kan du bruke samme brukernavn ogs� p� Inside.\n\nFor � bekrefte at dette er ditt brukernavn m� du skrive inn samme passord som du har p� forumet.\n\nOm dette ikke er ditt brukernavn m� du velge et annet.\n\nB�de brukernavn og passord kan endres senere.");
		}
    }
}