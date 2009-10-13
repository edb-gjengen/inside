function makeCatRequest(url) {
    if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}
	else if(window.ActiveXObject){
		request = new ActiveXObject("MSXML2.XMLHTTP");
	}
    sendCatRequest(url);
}

function sendCatRequest(url){
	request.onreadystatechange = catOnResponse;
	request.open("GET", url, true);
	request.send(null);
}

function checkCatReadyState(obj){
	if(obj.readyState == 0) { document.getElementById('statusField').innerHTML = "Sender forespørsel..."; }
	if(obj.readyState == 1) { document.getElementById('statusField').innerHTML = "Henter data..."; }
	if(obj.readyState == 2) { document.getElementById('statusField').innerHTML = "Laster data..."; }
	if(obj.readyState == 3) { document.getElementById('statusField').innerHTML = "Data lastet ferdig..."; }
	if(obj.readyState == 4) {
		if(obj.status == 200) {
			return true;
		}else if(obj.status == 404) {
			// Add a custom message or redirect the user to another page
			document.getElementById('statusField').innerHTML = "File not found";
		}else {
			document.getElementById('statusField').innerHTML = "There was a problem retrieving the XML.";
		}
	}
}

function catOnResponse() {
	if(checkCatReadyState(request)){
        //alert(request.responseXML);
		//alert(request.responseText);
		var response = request.responseXML.documentElement;
        var type  = response.getElementsByTagName('actiontype')[0].firstChild.data;
        var cat_type  = response.getElementsByTagName('cat-type')[0].firstChild.data;
        if (response.getElementsByTagName('id')[0].firstChild != null){
            var id = response.getElementsByTagName('id')[0].firstChild.data;
        }else {
			document.getElementById('statusField').innerHTML = "Kategorien finnes fra før.";
            return;
        }
        var table = document.getElementById(cat_type + '-cat-list');
        
        if (type == 'add'){
            var title = response.getElementsByTagName('title')[0].firstChild.data;
			document.getElementById('statusField').innerHTML = "Kategorien " + title  + " er lagt til. Om den ikke vises over, forsøk å laste siden på nytt (F5)";
            var text  = response.getElementsByTagName('text')[0].firstChild.data;
            var edit_href  = response.getElementsByTagName('edit-href')[0].firstChild.data;
            var edit_text  = response.getElementsByTagName('edit-text')[0].firstChild.data;
            var delete_href  = response.getElementsByTagName('delete-href')[0].firstChild.data;
            var delete_text  = response.getElementsByTagName('delete-text')[0].firstChild.data;
            var r = document.createElement("tr");
            r.setAttribute('id', cat_type +'category' + id);
            r.insertCell(0);
            r.cells[0].appendChild(document.createTextNode(title));
            r.insertCell(1);
            r.cells[1].appendChild(document.createTextNode(text));
            r.insertCell(2);
            var aEdit = document.createElement("a");
            aEdit.setAttribute('href', edit_href);
            aEdit.appendChild(document.createTextNode(edit_text));
            r.cells[2].appendChild(aEdit);
            r.insertCell(3);
            var aDelete = document.createElement("a");
            aDelete.setAttribute('href', delete_href);
            aDelete.appendChild(document.createTextNode(delete_text));
            r.cells[3].appendChild(aDelete);
            table.tBodies[0].appendChild(r);
        }else if (type == 'remove'){
			document.getElementById('statusField').innerHTML = "Kategori slettet.";
            var r = document.getElementById(cat_type + 'category' + id);
            table.tBodies[0].removeChild(r);
        }
    }
}