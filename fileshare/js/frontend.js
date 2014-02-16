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

function fortschrittBoxHandler(box,fortschrittBoxObjekt){
	this.label = (typeof fortschrittBoxObjekt.label !== 'undefined') ? fortschrittBoxObjekt.label : "";
	this.typ = (typeof fortschrittBoxObjekt.typ !== 'undefined') ? fortschrittBoxObjekt.typ : "ewigerKreis";
	this.fortschrittObjekt = (typeof fortschrittBoxObjekt.fortschrittObjekt !== 'undefined') ? fortschrittBoxObjekt.fortschrittObjekt : {};
	this.box = box;
	box.children(".boxLabel").text(this.label);
	switch(this.typ){
		case "ewigerKreis":
			box.children(".anzeige").css("width","64px");
			box.children(".anzeige").css("height","64px");
			$(box.children(".anzeige").first()).ewigerKreis();
			break;
		case "fortschritt":
			box.children(".anzeige").css("width","100%");
			box.children(".anzeige").css("height","22px");
			this.anzeige = $(box.children(".anzeige").first()).fortschritt(this.fortschrittObjekt);
			break;
	}
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

jQuery.prototype.ewigerKreis = function(){
	$(this).html("");
	var ewigerKreisContainer = $("<div>");
	ewigerKreisContainer.addClass("ewigerKreisContainer");
	var ewigerKreis = $("<div>");
	ewigerKreis.addClass("ewigerKreis");
	ewigerKreisContainer.append(ewigerKreis);
	$(this).append(ewigerKreisContainer);
};
 
 jQuery.prototype.fortschrittBox = function(fortschrittBoxObjekt){
 	var box = $("<div>").addClass("boxMitRand").addClass("fortschrittBox");
	var label = $("<p>").addClass("boxLabel");
	var anzeige = $("<div>").addClass("anzeige");
	box.append(label).append(anzeige);
	$(this).append(box);
	return new fortschrittBoxHandler(box,fortschrittBoxObjekt);
	/*
	das fortschrittBoxObjekt sieht z.B so aus:
	{
		label: "Das steht über dem Balken/ dem Kreis",
		typ: "fortschritt",
		fortschrittObjekt: fortschrittObjekt
	}
	
	typ kann auch ewigerKreis sein
	*/
};

var kompatibilitaetsCheck = function(){
	this.fileSaverUnterstuetzt = false;
	this.uint8ArrayUnterstuetzt = false;
	this.cryptoUnterstuetzt = false;
	
	try {//siehe https://github.com/eligrey/FileSaver.js/
		this.fileSaverUnterstuetzt = (!!new Blob() && !!window.File && !!window.FileReader);
	}
	catch(e){}
	
	try {
		this.uint8ArrayUnterstuetzt = !!new Uint8Array();
	}
	catch(e){}
	
	try {
		this.cryptoUnterstuetzt = !!(window.crypto || window.msCrypto);
	}
	catch(e){}
}