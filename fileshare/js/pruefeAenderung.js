//Einstellungen für Passwort-/Benutzernamenlänge
var minPasswortLaenge = 5;
var maxPasswortLaenge = 50;
var minBenutzernamenLaenge = 3;

//Elemente der HTML-Seite werden als javascript-variabeln repräsentiert
var formular = document.getElementById("formular");
var passwortInput = document.getElementById("NPw");
var passwortBestaetigtInput = document.getElementById("NPwb");
var fehlerListe = "#fehlerListe";

//Funktion, die beim Abschicken aufgerufen wird
formular.onsubmit=function(){
	//Formulardaten werden übernommen
	var passwort = passwortInput.value;
	var passwortBestaetigt = passwortBestaetigtInput.value;
	var okay = true;
	//Überprüfung, ob alle felder ausgefüllt
	//Überprüfung, ob Passwörter gleich
	if(passwortInput.value!=passwortBestaetigtInput.value){
		fehlerNachricht(fehlerListe,'fehler', 'Das Passwort stimmt nicht mit der Wiederholung überein','../');
		okay = false; // Wenn nicht, abbruch
	}
	//Überprüfung, ob Passwort nicht zu lang/kurz ist
	if((passwortInput.value.length<minPasswortLaenge)||(passwortInput.value.length>maxPasswortLaenge)){
		fehlerNachricht(fehlerListe,'fehler','Das Passwort muss mindestens '+minPasswortLaenge+' und höchstens '+maxPasswortLaenge+' Zeichen lang sein.','../');
		okay = false; // Wenn nicht, abbruch
	}
	return okay;//Wenn alles in ordnung wird Formular abgesendet
}