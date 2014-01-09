// element: Das element in das Fehlernachrichten hinzugefügt werden.
var fehlerNachricht = function(element, nachricht, type) {
	if (typeof type == 'undefined') type = "fehler";
	element.innerHTML += 
	"<div class='infobox " + type + "'>" +
		"<table border='0'>" +
			"<tr>" +
				"<td class='verticalMid'>" +
					"<img src='img/error.png' width='16' height='16' />" + 
				"</td><td class='verticalMid'>" +
					nachricht + 
				"</td>" + 
			"</tr>" +
		"</table>" +
	"</div>";
}