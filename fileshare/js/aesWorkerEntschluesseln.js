// Webworker der die Verschlüsselung in einem neuen Thread durchführt
window = self;//Bugfix für forge (im webworker ist das window-Objekt nicht definiert)
importScripts('forge/forge.bundle.js');

var AESKeyZufall;
var AESKeyIv;
var entschluesseler;
self.addEventListener('message', function(event) {
	switch(event.data.aktion){
		case "start":
			AESKeyUnverschluesselt=event.data.AESKeyUnverschluesselt;
			AESKeyIv=event.data.AESKeyIv;
			entschluesseler = forge.aes.createDecryptionCipher(AESKeyUnverschluesselt, 'CBC');
			entschluesseler.start(AESKeyIv);
			self.postMessage({aktion:'weiter',output:""});
			break;
		case "update":
			entschluesseler.update(forge.util.createBuffer(event.data.byteString));
			self.postMessage({aktion:'weiter',output:entschluesseler.output.data});
			entschluesseler.output.clear();
			break;
		case "finish":
			entschluesseler.update(forge.util.createBuffer(event.data.byteString));
			entschluesseler.finish();
			self.postMessage({aktion:'fertig',output:entschluesseler.output.data});
			entschluesseler.output.clear();
			break;
	}
});