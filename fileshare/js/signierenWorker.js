// Webworker der die Verschlüsselung in einem neuen Thread durchführt
window = self;//Bugfix für forge (im webworker ist das window-Objekt nicht definiert)
importScripts('forge/forge.bundle.js');

self.addEventListener('message', function(event) {
	var hasher = forge.md.sha256.create();
	hasher.update(event.data.dateiVerschluesselt);
	var signatur = forge.pki.privateKeyFromPem(event.data.signaturschluesselUnverschluesselt).sign(hasher);//256-Bit Signatur
	self.postMessage(signatur);
});