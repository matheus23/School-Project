// Sorry für das jQuery - ist hier aber sehr angenehm zu schreiben :)
//Sollte mit etwas css gut verständlich sein

$("#neuesmitglied").click(function(){
	$("#auswahlliste").html("");
	$("#hinzufuegenAuswahl").hide();
	//$("#fehlerListe").html("");
	var nameemail = $("#nameemail").val();
	if(nameemail.length===0){
		return;
	}
	$.ajax({//Frage an den Server, ob Name/Email existiert bzw. mehrere Treffer
		type: "POST",//Schicke mit POST
		url: "gruppeAjax.php",//Anfrage an gruppeAjax.php
		data: {nameemail:nameemail},//Sende nutzername/Email mit
		success: function(antwort){//Weiter gehts mit der Antwort
			var antwortObjekt = JSON.parse(antwort);
			console.log(antwortObjekt);
			eval(antwortObjekt.nrt);//Führe Fehlerlistenskript aus (eval ist hier nicht unsicherer als php generiertes js)
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
	var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
	var label = $("<span>").addClass("listenlabel").text(nutzer.Nutzername + " - " + nutzer.Email);//Nutzername/Email auf Listenlabel
	var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
	muell.click(function(event){
		$(this).parent().remove();//Löscht Listenelement bei Klick auf das Müllsymbol
	})
	listenelement.append(label);//Label wird auf Listenelement gepackt
	listenelement.append(muell);//Muellicon wird auf Listenelement gepackt
	listenelement.data("email",nutzer.Email)//belege Element mit Daten: Email (JS->HTML-Element)
	$("#mitgliederliste").append(listenelement);//Listenelement wird zur Liste hinzugefügt
}

function mitgliederZurAuswahlListe(nutzer){
	$(nutzer).each(function(index,nutzer){//Fügt alle Mitglieder der Auswahl-Liste hinzu
		var listenelement = $("<div>").addClass("listenelement"); //Neues Listenelement
		var label = $("<span>").addClass("listenlabel").text(nutzer.Nutzername + " - " + nutzer.Email);//Nutzername/Email auf Listenlabel
		listenelement.append(label);//Label wird auf Listenelement gepackt
		listenelement.data("email",nutzer.Email)//belege Element mit Daten: Email (JS->HTML-Element)
		listenelement.click(function(event){//Bei Auswahl klont sich das Listenelement in die Mitgliederliste, trennt den click-Listener und löscht die Auswahl
			var muell = $("<div>").addClass("loeschen").addClass("rightfloat");
			muell.click(function(event){
				$(this).parent().remove();//Löscht Listenelement bei Klick auf das Müllsymbol
			})
			$("#mitgliederliste").append($(this).unbind("click").clone(true).append(muell));
			$("#auswahlliste").html("");
			$("#hinzufuegenAuswahl").hide();
		});
		$("#auswahlliste").append(listenelement);//Listenelement wird zur Liste hinzugefügt
	});
}