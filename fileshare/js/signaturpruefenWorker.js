// Webworker der die Verschlüsselung in einem neuen Thread durchführt
window = self;//Bugfix für forge (im webworker ist das window-Objekt nicht definiert)
importScripts('forge/forge.bundle.js');

self.addEventListener('message', function(event) {
	try{
		RSAverifizierungsSchluessel = forge.pki.publicKeyFromPem(event.data.verifizierungsSchluessel);
		var hasher = forge.md.sha256.create();
		hasher.update(event.data.dateiVerschluesselt64);
		var verifiziert = RSAverifizierungsSchluessel.verify(hasher.digest().bytes(), event.data.signatur);
		if(!verifiziert){
			self.postMessage(false);
		}
		else{
			self.postMessage(true);
		}
	}
	catch(e){
		self.postMessage(false);
	}
});