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

function fortschrittHandler(element, status, limit){
	var _this = this;
	var balken = $("<div>");
	balken.addClass("fortschritt");
	$(element).html("");
	$(element).append(balken);
	var update = function(){
		var prozent = _this.status/_this.limit * 100 + "%";
		balken.css("width",prozent);
	}
	this.status=status;
	this.getStatus = function(){
		return this.status;
	}
	this.setStatus = function(status){
		this.status=status;
		update();
	}
	this.limit=limit;
	this.getLimit = function(){
		return this.limit;
	}
	this.setLimit = function(limit){
		this.limit=limit;
		update();
	}
	update();
}

jQuery.prototype.fortschritt = function(status, limit){
	return new fortschrittHandler(this, status, limit);
}
