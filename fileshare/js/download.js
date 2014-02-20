$("#editor > .fenster").hide(0);
var dateiliste;
var dateischluessel;
var schluesselVorhanden = false;
var aktuellerSchluessel;
var ausgewaehltID;
var ausgewaehlt;
var dateischluesselUnverschluesselt;

var downloadAnzeige;
var entschluesselnAnzeige;

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
	$("#panel").scrollTop(0);
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
	holeDatei(dateiURL,passwort);
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

function holeDatei(dateiURL,passwort){
	if(!bereiteDateischluesselVor(passwort)) {
		fehlerNachricht("#fehlerListe", "fehler", "Falsches Passwort oder beschädigter Schlüssel", pfadZuOrdnerFileshare);
		return;
	}
	$("#editorcontent").hide();
	$("#downloadAnzeige").show();
	$.ajax({
		xhr: function(){
			var xhr = new window.XMLHttpRequest();
			xhr.addEventListener("progress", function(event){
				if (event.lengthComputable) {
					downloadAnzeige.anzeige.setLimit(event.total);
					downloadAnzeige.anzeige.setStatus(event.loaded);
				}
			}, false);
			return xhr;
		},
		type: "GET",
		url:dateiURL,
		data: {},
		async:true,
		success: function(antwort){
			$("#downloadAnzeige").hide();
			bereiteDateiVor(antwort,passwort);
		}
	});
}

function bereiteDateiVor(datei,passwort){//Übernimmt auch die Signaturprüfung
	
	$("#signaturpruefenAnzeige").show();
	//Liest die notwendigen Informationen wieder ein, die an festen Stellen in der Datei gespeichert sind
	var AESKeyVerschluesselt = atob(datei.substring(0,344));
	var AESKeyIv = atob(datei.substring(344,368));
	var signatur = atob(datei.substring(368,712));
	var dateiVerschluesselt64 = datei.substring(712);//Nach 712 Byte fängt die Datei an
	
	var verifizierungsSchluessel = holeVerifizierungsSchluessel()
	if(!verifizierungsSchluessel){
		if(!confirm("Die Herkunft der Datei konnte nicht verifiziert werden. Dennoch behalten?")){
			$("#signaturpruefenAnzeige").hide();
			$("#editorcontent").show();
			return;
		}
	}
	else{
		var signaturpruefenWorker = new Worker("../../js/signaturpruefenWorker.js");
		signaturpruefenWorker.addEventListener('message', function(event){
			if (event.data){
				$("#signaturpruefenAnzeige").hide();
				entschluesseleDatei(AESKeyVerschluesselt,AESKeyIv,dateiVerschluesselt64,passwort);
			}
			else{
				if(confirm("Die Herkunft der Datei konnte nicht verifiziert werden. Dennoch behalten?")){
					$("#signaturpruefenAnzeige").hide();
					entschluesseleDatei(AESKeyVerschluesselt,AESKeyIv,dateiVerschluesselt64,passwort);
				}
				else{
					$("#signaturpruefenAnzeige").hide();
					$("#editorcontent").show();
				}
			}
		});
		signaturpruefenWorker.postMessage({verifizierungsSchluessel:verifizierungsSchluessel,dateiVerschluesselt64:dateiVerschluesselt64,signatur:signatur});
	}
	
	
}
function entschluesseleDatei(AESKeyVerschluesselt,AESKeyIv,dateiVerschluesselt64,passwort){
	$("#entschluesselnAnzeige").show();
	var AESKeyUnverschluesselt = dateischluesselUnverschluesselt.decrypt(AESKeyVerschluesselt);
	var aesWorkerEntschluesseln = new Worker("../../js/aesWorkerEntschluesseln.js");
	
	var dateiGroesse = dateiVerschluesselt64.length;
	entschluesselnAnzeige.anzeige.setLimit(dateiGroesse);
	entschluesselnAnzeige.anzeige.setStatus(0);
	var position = 0;
	var blockGroesse = 32768;//128*256
	var dateiEntschluesselt="";
	dateiVerschluesselt = atob(dateiVerschluesselt64);
	
	aesWorkerEntschluesseln.addEventListener('message', function(event){
			console.log(event.data);
			switch(event.data.aktion){
				case "weiter":
					dateiEntschluesselt+=event.data.output;
					entschluesselnAnzeige.anzeige.setStatus(position);
					if (position+blockGroesse<dateiGroesse){
						byteString="";
						for (var i = position; i<position+blockGroesse;i++){
							byteString += dateiVerschluesselt[i];
						}
						position+=blockGroesse;
						aesWorkerEntschluesseln.postMessage({aktion:"update",byteString:byteString});
					}
					else{
						byteString="";
						for (var i = position; i<dateiGroesse;i++){
							byteString += dateiVerschluesselt[i];
						}
						aesWorkerEntschluesseln.postMessage({aktion:"finish",byteString:byteString});
					}
					break;
				case "fertig":
					entschluesselnAnzeige.anzeige.setStatus(dateiGroesse);
					dateiEntschluesselt+=event.data.output;
					var byteArray = new Uint8Array(dateiEntschluesselt.length);
					for (var i = 0; i<dateiEntschluesselt.length;i++){
						byteArray[i]=dateiEntschluesselt.charCodeAt(i);
					}
					var outputBlob = new Blob([byteArray]);
					saveAs(outputBlob,ausgewaehlt.Name);
					$("#entschluesselnAnzeige").hide();
					$("#editorcontent").show();
				break;
			}
	});
	aesWorkerEntschluesseln.postMessage({aktion:"start",AESKeyUnverschluesselt:AESKeyUnverschluesselt,AESKeyIv:AESKeyIv});
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

//UI-Funktionen
$(document).ready(function() {
	$(".anzeigeUpload").hide();
	downloadAnzeige = $("#downloadAnzeige").fortschrittBox(
		{
			label: "Datei wird heruntergeladen...",
			typ: "fortschritt"
		}
	);
	entschluesselnAnzeige = $("#entschluesselnAnzeige").fortschrittBox(
		{
			label: "Datei wird entschlüsselt...",
			typ: "fortschritt"
		}
	);
	$("#signaturpruefenAnzeige").fortschrittBox(
		{
			label: "Signatur wird überprüft...",
			typ: "ewigerKreis"
		}
	);
});