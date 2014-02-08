var dateischluessel;
var signaturschluessel;
updateDateischluesselListe();
updateSignaturschluessel();

var pfadZuOrdnerFileshare = "../../";

var dateischluessel = {
	name: "Dateischluessel",
	
	fensterQuery: "#dateischluessel",
	fensterButtonQuery: "#dateischluesselButton",
	buttonGenerierenQuery: "#dateischluesselGenerieren",
	passwortInputQuery: "#dateischluesselPasswort",
	passwortWdhInputQuery: "#dateischluesselPasswortWdh",
	schluesselListeQuery: "#dateischluesselListe",
	aktuellerSchluesselQuery: "#aktuellerDateischluessel",
	
	neuAktionAjax: "neuerDateischluessel",
	
	callIf: function(ifDateischluessel, ifSignaturschluessel) {
		return ifDateischluessel();
	}
}

var signaturschluessel = {
	name: "Signaturschluessel",
	
	fensterQuery: "#signaturschluessel",
	fensterButtonQuery: "#signaturschluesselButton",
	buttonGenerierenQuery: "#signaturschluesselGenerieren",
	passwortInputQuery: "#signaturschluesselPasswort",
	passwortWdhInputQuery: "#signaturschluesselPasswortWdh",
	schluesselListeQuery: "#signaturschluesselListe",
	aktuellerSchluesselQuery: "#aktuellerSignaturschluessel",
	
	neuAktionAjax: "neuerSignaturschluessel",
	
	callIf: function(ifDateischluessel, ifSignaturschluessel) {
		return ifSignaturschluessel();
	}
}

registriereCallbacks(dateischluessel);
registriereCallbacks(signaturschluessel);

function registriereCallbacks(schluesselArt) {
	$(schluesselArt.fensterButtonQuery).click(function() {
		$("#editor > .label").text($(this).text());
		$("#editor > .fenster").hide(0);
		$("#editor").show(0);
		$(schluesselArt.fensterQuery).show(0);
	});
	$(schluesselArt.buttonGenerierenQuery).click(function(){
		var passwort = $(schluesselArt.passwortInputQuery).val();
		var passwortWdh = $(schluesselArt.passwortWdhInputQuery).val();
		if (passwort.length==0){
			fehlerNachricht("#fehlerListe", "warnung", "Du musst ein Passwort angeben.", pfadZuOrdnerFileshare);
			return;
		} else if (passwort !== passwortWdh) {
			fehlerNachricht("#fehlerListe", "fehler", "Die Passwörter stimmen nicht überein.", pfadZuOrdnerFileshare);
			return;
		}
		generiereSchluessel(schluesselArt, passwort);
	});
}

function generiereSchluessel(schluesselArt, passwort){
	//AES-Schluessel wird generiert
	var salt = forge.random.getBytesSync(256);//Wird für die Schlüsselerzeugung benötigt und kann mit dem Verschlüsseltem gespeichert werden
	var AESKey = forge.pkcs5.pbkdf2(passwort,salt, 8, 32);
	var AESKeyIv=forge.random.getBytesSync(16);//Wird für Verschlüsselung benötigt und kann mit dem Verschlüsseltem gespeichert werden
	//String, der eindeutig pro Nutzer sein soll
	var versionID = (""+new Date().valueOf()+Math.random()).replace("0.","");
	var rsa = forge.pki.rsa;
	var RSAPrivateKey;//privater Schlüssel, wird kodiert
	var privatePem;////pem (base64) kodierter privater Schlüssel, wird verschlüsselt
	var privatePemVerschluesselt;//AES-verschlüsselter RSA-Schlüssel, der lokal gespeichert wird.
	var privatePemVerschluesselt64;//AES-verschlüsselter RSA-Schlüssel, base64 kodiert
	var RSAPublicKey;//öffentlicher Schlüssel, wird kodiert
	var publicPem;//pem (base64) kodierter öffentlicher Schlüssel, wird zum Server geschickt
	
	//RSA-Schlüsselpaar wird generiert
	rsa.generateKeyPair({bits: 2048, workers: 2, workerScript:"../../js/forge/prime.worker.js"}, function(err, keypair) {
		RSAPrivateKey = keypair.privateKey;
		RSAPublicKey = keypair.publicKey;
		publicPem = forge.pki.publicKeyToPem(RSAPublicKey);
		privatePem = forge.pki.privateKeyToPem(RSAPrivateKey);
		verschluesseler = forge.aes.createEncryptionCipher(AESKey, 'CBC');
		verschluesseler.start(AESKeyIv);
		verschluesseler.update(forge.util.createBuffer(privatePem));//Verschlüsselt den privaten RSA-Schlüssel
		verschluesseler.finish();
		privatePemVerschluesselt = verschluesseler.output.data;
		privaterSchluesselContainer =
			btoa(privatePemVerschluesselt)+	//2284-Zeichen lang
			btoa(salt)+						//344-Zeichen lang
			btoa(AESKeyIv);					//24-Zeichen lang
		
		schickeSchluessel(schluesselArt, {
			publicPem: publicPem, 
			privaterSchluesselContainer: privaterSchluesselContainer, 
			versionID: versionID
		});
		schluesselArt.callIf(
			updateDateischluesselListe, 
			updateSignaturschluessel);
	});
}

function schickeSchluessel(schluesselArt, schluessel){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		async: false,
		data: {
			aktion: schluesselArt.neuAktionAjax,
			versionID: schluessel.versionID,
			schluessel: schluessel.publicPem,
			privaterSchluessel: schluessel.privaterSchluesselContainer,
			CSRFToken: CSRFToken
		},
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			console.log("schickeSchluessel (" + schluesselArt.name + "):");
			console.log(antwortObjekt);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
		}
	});
}

function updateDateischluesselListe() {
	$("#dateischluesselListe > .listenelement").remove();
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"schickeDateischluesselListe",CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			console.log("updateDateischluesselListe:");
			console.log(antwortObjekt);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			$("#aktuellerDateischluessel").text("Kein Schlüssel gefunden, bitte generieren!").css("color","red");
			$.each(antwortObjekt.schluesselArray,function(index,schluessel){
				if(schluessel.aktiv=="1"){
					$("#aktuellerDateischluessel").text(schluessel.VersionID).css("color","green");
				}
				else{
					var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
					var label = $("<span>").addClass("listenlabel").text(schluessel.VersionID);
					var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
					muell.click(function(event){
						loescheDateischluessel($(this).parent().text());
					});
					listenelement.append(label).append(muell);
					$("#dateischluesselListe").append(listenelement);
				}
			});
		}
	});
}

function loescheDateischluessel(versionID){
	if(!confirm("Lokalen Schlüssel "+versionID+" wirklich löschen?\n Das Entschlüsseln einiger Dateien kann unmöglich werden.")) return false;
	//$.grep geht durch ein Array und löscht ein Element, das false zurück gibt
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		async:false,
		data: {aktion: "loescheDateischluessel",versionID:versionID,CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
		}
	});
	updateDateischluesselListe();
}

function updateSignaturschluessel() {
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"aktuellerSignaturschluessel",CSRFToken:CSRFToken},
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			console.log("updateSignaturschluessel:");
			console.log(antwortObjekt);
			fehlerNachrichten("#fehlerListe", antwortObjekt.nrt);
			$("#aktuellerSignaturschluessel").text("Kein Schlüssel gefunden, bitte generieren!").css("color","red");
			if (antwortObjekt.versionID){
				$("#aktuellerSignaturschluessel").text(antwortObjekt.versionID).css("color","green");
			}
		}
	});
}