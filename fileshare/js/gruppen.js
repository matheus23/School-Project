// Sorry für das jQuery - ist hier aber sehr angenehm zu schreiben :)
//Sollte mit etwas css gut verständlich sein

var gruppeEditiertID;

$("#neuesmitglied").click(function(){
	$("#auswahlliste").html("");
	$("#hinzufuegenAuswahl").hide();
	var nameemail = $("#nameemail").val();
	if(nameemail.length===0){
		return;
	}
	$.ajax({//Frage an den Server, ob Name/Email existiert bzw. mehrere Treffer
		type: "POST",//Schicke mit POST
		url: "gruppeAjax.php",//Anfrage an gruppeAjax.php
		data: {nameemail:nameemail,aktion:"schickeNutzerEmail",CSRFToken:CSRFToken},//Sende nutzername/Email mit
		success: function(antwort){//Weiter gehts mit der Antwort
			if (fehlerBehandlung(antwort)) return;
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwortObjekt);
			$("#fehlerListe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
					$(this).html("");
					$(this).dequeue();
				});
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
		$("#fehlerListe").show();
		fehlerNachricht($("#fehlerListe")[0],"Der Nutzer ist schon in der Liste.","warnung","../../");
		$("#fehlerListe").delay(5000).hide(500).queue(function(){
			$(this).html("");
			$(this).dequeue();
		});
		return;
	}
	var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
	var label = $("<span>").addClass("listenlabel").text(nutzer.Nutzername + " - " + nutzer.Email);//Nutzername/Email auf Listenlabel
	var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
	muell.click(function(event){
		$(this).parent().remove();//Löscht Listenelement bei Klick auf das Müllsymbol
	});
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
	$('#editor').hide();
	$("#gruppenname").val("");
	$("#nameemail").val("");
	$("#mitgliederliste > .listenelement").remove();
	hinzuguegenGruppe();
});

$("#editFertig").click(function(){
	var gruppenname = $("#gruppenname").val();
	if(gruppenname.length===0){
		$("#fehlerListe").show();
		fehlerNachricht($("#fehlerListe")[0],"Gruppenname ausfüllen.","fehler","../../");
		$("#fehlerListe").delay(5000).hide(500).queue(function(){
				$(this).html("");
				$(this).dequeue();
			});
		return;
	}
	var emails = [];
	$("#mitgliederliste > .listenelement").each(function(index,listenelement){
		emails.push($(listenelement).data("email"));
	});
	if(emails.length===0){
		$("#fehlerListe").show();
		fehlerNachricht($("#fehlerListe")[0],"Die Gruppe muss Mitglieder enthalten.","fehler","../../");
		$("#fehlerListe").delay(5000).hide(500).queue(function(){
			$(this).html("");
			$(this).dequeue();
		});
		return;
	}
	$.ajax({
		type: "POST",
		url: "gruppeAjax.php",
		data: {emails:JSON.stringify(emails),aktion:"fertigGruppe",gruppenname:gruppenname,GruppenID:gruppeEditiertID,CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			console.log(antwort);
			if (fehlerBehandlung(antwort)) return;
			var antwortObjekt = JSON.parse(antwort);
			eval(antwortObjekt.nrt);
			console.log(antwortObjekt);
			$("#fehlerListe").show();
			$("#fehlerListe").delay(5000).hide(500).queue(function(){
					$(this).html("");
					$(this).dequeue();
				});
			aktualisiereGruppen();
		}
	});
});

/*
$("#neuegruppe").click(function(){
	hinzuguegenGruppe();
});
*/

function hinzuguegenGruppe(){
	$("#neuegruppe").unbind("click");//Entfernt das Klick-Event
	var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
	var nameFeld = $("<input>").attr("type","text");//Input für Gruppennamen wird erstellt
	listenelement.append(nameFeld);//Input wird auf Listenelement gelegt
	var akzeptieren = $("<div>").addClass("akzeptieren").addClass("rightfloat");
	akzeptieren.click(function(event){
		event.stopPropagation();//Verhindert das Klick-Event des dahinterliegenden Listenelements
		$(this).parent().remove();
		$("#neuegruppe").click(function(){
			hinzuguegenGruppe();
		});
		var gruppenname = $(this).parent().children("input").val();
		$.ajax({//Frage an den Server, ob Name/Email existiert bzw. mehrere Treffer
			type: "POST",//Schicke mit POST
			url: "gruppeAjax.php",//Anfrage an gruppeAjax.php
			data: {aktion:"neueGruppe",gruppenname:gruppenname,CSRFToken:CSRFToken},//Sende nutzername/Email mit
			async:false,
			success: function(antwort){//Weiter gehts mit der Antwort
				console.log(antwort);
				if (fehlerBehandlung(antwort)) return;
				var antwortObjekt = JSON.parse(antwort);
				$("#fehlerListeGruppe").show();
				eval(antwortObjekt.nrt);
				$("#fehlerListeGruppe").delay(5000).hide(500).queue(function(){
					$(this).html("");
					$(this).dequeue();
				});
			}
		});
		aktualisiereGruppen();
	});
	listenelement.append(akzeptieren);
	$("#gruppenliste").append(listenelement);
}
function behandleKlickGruppe(element){
	$("#editor").show();
	$("#gruppenliste > .listenelement").removeClass("gruppeEditiert");
	$(element).addClass("gruppeEditiert");
	gruppeEditiertID = $(element).data("id");
	$("#gruppenname").val($(element).children(".listenlabel").text());
	$.ajax({
		type: "POST",
		url: "gruppeAjax.php",
		data: {aktion:"schickeMitglieder",GruppenID:gruppeEditiertID,CSRFToken:CSRFToken},
		success: function(antwort){
			if (fehlerBehandlung(antwort)) return;
			$("#mitgliederliste > .listenelement").remove();
			$("#mitgliederliste").append(antwort);
			$("#mitgliederliste > .listenelement").each(function(index,element){
				var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
				muell.click(function(event){
					$(element).remove();
				});
				$(element).append(muell);
			});
		}
	});
}
function loescheGruppe(zuLoeschen){
	if(!confirm("Willst du die Gruppe '"+$(zuLoeschen).parent().text()+"' wirklich löschen?")){
		return false;
	}
	var gruppenID = $(zuLoeschen).parent().data("id");
	if (gruppenID===gruppeEditiertID){
		gruppenEditorReset();
		$("#editor").hide(0);
	}
	$.ajax({
		type: "POST",
		url: "gruppeAjax.php",
		data: {aktion:"loescheGruppe",GruppenID:gruppenID,CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			console.log(antwort);
			if (fehlerBehandlung(antwort)) return;
			antwortObjekt = JSON.parse(antwort);
			$("#fehlerListeGruppe").show();
			eval(antwortObjekt.nrt);
			$("#fehlerListeGruppe").delay(5000).hide(500).queue(function(){
					$(this).html("");
					$(this).dequeue();
				});
		}
	});
	aktualisiereGruppen();
}
function aktualisiereGruppen(){
	$('#gruppenliste > .listenelement').remove();
	var gruppen;
	//Lade Gruppen vom Server
	$.ajax({
		type: "POST",
		url: "gruppeAjax.php",
		data: {aktion:"schickeGruppen",CSRFToken:CSRFToken},
		async:false,
		success: function(antwort){
			if (fehlerBehandlung(antwort)) return;
			gruppen = antwort;
		}
	});
	$('#gruppenliste').append(gruppen);
	$("#gruppenliste > .listenelement").each(function(index,element){
		var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
		muell.click(function(){
			loescheGruppe(this);
		});
		$(element).append(muell);
	});
	$("#gruppenliste > .listenelement").click(function(){
		behandleKlickGruppe(this);
	});
	
}
function gruppenEditorReset(){
	$("#auswahlliste").html("");
	$("#editor input[type=text]").val("");
}

function fehlerBehandlung(antwort){
	if (antwort==="interner Fehler"){
		alert("Etwas stimmt mit deiner Authentifizierung nicht, bitte melde dich erneut an.");
		window.location.href = "../Anmeldung.php";
	}
	return false;
}
aktualisiereGruppen();