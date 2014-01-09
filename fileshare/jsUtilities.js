// element: Das element in das Fehlernachrichten hinzugefügt werden.
var fehlerNachricht = function(element, nachricht) {
	element.innerHTML += "<div class='fehler'><img src='img/error.png' />" + nachricht + "</div>";
}