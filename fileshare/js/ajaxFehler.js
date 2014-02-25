$(document).ajaxError(function() {
	if(confirm("Der Server konnte nicht erreicht werden. Soll die Seite neu geladen werden?")){
		location.reload();
	}
});