$("#editor > .fenster").hide(0);
var dateiliste;
var dateischluessel;
var schluesselVorhanden = false;
var aktuellerSchluessel;
var ausgewaehltID;
var ausgewaehlt;
var dateischluesselUnverschluesselt;

var pfadZuOrdnerFileshare = "../../";

updateDateiSchluessel();
updateDateiListe();

function updateDateiSchluessel(){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"schickeDateischluesselListe",CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			dateischluessel = antwortObjekt.schluesselArray;
		}
	});	
}

$("#dateiListe > .listenelement").click(function(){
	$("#editor > .label").text("Download: "+$(this).text());
	$("#editor").show(0);
	ausgewaehltID = $(this).data("ID");
	ausgewaehlt = $.grep(dateiliste,function(datei,index){
		if (datei.ID == ausgewaehltID) return true;
		return false;
	})[0];
	schluesselVorhanden = false;
	$("#schluessel").text("Du hast keinen Schluessel für diese Datei").css("color","red");
	$.each(dateischluessel,function(index,schluessel){
		if (ausgewaehlt.SchluesselID==schluessel.VersionID){
			schluesselVorhanden = true;
			aktuellerSchluessel = schluessel;
			$("#schluessel").text(schluessel.VersionID).css("color","green");
		}
	});
});

function updateDateiListe(){
	$("#dateiListe > .listenelement").remove();
	$.ajax({
		type: "POST",
		url: "dateiAjax.php",
		async:false,
		data: {aktion:"holeDateien",CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			dateiliste=antwortObjekt.dateien;
			$.each(dateiliste,function(index,datei){
				var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
				listenelement.data("ID",datei.ID);
				var label = $("<span>").addClass("listenlabel").text(datei.Name);//Nutzername/Email auf Listenlabel
				var username = $("<div>").addClass("nutzerlabel").text(" - von " + datei.Nutzername);
				listenelement.append(label);
				listenelement.append(username);
				$("#dateiListe").append(listenelement);
			});
		}
	});
}

$("#runterladen").click(function(){
	var passwort = $("#dateischluesselPasswort").val();
	if (passwort.length==0){
		fehlerNachricht("#fehlerListe", "fehler", "Du musst ein Passwort angeben", pfadZuOrdnerFileshare);
		return;
	}
	if (!schluesselVorhanden){
		fehlerNachricht("#fehlerListe", "fehler", "Du kannst diese Datei nicht entschlüsseln", pfadZuOrdnerFileshare);
		return;
	}
	var dateiURL = frageNachURL();
	if (!dateiURL) return;
	holeUndEntschluesseleDatei(dateiURL,passwort);
});

function frageNachURL(){
	var url=false;
	$.ajax({
		type: "POST",
		url: "dateiAjax.php",
		async:false,
		data: {aktion:"frageNachURL",ausgewaehltID:ausgewaehltID,CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			if (!antwortObjekt.url) return;
			url = antwortObjekt.url;
		}
	});
	return url;
}

function holeUndEntschluesseleDatei(dateiURL,passwort){
	$.get(dateiURL, function(datei){
		//Entschlüssele lokalen Schlüssel
		if(!bereiteDateischluesselVor(passwort)) {
			fehlerNachricht("#fehlerListe", "fehler", "Falsches Passwort oder beschädigter Schlüssel", pfadZuOrdnerFileshare);
			return;
		}
		
		//Liest die notwendigen Informationen wieder ein, die an festen Stellen in der Datei gespeichert sind
		var AESKeyVerschluesselt = atob(datei.substring(0,344));
		var AESKeyIv = atob(datei.substring(344,368));
		var signatur = atob(datei.substring(368,712));
		var dateiVerschluesselt = datei.substring(712);//Nach 712 Byte fängt die Datei an
		
		var verifizierunsSchluessel = holeVerifizierungsSchluessel()
		if(!verifizierunsSchluessel){
			if(!confirm("Die Herkunft der Datei konnte nicht verifiziert werden. Dennoch behalten?")) return;
		}
		else{
			RSAVerifizierunsSchluessel = forge.pki.publicKeyFromPem(verifizierunsSchluessel);
			var hasher = forge.md.sha256.create();
			hasher.update(dateiVerschluesselt);
			var verifiziert = RSAVerifizierunsSchluessel.verify(hasher.digest().bytes(), signatur);
			if(!verifiziert){
				if(!confirm("Die Herkunft der Datei konnte nicht verifiziert werden. Dennoch behalten?")) return;
			}
		}
		
		var AESKeyUnverschluesselt = dateischluesselUnverschluesselt.decrypt(AESKeyVerschluesselt);
		var entschluesseler = forge.aes.createDecryptionCipher(AESKeyUnverschluesselt, 'CBC');
		entschluesseler.start(AESKeyIv);
		entschluesseler.update(forge.util.createBuffer(atob(dateiVerschluesselt)));
		entschluesseler.finish();
		
		var byteString = entschluesseler.output.data;
		var byteArray = new Uint8Array(byteString.length);
		for (var i = 0; i<byteString.length;i++){
			byteArray[i]=byteString.charCodeAt(i);
		}
		
		var outputBlob = new Blob([byteArray]);
		saveAs(outputBlob,ausgewaehlt.Name);
	});
}

function bereiteDateischluesselVor(passwort){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"holePrivaterDateischluessel",versionID:aktuellerSchluessel.VersionID,CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			aktuellerSchluessel.privatePemVerschluesselt = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(0,2284));
			aktuellerSchluessel.salt = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(2284,2628));
			aktuellerSchluessel.AESKeyIv = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(2628,2652));
		}
	});
	
	try{
		var salt = aktuellerSchluessel.salt;
		var AESKey = forge.pkcs5.pbkdf2(passwort,salt, 8, 32);
		var AESKeyIv=aktuellerSchluessel.AESKeyIv;
		
		var entschluesseler = forge.aes.createDecryptionCipher(AESKey, 'CBC');
		entschluesseler.start(AESKeyIv);
		entschluesseler.update(forge.util.createBuffer(aktuellerSchluessel.privatePemVerschluesselt));
		entschluesseler.finish();
		if(entschluesseler.output.data.substring(0,5)!=="-----"){
			return false;
		}
		dateischluesselUnverschluesselt = forge.pki.privateKeyFromPem(entschluesseler.output.data);
	}
	catch(e){
		return false;
	}
	return true;
}

function holeVerifizierungsSchluessel(){
	var schluessel=false;
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		async:false,
		data: {aktion:"holeSignaturschluessel",nutzerID:ausgewaehlt.nutzerID,CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			if (!antwortObjekt.schluessel) return;
			schluessel = antwortObjekt.schluessel.Schluessel;
		}
	});
	return schluessel;
}