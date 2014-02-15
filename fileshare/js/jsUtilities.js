var erstelleFehlerNachricht = function(nachricht, type, path) {
	path = typeof path !== 'undefined' ? path : '../';
	if (typeof type == 'undefined') type = "fehler";
	
	var imgPath = path + "img/error.png";
	if (type == "okay") imgPath = path + "img/accept.png";
	if (type == "warnung") imgPath = path + "img/warning.png";
	
	return (
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
		"</div>");
}

// Nimmt ein Objekt der Form:
// {
//	liste: [
//		{ typ: ["fehler"/"warnung"/"okay"], nachricht: "Meine Fehlernachricht" },
//		{ typ: ["fehler"/"warnung"/"okay"], nachricht: "Noch mehr Nachrichten" },
//		{ typ: ["fehler"/"warnung"/"okay"], nachricht: "Und sie werden dümmer" },
//	],
//	path: [relativer path zu dem "fileshare/" Verzeichnis (um auf richtige weise auf "/img/" zu referenzieren)
// }
var fehlerNachrichten = function(elementQuery, nachrichten) {
	if (nachrichten.liste.length != 0) {
		$(nachrichten.liste).each(function(i, nrt) {
			console.log(nrt.typ + "-Nachricht: \"" + nrt.nachricht + "\"");
			return fehlerNachricht(elementQuery, nrt.typ, nrt.nachricht, nachrichten.path);
		});
	}
}

var fehlerNachricht = function(elementQuery, typ, nachricht, path) {
	var nachricht = $(erstelleFehlerNachricht(nachricht, typ, path));
	nachricht.delay(10000).hide(500);
	$(elementQuery).append(nachricht);
}

//base64 Fallback, falls von browser nicht unterstützt
if((!!forge)&&(!atob)){
	atob = forge.util.decode64;
}
if((!!forge)&&(!btoa)){
	btoa = forge.util.encode64;
}