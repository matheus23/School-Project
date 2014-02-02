var dateischluessel;
var signaturschluessel;
updateDateischluesselListe();
updateSignaturschluessel();

//Dateischlüssel
$("#editor > .fenster").hide(0);
$("#dateischluesselButton").click(function(){
	$("#editor > .label").text($(this).text());
	$("#editor > .fenster").hide(0);
	$("#editor").show(0);
	$("#dateischluessel").show(0);
});
$("#dateischluesselGenerieren").click(function(){
	var passwort = $("#dateischluesselPasswort").val();
	if (passwort.length==0){
		$("#fehlerListe").show();
		fehlerNachricht($("#fehlerListe")[0],"Du musst ein Passwort angeben","warnung","../../");
		$("#fehlerListe").delay(5000).hide(500).queue(function(){
			$(this).html("");
			$(this).dequeue();
		});
		return;
	}
	generiereSchluessel(passwort);
});

function fehlerBehandlung(antwort){
	if (antwort==="interner Fehler"){
		alert("Etwas stimmt mit deiner Authentifizierung nicht, bitte melde dich erneut an.");
		window.location.href = "../Anmeldung.php";
	}
	return false;
}

function generiereSchluessel(passwort){
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
		dateischluessel.push({"versionID":versionID,"privatePemVerschluesselt":privatePemVerschluesselt,"salt":salt,"AESKeyIv":AESKeyIv});
		localStorage.dateischluessel = JSON.stringify(dateischluessel);//speichert Schlüssel lokal
		schickeSchluessel(publicPem,versionID);
	});
}

function schickeSchluessel(publicPem,versionID){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"neuerDateischluessel",versionID:versionID,schluessel:publicPem,CSRFToken:CSRFToken},
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			$("#fehlerListe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
				$(this).html("");
				$(this).dequeue();
			});
		}
	});
	updateDateischluesselListe();
}

function updateDateischluesselListe(){
	$("#dateischluesselListe > .listenelement").remove();
	dateischluessel = JSON.parse(localStorage.dateischluessel || "[]");
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"aktuellerDateischluessel",CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			$("#fehlerListe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
				$(this).html("");
				$(this).dequeue();
			});
			$("#aktuellerDateischluessel").text("Kein Schlüssel gefunden, bitte generieren!").css("color","red");
			$.each(dateischluessel,function(index,schluessel){
				if (schluessel.versionID === antwortObjekt.versionID){
					$("#aktuellerDateischluessel").text(schluessel.versionID).css("color","green");
				}
				else{
					var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
					var label = $("<span>").addClass("listenlabel").text(schluessel.versionID);
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
	dateischluessel = $.grep(dateischluessel,function(schluessel,index){
		if (schluessel.versionID == versionID) return false;
		return true;
	});
	localStorage.dateischluessel = JSON.stringify(dateischluessel);
	updateDateischluesselListe();
}

//Signaturschlüssel
$("#signaturschluesselButton").click(function(){
	$("#editor > .label").text($(this).text());
	$("#editor > .fenster").hide(0);
	$("#editor").show(0);
	$("#signaturschluessel").show(0);
});
$("#signaturschluesselGenerieren").click(function(){
	var passwort = $("#signaturschluesselPasswort").val();
	if (passwort.length==0){
		$("#fehlerListe").show();
		fehlerNachricht($("#fehlerListe")[0],"Du musst ein Passwort angeben","warnung","../../");
		$("#fehlerListe").delay(5000).hide(500).queue(function(){
			$(this).html("");
			$(this).dequeue();
		});
		return;
	}
	generiereSignaturschluessel(passwort);
});

function generiereSignaturschluessel(passwort){
	//AES-Schluessel wird generiert
	debugger;
	var salt = forge.random.getBytesSync(256);//Wird für die Schlüsselerzeugung benötigt und kann mit dem Verschlüsseltem gespeichert werden
	var AESKey = forge.pkcs5.pbkdf2(passwort,salt, 8, 32);
	var AESKeyIv=forge.random.getBytesSync(16);//Wird für Verschlüsselung benötigt und kann mit dem Verschlüsseltem gespeichert werden
	//String, der eindeutig pro Nutzer sein soll
	var versionID = (""+new Date().valueOf()+Math.random()).replace("0.","");
	
	//Folgendes läuft ab wie oben, nur dass der private Schlüssel verschickt wird und der öffentliche verschlüsselt lokal bleibt.
	var rsa = forge.pki.rsa;
	var RSAPrivateKey;
	var privatePem;
	var RSAPublicKey;
	var publicPem;
	var publicPemVerschluesselt;
	
	//RSA-Schlüsselpaar wird generiert
	rsa.generateKeyPair({bits: 2048, workers: 2, workerScript:"../../js/forge/prime.worker.js"}, function(err, keypair) {
		RSAPrivateKey = keypair.privateKey;
		RSAPublicKey = keypair.publicKey;
		publicPem = forge.pki.publicKeyToPem(RSAPublicKey);
		privatePem = forge.pki.privateKeyToPem(RSAPrivateKey);
		verschluesseler = forge.aes.createEncryptionCipher(AESKey, 'CBC');
		verschluesseler.start(AESKeyIv);
		verschluesseler.update(forge.util.createBuffer(privatePem));//Verschlüsselt den öffentlichen RSA-Schlüssel
		verschluesseler.finish();
		privatePemVerschluesselt = verschluesseler.output.data;
		signaturschluessel = {"versionID":versionID,"privatePemVerschluesselt":privatePemVerschluesselt,"salt":salt,"AESKeyIv":AESKeyIv};
		localStorage.signaturschluessel = JSON.stringify(signaturschluessel);//speichert Schlüssel lokal
		schickeSignaturschluessel(publicPem,versionID);
	});
}

function schickeSignaturschluessel(publicPem,versionID){
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"neuerSignaturschluessel",versionID:versionID,schluessel:publicPem,CSRFToken:CSRFToken},
		success: function(antwort){
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			$("#fehlerListe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
				$(this).html("");
				$(this).dequeue();
			});
		}
	});
	updateSignaturschluessel();
}

function updateSignaturschluessel(){
	signaturschluessel = JSON.parse(localStorage.signaturschluessel || "{}");
	$.ajax({
		type: "POST",
		url: "schluesselAjax.php",
		data: {aktion:"aktuellerSignaturschluessel",CSRFToken:CSRFToken},
		success: function(antwort){
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwort);
			$("#fehlerListe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
				$(this).html("");
				$(this).dequeue();
			});
			$("#aktuellerSignaturschluessel").text("Kein Schlüssel gefunden, bitte generieren!").css("color","red");
			if (signaturschluessel.versionID === antwortObjekt.versionID){
				$("#aktuellerSignaturschluessel").text(signaturschluessel.versionID).css("color","green");
			}
		}
	});
}
