var signaturschluesselVorhanden = false;
var signaturschluessel;
var signaturschluesselUnverschluesselt;

var pfadZuOrdnerFileshare = "../../";

updateSignaturschluessel();
function updateSignaturschluessel(){//Fast gleich wie Funktion in schluessel.js... Funktionen müssen vereinfacht werden/zusammengelegt werden
	signaturschluessel = JSON.parse(localStorage.signaturschluessel || "{}");
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"aktuellerSignaturschluessel",CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			$("#aktuellerSignaturschluessel").text("Kein Signaturschlüssel gefunden, bitte in der Schlüsselverwaltung generieren!").css("color","red");
			if (signaturschluessel.versionID === antwortObjekt.versionID){
				$("#aktuellerSignaturschluessel").text(signaturschluessel.versionID).css("color","green");
				signaturschluesselVorhanden=true;
			}
		}
	});
}
$("#hochladen").click(function(){
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
		signaturschluesselUnverschluesselt = forge.pki.privateKeyFromPem(entschluesseler.output.data);
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
	var reader = new FileReader();
	reader.onload = (function(datei) {
		return function(dateiInhalt) {
			var byteString = "";//Eigene Implementierung von FileReader.readAsBinaryString weil deprecated
			var byteArray = new Uint8Array(dateiInhalt.target.result);
			for (var i = 0; i<byteArray.length;i++){
				byteString += String.fromCharCode(byteArray[i]);
			}
			
			var AESKeyZufall = forge.random.getBytesSync(32);//zufälliger 256Bit-Schlüssel
			verschluesseler = forge.aes.createEncryptionCipher(AESKeyZufall, 'CBC');
			AESKeyIv=forge.random.getBytesSync(16);
			
			verschluesseler.start(AESKeyIv);
			verschluesseler.update(forge.util.createBuffer(byteString));
			verschluesseler.finish();
			
			
			AESKeyVerschluesselt=btoa(fremderSchluessel.encrypt(AESKeyZufall));//AESKey wird asymmetrisch verschluesselt und base64 kodiert (344-Zeichen)
			AESKeyZufall = null;
			dateiVerschluesselt = btoa(verschluesseler.output.data); //Die AES-verschluesselte/base64 kodierte Datei
			verschluesseler = null;
			
			var hasher = forge.md.sha256.create();
			hasher.update(dateiVerschluesselt);
			var signatur = signaturschluesselUnverschluesselt.sign(hasher);//256-Bit Signatur
			var AESKeyIv64 = btoa(AESKeyIv);
			var signatur64 = btoa(signatur);//344-Zeichen langer base64 String der Signatur
			/*Da der (verschlüsselte )Schlüssel und die Signatur jeweils 256-Bit lang sind
			benötigen sie 344-Zeichen als base64-String - Diese können den eigentlichen Daten wegen ihrer
			fixen Größe vorausgestellt mit in der Datei gespeichert werden. AESKeyIv64 ist 24-Zeichen lang
			*/
			dateiVerschluesselt=AESKeyVerschluesselt+AESKeyIv64+signatur64+dateiVerschluesselt;
			hochladen(dateiVerschluesselt,datei.name,infoObject);
			console.log(datei);
		};
	})(datei);
	reader.readAsArrayBuffer(datei);//Es kann hier zunächst nur eine Datei eingelesen werden
}

function hochladen(dateiVerschluesselt,dateiname,infoObject){
	$.ajax({
		type: "POST",
		url: "dateiAjax.php",
		data: {aktion:"ladeHoch",datei:dateiVerschluesselt,dateiname:dateiname,nutzerID:infoObject.ID,versionID:infoObject.VersionID,zugriff:infoObject.ID,CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
		}
	});
}