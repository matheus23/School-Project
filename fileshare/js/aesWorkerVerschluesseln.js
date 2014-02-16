// Webworker der die Verschlüsselung in einem neuen Thread durchführt
window = self;//Bugfix für forge (im webworker ist das window-Objekt nicht definiert)
importScripts('forge/forge.bundle.js');

var AESKeyZufall;
var AESKeyIv;
var verschluesseler;
self.addEventListener('message', function(event) {
	switch(event.data.aktion){
		case "start":
			AESKeyZufall=event.data.AESKeyZufall;
			AESKeyIv=event.data.AESKeyIv;
			verschluesseler = forge.aes.createEncryptionCipher(AESKeyZufall, 'CBC');
			verschluesseler.start(AESKeyIv);
			self.postMessage({aktion:'weiter',output:""});
			break;
		case "update":
			verschluesseler.update(forge.util.createBuffer(event.data.byteString));
			self.postMessage({aktion:'weiter',output:verschluesseler.output.data});
			verschluesseler.output.clear();
			break;
		case "finish":
			verschluesseler.update(forge.util.createBuffer(event.data.byteString));
			verschluesseler.finish();
			self.postMessage({aktion:'fertig',output:verschluesseler.output.data});
			verschluesseler.output.clear();
			break;
	}
});