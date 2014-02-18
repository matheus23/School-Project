var signaturschluesselVorhanden = false;
var signaturschluessel={};
var signaturschluesselUnverschluesselt;

var verschluesselnAnzeige;
var uploadAnzeige;

var pfadZuOrdnerFileshare = "../../";
var kb = 1024;
var mb = 1024*kb;
var serverPostLimit=20*mb;//Limit des Post-Requests

updateSignaturschluessel();
function updateSignaturschluessel(){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"aktuellerSignaturschluesselContainer",CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			if(antwortObjekt.schluesselContainer===null){
				$("#aktuellerSignaturschluessel").text("Kein Signaturschlüssel gefunden, bitte in der Schlüsselverwaltung generieren!").css("color","red");
			}
			else{
				signaturschluessel.privatePemVerschluesselt = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(0,2284));
				signaturschluessel.salt = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(2284,2628));
				signaturschluessel.AESKeyIv = atob(antwortObjekt.schluesselContainer.privaterSchluessel.slice(2628,2652));
				signaturschluessel.VersionID = antwortObjekt.schluesselContainer.VersionID;
				$("#aktuellerSignaturschluessel").text(signaturschluessel.VersionID).css("color","green");
				signaturschluesselVorhanden=true;
			}
		}
	});
}
$("#hochladen").click(function(){
	updateSignaturschluessel();
	var passwort = $("#signaturschluesselPasswort").val();
	var email = $("#emailEmpfaenger").val();
	
	if (passwort.length==0){
		fehlerNachricht("#fehlerListe", "fehler", "Du musst ein Passwort angeben.", pfadZuOrdnerFileshare);
		return;
	}
	if ($("#file")[0].files.length==0){
		fehlerNachricht("#fehlerListe", "fehler", "Du musst eine Datei gewählt haben.", pfadZuOrdnerFileshare);
		return;
	}
	if (email.length==0){
		fehlerNachricht("#fehlerListe", "fehler", "Du musst einen Empfänger angeben.", pfadZuOrdnerFileshare);
		return;
	}
	if ($("#file")[0].files[0].size>serverPostLimit){
		fehlerNachricht("#fehlerListe", "fehler", "Die Datei ist zu groß.", pfadZuOrdnerFileshare);
		return;
	}
	
	if(!signaturschluesselVorhanden){
		fehlerNachricht("#fehlerListe", "fehler", "Kein Signaturschlüssel gefunden.", pfadZuOrdnerFileshare);
		return;
	}
	if(!bereiteSignierungVor(passwort)){
		fehlerNachricht("#fehlerListe", "fehler", "Falsches Passwort oder beschädigter Schlüssel.", pfadZuOrdnerFileshare);
		return;
	}
	var infoObject = holeBenutzerInfo(email);
	if (!infoObject){
		fehlerNachricht("#fehlerListe", "fehler", 
			"Es gab ein Problem beim Abholen der Empfängerinfos - Hat der Empfänger schon einen Dateischlüssel generiert?", 
			pfadZuOrdnerFileshare);
		return;
	}
	$("#uploadForm").hide();
	$("#vorbereitenAnzeige").show();
	leseUndVerschluesseleDatei(infoObject);
});

//Die Funktion bringt den verschlüsselten RSA-Schlüssel für die Signierung in seine Normalform
function bereiteSignierungVor(passwort){
	//AES-Schluessel wird gelesen
	try{
		var salt = signaturschluessel.salt;
		var AESKey = forge.pkcs5.pbkdf2(passwort,salt, 8, 32);
		var AESKeyIv=signaturschluessel.AESKeyIv;
		
		var entschluesseler = forge.aes.createDecryptionCipher(AESKey, 'CBC');
		entschluesseler.start(AESKeyIv);
		entschluesseler.update(forge.util.createBuffer(signaturschluessel.privatePemVerschluesselt));
		entschluesseler.finish();
		if(entschluesseler.output.data.substring(0,5)!=="-----"){
			return false;
		}
		signaturschluesselUnverschluesselt = entschluesseler.output.data;
	}
	catch(e){
		return false;
	}
	return true;
}

function holeBenutzerInfo(email){
	var info=false;
	$.ajax({
		type: "POST",
		url: "dateiAjax.php",
		data: {aktion:"benutzerInformation",email:email,CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			if(!!antwortObjekt.info){
				info = antwortObjekt.info;
			}
		}
	});
	return info;
}

function leseUndVerschluesseleDatei(infoObject){
	var fremderSchluessel=forge.pki.publicKeyFromPem(infoObject.Schluessel);
	var datei = $("#file")[0].files[0];
	
	var AESKeyZufall = forge.random.getBytesSync(32);//zufälliger 256Bit-Schlüssel
	var AESKeyIv=forge.random.getBytesSync(16);
	var aesWorkerVerschluesseln = new Worker("../../js/aesWorkerVerschluesseln.js");
			
	var reader = new FileReader();
	reader.onload = (function(datei) {
		return function(dateiInhalt) {
			$("#vorbereitenAnzeige").hide();
			$("#verschluesselnAnzeige").show();
			var dateiGroesse = datei.size;
			verschluesselnAnzeige.anzeige.setLimit(dateiGroesse);
			verschluesselnAnzeige.anzeige.setStatus(0);
			var position = 0;
			var blockGroesse = 32768;//128*256
			var dateiVerschluesselt="";//Die AES-verschluesselte/base64 kodierte Datei
			
			var byteString;//Eigene Implementierung von FileReader.readAsBinaryString weil deprecated
			var byteArray = new Uint8Array(dateiInhalt.target.result);
			aesWorkerVerschluesseln.addEventListener('message', function(event){
				console.log(event.data);
				switch(event.data.aktion){
					case "weiter":
						dateiVerschluesselt+=event.data.output;
						verschluesselnAnzeige.anzeige.setStatus(position);
						if (position+blockGroesse<dateiGroesse){
							byteString="";
							for (var i = position; i<position+blockGroesse;i++){
								byteString += String.fromCharCode(byteArray[i]);
							}
							position+=blockGroesse;
							aesWorkerVerschluesseln.postMessage({aktion:"update",byteString:byteString});
						}
						else{
							byteString="";
							for (var i = position; i<dateiGroesse;i++){
								byteString += String.fromCharCode(byteArray[i]);
							}
							aesWorkerVerschluesseln.postMessage({aktion:"finish",byteString:byteString});
						}
						break;
					case "fertig":
						verschluesselnAnzeige.anzeige.setStatus(dateiGroesse);
						dateiVerschluesselt+=event.data.output;
						dateiVerschluesselt=btoa(dateiVerschluesselt);
						var AESKeyVerschluesselt=btoa(fremderSchluessel.encrypt(AESKeyZufall));//AESKey wird asymmetrisch verschluesselt und base64 kodiert (344-Zeichen)
						AESKeyZufall = null;
						
						$("#verschluesselnAnzeige").hide();
						$("#signierenAnzeige").show();
						var signierenWorker = new Worker("../../js/signierenWorker.js");
						signierenWorker.addEventListener('message', function(event){
							var signatur = event.data;
							var AESKeyIv64 = btoa(AESKeyIv);
							var signatur64 = btoa(signatur);//344-Zeichen langer base64 String der Signatur
							/*Da der (verschlüsselte )Schlüssel und die Signatur jeweils 256-Bit lang sind
							benötigen sie 344-Zeichen als base64-String - Diese können den eigentlichen Daten wegen ihrer
							fixen Größe vorausgestellt mit in der Datei gespeichert werden. AESKeyIv64 ist 24-Zeichen lang
							*/
							dateiVerschluesselt=AESKeyVerschluesselt+AESKeyIv64+signatur64+dateiVerschluesselt;
							hochladen(dateiVerschluesselt,datei.name,infoObject);
						});
						signierenWorker.postMessage({dateiVerschluesselt:dateiVerschluesselt,signaturschluesselUnverschluesselt:signaturschluesselUnverschluesselt});
						break;
				}
			});
			aesWorkerVerschluesseln.postMessage({aktion:"start",AESKeyZufall:AESKeyZufall,AESKeyIv:AESKeyIv});
			console.log(datei);
		};
	})(datei);
	reader.readAsArrayBuffer(datei);//Es kann hier zunächst nur eine Datei eingelesen werden
}

function hochladen(dateiVerschluesselt,dateiname,infoObject){
	$("#signierenAnzeige").hide();
	$("#uploadAnzeige").show();
	console.time("lol");
	$.ajax({
		xhr: function(){
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(event){
				if (event.lengthComputable) {
					uploadAnzeige.anzeige.setLimit(event.total);
					uploadAnzeige.anzeige.setStatus(event.loaded);
				}
			}, false);
			return xhr;
		},
		type: "POST",
		url: "dateiAjax.php",
		data: {aktion:"ladeHoch",datei:dateiVerschluesselt,dateiname:dateiname,nutzerID:infoObject.ID,versionID:infoObject.VersionID,zugriff:infoObject.ID,CSRFToken:CSRFToken},
		async:true,
		success: function(antwort){
			console.timeEnd("lol");
			var antwortObjekt = JSON.parse(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			$("#uploadAnzeige").hide();
			$("#uploadForm").show();
		}
	});
}

//UI-Funktionen
$(document).ready(function() {
	$(".anzeigeUpload").hide();
	verschluesselnAnzeige = $("#verschluesselnAnzeige").fortschrittBox(
		{
			label: "Datei wird verschlüsselt...",
			typ: "fortschritt"
		}
	);
	$("#signierenAnzeige").fortschrittBox(
		{
			label: "Datei wird signiert...",
			typ: "ewigerKreis"
		}
	);
	uploadAnzeige = $("#uploadAnzeige").fortschrittBox(
		{
			label: "Datei wird hochgeladen...",
			typ: "fortschritt"
		}
	);
});