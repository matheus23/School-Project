Dokumentation
============================

**Inhaltsverzeichnis**  *generated with [DocToc](http://doctoc.herokuapp.com/)*

- [Datenbankstruktur](#datenbankstruktur)
	- [Benutzer](#benutzer)
	- [Gruppe](#gruppe)
	- [Gruppenmitglieder](#gruppenmitglieder)
	- [Passwortreset](#passwortreset)
	- [Signaturschluessel](#signaturschluessel)
	- [Dateischluessel](#dateischluessel)
	- [aktuellerDateischluessel](#aktuellerdateischluessel)
	- [Datei](#datei)
- [Das Uploadsystem](#das-uploadsystem)
	- [Verschlüsselung](#verschl%C3%BCsselung)
		- [NEU](#neu)
		- [Der Dateischlüssel](#der-dateischl%C3%BCssel)
		- [Der Signaturschlüssel](#der-signaturschl%C3%BCssel)
		- [Beispiel](#beispiel)
		- [Dateiverarbeitung](#dateiverarbeitung)
	- [Dateigröße](#dateigr%C3%B6%C3%9Fe)
	- [aktuelle Probleme](#aktuelle-probleme)
		- [Lösungsmöglichkeiten](#l%C3%B6sungsm%C3%B6glichkeiten)

##Datenbankstruktur
kursiv geschriebene Schlüssel sind Primärschlüssel in der Tabelle

###Benutzer
|Schlüssel|Beschreibung|
|---|---|
|***ID***|eine einzigartige ID, die einem Nutzer eindeutig zuordenbar ist *auto increment*|
|**Nutzername**| frei vom Nutzer wählbarer Name *kann vom Nutzer jederzeit geändert werden*|
|**Passwort**| frei vom Nutzer wählbareres Passwort als Hash, das nur für die Anmeldung zuständig ist *kann vom Nutzer jederzeit geändert werden*|
|**Email**| *kann vom Nutzer jederzeit geändert werden*|
|**RegistrierungsID**| wird bei der Registrierung generiert, um einen Link anbieten zu können, der die Registrierung abschließt|
|**Bestaetigt**| gibt an, ob der Nutzer bestätigt wurde|

###Gruppe
|Schlüssel|Beschreibung|
|---|---|
|***ID***|eine einzigartige ID, die einer Gruppe eindeutig zuordenbar ist *auto increment*|
|**Name**| frei vom Nutzer wählbarer Gruppenname *kann vom Nutzer jederzeit geändert werden*|
|**ModeratorID**| gibt an, welchem Nutzer die Gruppe "gehört", nur dieser kann die Gruppe sehen/benutzen|

###Gruppenmitglieder
Die Tabelle gibt an, welche Mitglieder zu welcher Gruppe gehören (n:m-Beziehung)

|Schlüssel|Beschreibung|
|---|---|
|**GruppenID**|eine einzigartige ID, die einer Gruppe eindeutig zuordenbar ist|
|**NutzerID**|eine einzigartige ID, die einem Nutzer eindeutig zuordenbar ist|

###Passwortreset
|Schlüssel|Beschreibung|
|---|---|
|**Email**|E-Mail des Kontos dessen Passwort zurückgesetzt werden soll|
|**ID**|zufällige und einzigartige ID, die im Link für den Passwortreset steht, um nur den Inhaber der E-Mail-Adresse Zugang zu gewähren|
|**Verfalldatum**|gibt an, ab wann der Link in der Email nicht mehr gültig ist (1 Tag nach Anforderung)|

###Signaturschluessel
|Schlüssel|Beschreibung|
|---|---|
|***NutzerID***|eine einzigartige ID, die einem Nutzer eindeutig zuordenbar ist|
|**Schluessel**|öffentlicher Schlüssel, der für jeden Nutzer frei abrufbar ist, um die Signatur zu bestätigen|
|**privaterSchluessel**|base64-kodierter AES-Verschlüsselter privater RSA-Schlüssel, der nur vom zugehörigen Nutzer abrufbar ist|
|**VersionID**|ID für einen Schlüssel, die pro Nutzer eindeutig und einzigartig ist|

###Dateischluessel
|Schlüssel|Beschreibung|
|---|---|
|**NutzerID**|eine einzigartige ID, die einem Nutzer eindeutig zuordenbar ist|
|**Schluessel**|öffentlicher Schlüssel, der für jeden Nutzer frei abrufbar ist, um die Signatur zu bestätigen|
|**privaterSchluessel**|base64-kodierter AES-Verschlüsselter privater RSA-Schlüssel, der nur vom zugehörigen Nutzer abrufbar ist|
|**VersionID**|ID für einen Schlüssel, die pro Nutzer eindeutig und einzigartig ist|
 im Gegensatz zur Signaturschluessel-Tabelle können mehrere Schlüssel pro Nutzer vorkommen.

###aktuellerDateischluessel
Tabelle ordnet einem Nutzer den aktuellsten Dateischlüssel zu.

|Schlüssel|Beschreibung|
|---|---|
|***NutzerID***|eine einzigartige ID, die einem Nutzer eindeutig zuordenbar ist|
|**VersionID**|ID für einen Schlüssel, die pro Nutzer eindeutig und einzigartig ist|

###Datei
|Schlüssel|Beschreibung|
|---|---|
|***ID***|eine einzigartige ID, die einer Datei eindeutig zuordenbar ist *auto increment*|
|**Name**|Dateiname|
|**SchluesselID**|VersionID des verwendeten Schlüssels, der zum Verschlüsseln der Datei verwendet wurde|
|**Ersteller**|ID des Nutzers, der die Datei hochgeladen hat|
|**Zugriff**|ID des Nutzers, der die Datei angezeigt bekommt und herunterladen kann|

##Das Uploadsystem
###Verschlüsselung
####NEU
* private Schlüssel werden AES-Verschlüsselt auf dem Server, statt verschlüsselt im localStorage gespeichert. Das ermöglicht den geräteübergreifenden Zugriff auf den Service.

####Der Dateischlüssel
Der Dateischlüssel ist ein __RSA-Schlüsselpaar__, das heißt er besteht aus einem privaten und einem öffentlichen Schlüssel. Der öffentliche Schlüssel wird an den Server übergeben, sodass andere Nutzer Dateien mit ihm verschlüsseln können. Der private Schlüssel wird mit einem Passwort verschlüsselt (symmetrische Verschlüsselung, __AES-256__) und anschließend ebenfalls zum Server geschickt.

####Der Signaturschlüssel
Der Signaturschlüssel ist ebenfalls ein __RSA-Schlüsselppar__, mit dem genauso verfahren wird, wie mit dem Dateischlüssel. Der Signaturschlüssel ist dazu da, die Prüfsumme der Datei zu signieren, sodass beim Empfänger mit dem öffentlichen Schlüssel verifiziert werden kann, ob es sich tatsächlich um den vermeintlichen Sender handelt. Der öffentliche Teil wird zum Server geschickt der private Schlüssel wird wieder mit einem Passwort verschlüsselt (symmetrische Verschlüsselung, __AES-256__) und anschließend ebenfalls zum Server geschickt.

####Beispiel
NutzerA und NutzerB melden sich bei Mangoshare an und erstellen jeweils einen Dateischlüssel und einen Signaturschlüssel:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/1qu8DQHO7GqdgA9DXN1K8gV8QXQ8jhT4YvXxYkOhP87o/pub?w=1440&amp;h=1080)
NutzerA möchte nun NutzerB "vollGeheimesDokument.pdf" schicken. Dazu wählt er die Datei in upload.php aus und gibt sein Passwort für den Signaturschlüssel ein:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/13FjYPSqNeYbAmohra5PBzIUbZceny03RBBPMAJ1PA8w/pub?1440&h=1080)
NutzerB kann in download.php nun die Datei downloaden und mit seinem privaten Dateischlüssel entschlüsseln:
!["Schlüsselgenerierung"](https://docs.google.com/drawings/d/1jJdk9J3yu8SbUYlQYK8x9rKtf2zt4lBPs2MaB_ZbPJc/pub?w=1440&h=1080)

\* Die Ver-/Entschlüsselung der privaten Schlüssel ist hier nur vereinfacht dargestellt. Tatsächlich sieht die  Ver-/Entschlüsselung ähnlich aus wie bei den verschlüsselten Dateien. Das heißt es werden zusätzliche Schlüsselinformationen übertragen, die gebraucht werden, um den Schlüssel und den Algorithmus zu "rekonstruieren".

Für die Erstellung des 256 Bit langen AES-Schlüssels aus dem Passwort wir die Funktion "Password-Based Key Derivation Function 2" oder kurz __PBKDF2__ benutzt. Diese wiederum benötigt einen zufällig generierten __Salt__, damit Nutzer mit dem gleichen Passwort dennoch einen unterschiedlichen Schlüssel haben. Dann kommt noch der __AES-Initialisierungsvektor__ oder kurz IV dazu, der eine ähnliche Possition in der AES-Ver-/Entschlüsselung einnimmt, wie der Salt in der PBKDF2-Funktion (also das Bilden unterschiedlicher Ergebnisse bei gleichen Anfangswerten). Salt und IV werden nach der AES-Verschlüsselung der privaten Schlüssel an diese angehängt und später bei der Entschlüsselung zur "Rekonstruktion" eingesetzt.

Außerdem sind Schlüssel und Informationen natürlich auch base64 gespeichert und werden erst auf dem Client dekodiert.

####Dateiverarbeitung

Die Schaubilder beantworten Fragen zur Dateibehandlung nicht vollständig. So wird die Datei beim Einlesen als __ArrayBuffer__ bzw. __Uint8Array__ dargestellt. Uint8Arrays sind Arrays mit jeweils einem Byte pro Element als Integer, also eine Zahl zwischen 0 und 255.

Dann gibt es die __BinaryStrings__, mit denen die Verschlüsselungs-Bibliothek forge arbeitet bzw. zurückgibt. Das sind Zeichenketten deren Zeichen für jeweils ein Byte stehen. Ein Beispiel: Der Uint8Array mit nur einem Element ["33"] wird zu dem BinaryString "!" weil das "!" das 33. Zeichen des Unicode-Zeichensatzes ist.

forge.util.createBuffer ist eine bestimmte Representation der binären Daten, die von der Verschlüsselungs-Bibliothek forge während der Verschlüsselung benutzt wird.

btoa() und atob() kodieren Daten nach __base64__ bzw. zurück. Bei base64 kodierten Binärdaten handelt es sich um eine Representation der Daten, die sicher für den Transport durch Netzwerke ist, da nur alphanumerische Zeichen sowie "+","/" und "=" verwendet werden. Die Daten sind allerdings entsprechend größer.

Eine Vorstellung der Funktionsweise bekommt man durch folgendes Bild, oder einfach mal auf Wikipedia schauen.

!["Base64"](http://upload.wikimedia.org/wikipedia/commons/b/ba/Base64-da.png)

[von MathiasRav at da.wikipedia [Public domain], via Wikimedia Commons](http://commons.wikimedia.org/wiki/File%3ABase64-da.png)


#### Die Webworker
Da Schlüsselgenerierung, Signieren, Vertifizieren und Ver-/Entschlüsseln rechenaufwendige Prozesse sind, werden sie nicht im Hauptthread durgeführt, sondern asynchron durch Webworker realisiert. Das hat den Vorteil, dass UI-Funktionen nicht unterbrochen werden und der Browser keine Meldung über zu lange Skriptausführung ausgibt. Der Webworker zum Verschlüsseln einer Datei sieht schematisch dargestellt etwa so aus:

!["Worker"](https://docs.google.com/drawings/d/1ziv8ys4l1dzWrLOoQXfhoBFRyZVI6mV33Fz6YBBxPTw/pub?w=1440&h=1080)

###Dateigröße
Die Bestimmung der Dateigröße ist auf den ersten Blick verwirrend. Diese muss allerdings ermittelt werden, um den Nutzer vor dem Upload zu großer Dateien zu warnen.
Am besten lässt sich das an einem Beispiel nachvollziehen.
Hochgeladen werden soll eine Datei mit einer Größe von 2360764 Byte (ca. 2,4 MB)
Dabei werden folgende Schritte durchlaufen:

|Nr.|Schritt|Dateigröße (in Byte)|Änderung zum voherigen Schritt|Allgemeine Änderung|Grund der Änderung|
|---|---|---|---|---|---|
|1|Vor dem Einlesen (fileSize-Eigenschaft)|2360764|keine|keine|-|
|2|Nach dem Einlesen (ArrayBuffer/BinaryString)|2360764|keine|keine|-|
|3|Nach dem AES-Verschlüsseln|2360768|+4 Byte|+0-15 Byte|CBC-Padding *siehe unten*|
|4|Nach der Base64-Kodierung|3147692|+786924 Byte|neueGröße = aufrunden(alteGröße/3)\*4|base64 *siehe oben*|
|5|Nach Anhängen der Zusatzinformationen|3148404|+344+344+24 Byte|+344+344+24 Byte|Schlüsselinformationen *siehe oben*|

Die Berechnung der finalen Größe kann so erfolgen:
```
//Die meisten Klammern dienen nur dem Verständnis
(aufrunden((aufrunden(originalGroesse/16)*16)/3)*4)+344+344+24
```
oder in richtigem JavaScript:
```
(Math.ceil((Math.ceil(originalGroesse/16)*16)/3)*4)+344+344+24
```

Das in der Tabelle bereits genannte **CBC-Padding** ist eine Eigenschaft des CBC-Modus der AES-Verschlüsselung. In AES werden die zu verschlüsselnden Daten in Blöcke geteilt (eigentlich immer 128 Bit = 16 Byte pro Block). Es gibt allerdings verschiedene Modi, wie die Blöcke vor der Verschlüsselung "aufbereitet" werden.
Statt jeden Block wie er ist zu verschlüsseln (ECB-Modus = Electronic Codebook), wird im CBC-Modus (Cipher-Block Chaining) jeder unverschlüsselte Block mit einer XOR-Verknüpfung mit dem letzten verschlüsselten Block verknüpft, bevor er selbst verschlüsselt wird. Die erste XOR-Verknüpfung wird mit dem 128 Bit langen Initialisierungsvektor begonnen. Das erhöht die Sicherheit, da gleiche Klartextblöcke unterschiedliche verschlüsselte Blöcke ergeben.
Jetzt der eigentliche Punkt: Bei diesem Modus muss jeder Block die gleiche Größe haben (sonst würde das mit dem XOR schwierig werden). Da das pro Block 128 Bit sind, wird die Datei auf ein vielfaches von 16 Byte aufgefüllt.

Daher stammt auch der Codeteil
```
(Math.ceil(originalGroesse/16)*16)
```

Hier ein Schaubild des CBC-Modus:

!["CBC-Modus"](http://upload.wikimedia.org/wikipedia/commons/8/80/CBC_encryption.svg)

[By WhiteTimberwolf (SVG version) (PNG version) [Public domain], via Wikimedia Commons](http://upload.wikimedia.org/wikipedia/commons/thumb/8/80/CBC_encryption.svg/1000px-CBC_encryption.svg.png)

###aktuelle Probleme
* Noch kein Gruppenupload
* Kein wirkliches Uploadlimit, bis auf das Standartlimit von php, das bei Überschreitung zu Fehlern führt.
* <del>Bisher sind private Schlüssel im localStorage nicht übertragbar auf andere Browser, was das Benutzen an anderen Browsern oder Geräten unmöglich macht.</del>
* Dateien sind noch nicht löschbar.
* Dateiliste zeigt alle Dateien an und besitzt kein Limit
* <del>Permanentes Speichern der Schlüssel auf die localStorage-Technologie beschränkt</del>
* viele Funktionen sind auf älteren Browsern so nicht vorhanden.
* mehrere kleine bis mittelschwere Designproblemchen
* forge ist eigentlich die Implementierung von TLS in Javascript, deshalb können noch ein paar Dateien gelöscht werden, die nicht gebraucht werden

####Lösungsmöglichkeiten
1. Das meiste lässt sich durch mehr Programmieren beheben.
2. Ähnlich wie FileSaver.js den Download behandelt wüden zusätzliche Bibliotheken einen umfassenden Browser-Support ermöglichen.
