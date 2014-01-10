// element: Das element in das Fehlernachrichten hinzugefügt werden.
var fehlerNachricht = function(element, nachricht, type) {
	if (typeof type == 'undefined') type = "fehler";
	
	var imgPath = "../img/error.png";
	if (type == "okay") imgPath = "../img/accept.png";
	if (type == "warnung") imgPath = "../img/warning.png";
	
	element.innerHTML += 
	"<div class='infobox " + type + "'>" +
		"<table border='0'>" +
			"<tr>" +
				"<td class='verticalMid'>" +
					"<img src='" + imgPath + "' width='16' height='16' />" + 
				"</td><td class='verticalMid'>" +
					nachricht + 
				"</td>" + 
			"</tr>" +
		"</table>" +
	"</div>";
}