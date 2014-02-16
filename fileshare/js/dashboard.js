var browser = new kompatibilitaetsCheck();
var li;
if(browser.fileSaverUnterstuetzt){
	li = $("<li>").addClass("gruen").text("Dateien können gespeichert werden.");
}
else{
	li = $("<li>").addClass("rot").text("Dateien können nicht gespeichert werden.");
}
$("#kompatibilitaet>ul").append(li);
if(browser.uint8ArrayUnterstuetzt){
	li = $("<li>").addClass("gruen").text("Dateien können korrekt eingelesen/behandelt werden.");
}
else{
	li = $("<li>").addClass("rot").text("Dateien können nicht gespeichert werden.");
}
$("#kompatibilitaet>ul").append(li);
if(browser.cryptoUnterstuetzt){
	li = $("<li>").addClass("gruen").text("Sichere Schlüssel können erstellt werden.");
}
else{
	li = $("<li>").addClass("rot").text("Schlüssel, die du mit diesem Browser erstellst sind nicht sicher! Von der Schlüsselerstellung wird abgeraten.");
}
$("#kompatibilitaet>ul").append(li);