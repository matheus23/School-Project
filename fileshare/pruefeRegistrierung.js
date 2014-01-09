//Einstellungen für Passwort-/Benutzernamenlänge
var minPasswortLaenge = 5;
var maxPasswortLaenge = 50;
var minBenutzernamenLaenge = 3;

//Elemente der HTML-Seite werden als javascript-variabeln repräsentiert
var formular = document.getElementById("formular");
var benutzernameInput = document.getElementById("Bn");
var passwortInput = document.getElementById("Pw");
var passwortBestaetigtInput = document.getElementById("Pwb");
var emailInput = document.getElementById("email");
var fehlerListe = document.getElementById("fehlerListe");

//Funktion, die beim Abschicken aufgerufen wird
formular.onsubmit=function(){
	//fehlerliste wird geleert
	fehlerListe.innerHTML = "";
	//Formulardaten werden übernommen
	var benutzername = benutzernameInput.value;
	var passwort = passwortInput.value;
	var passwortBestaetigt = passwortBestaetigtInput.value;
	var email = emailInput.value;
	var okay = true;
	//Überprüfung, ob alle felder ausgefüllt
	if((benutzername.length==0)||(passwort.length==0)||(passwortBestaetigt.length==0)||(email.length==0)){
		fehlerNachricht(fehlerListe, 'Alle Felder müssen ausgefüllt sein.');
		okay = false; // Wenn nicht, abbruch
	}
	//Überprüfung, ob Passwörter gleich
	if(passwortInput.value!=passwortBestaetigtInput.value){
		fehlerNachricht(fehlerListe, 'Das Passwort stimmt nicht mit der Wiederholung überein');
		okay = false; // Wenn nicht, abbruch
	}
	//Überprüfung, ob Passwort nicht zu lang/kurz ist
	if((passwortInput.value.length<minPasswortLaenge)||(passwortInput.value.length>maxPasswortLaenge)){
		fehlerNachricht(fehlerListe, 'Das Passwort muss mindestens '+minPasswortLaenge+' und höchstens '+maxPasswortLaenge+' Zeichen lang sein.');
		okay = false; // Wenn nicht, abbruch
	}
	//Überprüfung, ob Benutzername nicht zu kurz ist
	if(benutzernameInput.value.length<minBenutzernamenLaenge){
		fehlerNachricht(fehlerListe, 'Der Benutzername muss mindestens '+minBenutzernamenLaenge+' Zeichen lang sein.');
		okay = false; // Wenn nicht, abbruch
	}
	return okay;//Wenn alles in ordnung wird Formular abgesendet
}