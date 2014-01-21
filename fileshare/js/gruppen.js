// Sorry für das jQuery - ist hier aber sehr angenehm zu schreiben :)
//Sollte mit etwas css gut verständlich sein
var neueGruppe = false;

$("#neuesmitglied").click(function(){
	$("#auswahlliste").html("");
	$("#hinzufuegenAuswahl").hide();
	$("#fehlerListe").html("");
	var nameemail = $("#nameemail").val();
	if(nameemail.length===0){
		return;
	}
	$.ajax({//Frage an den Server, ob Name/Email existiert bzw. mehrere Treffer
		type: "POST",//Schicke mit POST
		url: "gruppeAjax.php",//Anfrage an gruppeAjax.php
		data: {nameemail:nameemail,aktion:"schickeNutzerEmail"},//Sende nutzername/Email mit
		success: function(antwort){//Weiter gehts mit der Antwort
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwortObjekt);
			eval(antwortObjekt.nrt);//Führe Fehlerlistenskript aus (eval ist hier nicht unsicherer als php generiertes js)
			$("#nameemail").val("");
			if(antwortObjekt.nutzer.length===1){//Wenn nur ein Nutzer gefunden wurde:
				mitgliedZurListe(antwortObjekt.nutzer[0]);
			}
			if(antwortObjekt.nutzer.length>1){//Wenn mehrere Nutzer gefunden wurden:
				$("#hinzufuegenAuswahl").show();
				mitgliederZurAuswahlListe(antwortObjekt.nutzer);
			}
		}
	});
});

function mitgliedZurListe(nutzer){
	if(mitgliedSchonInListe(nutzer)){
		fehlerNachricht($("#fehlerListe")[0],"Der Nutzer ist schon in der Liste.","warnung","../../");
		return;
	}
	var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
	var label = $("<span>").addClass("listenlabel").text(nutzer.Nutzername + " - " + nutzer.Email);//Nutzername/Email auf Listenlabel
	var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
	muell.click(function(event){
		$(this).parent().remove();//Löscht Listenelement bei Klick auf das Müllsymbol
	})
	listenelement.append(label);//Label wird auf Listenelement gepackt
	listenelement.append(muell);//Muellicon wird auf Listenelement gepackt
	listenelement.data("email",nutzer.Email);//belege Element mit Daten: Email (JS->HTML-Element)
	$("#mitgliederliste").append(listenelement);//Listenelement wird zur Liste hinzugefügt
}

function mitgliederZurAuswahlListe(nutzer){
	$.each(nutzer,function(index,nutzer){//Fügt alle Mitglieder der Auswahl-Liste hinzu
		var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
		var label = $("<span>").addClass("listenlabel").text(nutzer.Nutzername + " - " + nutzer.Email);//Nutzername/Email auf Listenlabel
		listenelement.append(label);//Label wird auf Listenelement gepackt
		listenelement.data("email",nutzer.Email);//belege Element mit Daten: Email (JS->HTML-Element)
		listenelement.click(function(event){//Bei Auswahl klont sich das Listenelement in die Mitgliederliste, trennt den click-Listener und löscht die Auswahl
			mitgliedZurListe(nutzer);
			$("#auswahlliste").html("");
			$("#hinzufuegenAuswahl").hide();
		});
		$("#auswahlliste").append(listenelement);//Listenelement wird zur Liste hinzugefügt
	});
}

function mitgliedSchonInListe(nutzer){//Prüft, ob Nutzer schon in Mitgliederliste steht
	var existiertSchon = false;
	$("#mitgliederliste > .listenelement").each(function(index,listenelement){
		if($(listenelement).data("email")===nutzer.Email){
			existiertSchon = true;
			return false;
		}
	});
	return existiertSchon;
}

$("#neuegruppe").click(function(){
	neueGruppe = true;
	$("#gruppenname").val("");
	$("#nameemail").val("");
	$("#mitgliederliste > .listenelement").remove()
});

$("#editFertig").click(function(){
	$("#fehlerListe").html("");	
	var gruppenname = $("#gruppenname").val();
	if(gruppenname.length===0){
		fehlerNachricht($("#fehlerListe")[0],"Gruppenname ausfüllen.","fehler","../../");
		return;
	}
	var emails = [];
	$("#mitgliederliste > .listenelement").each(function(index,listenelement){
		emails.push($(listenelement).data("email"));
	});
	if(emails.length===0){
		fehlerNachricht($("#fehlerListe")[0],"Die Gruppe muss Mitglieder enthalten.","fehler","../../");
		return;
	}
	$.ajax({//Frage an den Server, ob Name/Email existiert bzw. mehrere Treffer
		type: "POST",//Schicke mit POST
		url: "gruppeAjax.php",//Anfrage an gruppeAjax.php
		data: {emails:JSON.stringify(emails),aktion:"fertigGruppe",neueGruppe:neueGruppe,gruppenname:gruppenname},//Sende nutzername/Email mit
		success: function(antwort){//Weiter gehts mit der Antwort
			console.log(antwort);
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwortObjekt);
			eval(antwortObjekt.nrt);
			if(antwortObjekt.erfolg){
				neueGruppe = false;
			}
		}
	});
});