var contentWrapper;
var menu;
var panel;
var header;
var body;
var menupunkte;

window.addEventListener("load",function(){
	contentWrapper = document.getElementById("contentWrapper");
	menu = document.getElementById("menu");
	panel = document.getElementById("panel");
	header = document.getElementById("header");
	body = document.getElementsByTagName("body")[0];
	menupunkte = document.getElementsByClassName("menupunkt");
	/*
	for(var i=0; i<menupunkte.length;i++){
		menupunkte[i].children[0].style.marginTop=menupunkte[i].clientHeight/2-menupunkte[i].children[0].offsetHeight/2+"px";	
	}
	*/
	
	groesseAnpassen();
	window.addEventListener("resize",function(){
		groesseAnpassen();
	});
});

function groesseAnpassen(){
	contentWrapper.style.height=window.innerHeight-header.offsetHeight+"px";
	panel.style.width=window.innerWidth-menu.offsetWidth+"px";
}

function fortschrittHandler(element,fortschrittObjekt){
	$(element).html("");
	$(element).addClass("fortschrittContainer");
	
	this.status = (typeof fortschrittObjekt.status !== 'undefined') ? fortschrittObjekt.status : 0;
	this.limit = (typeof fortschrittObjekt.limit !== 'undefined') ? fortschrittObjekt.limit : 100;
	
	var balken = $("<div>");
	balken.addClass("fortschritt");
	$(element).append(balken);
	var anzeige = $("<span>");
	anzeige.addClass("fortschrittAnzeige");
	$(element).append(anzeige);
	this.update = function(){
		var prozent = this.status/this.limit * 100;
		balken.css("width",prozent + "%");
		anzeige.text(Math.round(prozent)+ "%");
	}
	this.getStatus = function(){
		return this.status;
	}
	this.setStatus = function(status){
		this.status=status;
		this.update();
	}
	this.getLimit = function(){
		return this.limit;
	}
	this.setLimit = function(limit){
		this.limit=limit;
		this.update();
	}
	this.update();
}

jQuery.prototype.fortschritt = function(fortschrittObjekt){
	return new fortschrittHandler(this,fortschrittObjekt);
	/*
	das fortschrittObjekt sieht z.B so aus:
	{
		status: 0,
		limit: 100
	}
	*/
}

jQuery.prototype.ewigerKreis = function(fortschrittObjekt){
	var ewigerKreisContainer = $("<div>");
	ewigerKreisContainer.addClass("ewigerKreisContainer");
	var ewigerKreis = $("<div>");
	ewigerKreis.addClass("ewigerKreis");
	ewigerKreisContainer.append(ewigerKreis);
	$(this).append(ewigerKreisContainer);
};