#Das Uploadsystem
##Verschlüsselung
###Der Dateischlüssel
Der Dateischlüssel ist ein RSA-Schlüsselpaar, das heißt er besteht aus einem privaten und einem öffentlichen Schlüssel. Der öffentliche Schlüssel wird an den Server übergeben, sodass andere Nutzer Dateien mit ihm verschlüsseln können. Der private Schlüssel wird mit einem Passwort verschlüsselt (symmetrische Verschlüsselung, AES-256) und anschließen lokal gespeichert (im HTML5-localStorage).
###Der Signaturschlüssel
Der Signaturschlüssel ist ebenfalls ein RSA-Schlüsselppar, mit dem genauso verfahren wird, wie mit dem Dateischlüssel. Der Signaturschlüssel ist dazu da, die Prüfsumme der Datei zu signieren, sodass beim Empfänger mit dem öffentlichen Schlüssel verifiziert werden kann, ob es sich tatsächlich um den vermeintlichen Sender handelt. Der öffentliche Teil wird zum Server geschickt der private Schlüssel wird wieder mit einem Passwort verschlüsselt (symmetrische Verschlüsselung, AES-256) und anschließen lokal gespeichert (im HTML5-localStorage).

###Beispiel
NutzerA und NutzerB melden sich bei Secureshare an und erstellen jeweils einen Dateischlüssel und einen Signaturschlüssel:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/1GW0yqGweFnrvwwA75bWMSDKvarZuVRFBRJD9qLgCXgw/pub?w=1440&amp;h=1080)
NutzerA möchte nun NutzerB "vollGeheimesDokument.pdf" schicken. Dazu wählt er die Datei in upload.php aus und gibt sein Passwort für den Signaturschlüssel ein:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/1C0m11v2X8yH0kcLzMcU2LTFdAIZn-wGctQeIgzrS42w/pub?w=1440&h=1080)
NutzerB kann in download.php nun die Datei downloaden und mit seinem privaten Dateischlüssel entschlüsseln:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/1OB-7orXO9UFzyrOHeVEunIfax1RQsEBmBBhbvznsydk/pub?w=1440&h=1080)

###Dateiverarbeitung

Die Schaubilder beantworten Fragen zur Dateibehandlung nicht vollständig. So wird die Datei beim Einlesen als ArrayBuffer bzw. Uint8Array. Uint8Arrays sind Arrays mit jeweils einem Byte pro Element als Integer, also eine Zahl zwischen 0 und 255.

Dann gibt es die BinaryStrings, mit denen die Verschlüsselungs-Bibliothek forge arbeitet bzw. zurückgibt. Das sind Zeichenketten deren Zeichen für jeweils ein Byte stehen. Ein Beispiel: Der Uint8Array mit nur einem Element ["33"] wird zu dem BinaryString "!" weil das "!" das 33. Zeichen des Unicode-Zeichensatzes ist.

forge.util.createBuffer ist eine bestimmte Representation der binären Daten, die von der Verschlüsselungs-Bibliothek forge während der Verschlüsselung benutzt wird.

btoa() und atob() kodieren Daten nach base64 bzw. zurück. Bei base64 kodierten Binärdaten handelt es sich um eine Representation der Daten, die sicher für den Transport durch Netzwerke ist, da nur alphanumerische Zeichen sowie "+","/" und "=" verwendet werden. Die Daten sind allerdings entsprechend größer.

Eine Vorstellung der Funktionsweise bekommt man durch folgendes Bild, oder einfach mal auf Wikipedia schauen.

!["Base64"](http://upload.wikimedia.org/wikipedia/commons/b/ba/Base64-da.png)

[von MathiasRav at da.wikipedia [Public domain], via Wikimedia Commons](http://commons.wikimedia.org/wiki/File%3ABase64-da.png)

##aktuelle Probleme
* Noch kein Gruppenupload
* Kein wirkliches Uploadlimit, bis auf das Standartlimit von php, das bei Überschreitung zu Fehlern führt.
* Bisher sind private Schlüssel im localStorage nicht übertragbar auf andere Browser, was das Benutzen an anderen Browsern oder geräten unmöglich macht.
* Dateien sind noch nicht löschbar.
* Dateiliste zeigt alle Dateien an und besitzt kein Limit
* Permanentes Speichern der Schlüssel auf die localStorage-Technologie beschränkt
* viele Funktionen sind auf älteren Browsern so nicht vorhanden.
* mehrere kleine bis mittelschwere Designproblemchen
* forge ist eigentlich die Implementierung von TLS in Javascript, deshalb können noch ein paar Dateien gelöscht werden, die nicht gebraucht werden

###Lösungsmöglichkeiten
1. Das meiste lässt sich durch mehr Programmieren beheben.
2. Ähnlich wie FileSaver.js den Download behandelt wüden zusätzliche Bibliotheken einen umfassenden Browser-Support ermöglichen.
	- PersistJS zum Beispiel würde das lokale speichern der Schlüssel durch 6 verschiedene Technologien absichern, sodass wesentlich mehr Browser unterstützt werden könnten.