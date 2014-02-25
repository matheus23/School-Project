Dokumentation von Yannick
=========================

Registrierung und Anmeldung der Benutzer
-------------------------------------------------
Für jede dieser drei Funktionen existiert eine eigene Datei in dem Ordner */fileshare/php/*.

**Registrierung(.php)**
Zu Beginn des Codes wird eine Session gestartet, um die Nutzerspezifischen Variablen weiterzugeben. Diese Seite besteht aus vier Textfeldern, einer Captcha und zwei Buttons, um die Eingaben zu bestätigen oder zu löschen. Solange nicht alle Felder ausgefüllt sind, erscheint eine mit HTML5 erzeugte Fehlermeldung. Unterstützt der benutzte Browser kein HTML5, wird trotzdem durch eine if-Abfrage geprüft, ob die Felder alle ausgefüllt sind. Mit dieser entweder durch HTML 5 oder durch die if-Abfrage erzugte Fehlermeldung wird der Nutzer darauf hingewiesen, dass er noch nicht alle Felder ausgefüllt hat. Zusätzlich wird noch eine Captcha generiert, die Massenanmeldungen durch externe Computer bzw. Programme verhindert.
Sind dann alle Felder ausgefüllt, und die Captcha ist richtig, wird die Email zu Kleinbuchstaben konvertiert, und die Funktion *verarbeiteRegistrierung()* aus der Datei */fileshare/php/websiteFunktionen/registrierung.php* aufgerufen. Diese Funktion prüft, ob die Email schonmal vorhanden ist, und ob das Passwort mit der Wiederholung übereinstimmt. Anschließend wird das Passwort hash-verschlüsselt, und ein Datenbankzugriff formuliert, der die Daten in die Datenbank einfügt. Ist diese Funktion abgearbeitet,wird durch eine Funktion noch eine Email generiert, mit der der Nutzer seinen Account bestätigenm muss. Ist dies geschehen, wird der User automatisch auf die Anmeldeseite umgeleitet. Auf der Anmeldeseite erscheint dann eine Meldung, in der eine abgesendete Email angekündigt wird. Wenn der Nutzer die Email empfangen hat, muss er zur Bestätigung seines Accounts den in der Email enthaltenen Link öffnen. Tut er dies, wird in der Tabelle *Bestätigt* der Datenbank *Fileshare* der Wert von *Bestaetigt* von '0' auf '1' geändert. Erst wenn dies geschehen ist, kann der User sich anmelden.

**Anmeldung(.php)**
Hat der User schon einen Account, kann er direkt auf der Startseite bleiben, und sich mit seiner Email und dem Passwort anmelden. Zusätzlich kann man bei *Email merken* ein Häkchen setzen, um einen Cookie zu setzen, in dem die Email abgelegt wird. Hat der Nutzer alle Felder ausgefüllt, wird mit der Funktion *verarbeiteAnmeldung* geprüft, ob dieser User vorhanden ist. Ist er vorhanden, wird das Passwort hash-verschlüsselt, und mit dem auf dem Server abgeglichen. Zum Schluss wird noch geprüft, ob der User bereits bestätigt wurde. Wenn dies der Fall ist, wird man automatisch auf das Dashboard umgeleitet. Ist eines dieser Dinge nicht der Fall, wird eine entsprechende Fehlermeldung ausgegeben.

**Passwort vergessen (pwvergessen.php)**
Wenn der User sein Passwort vergessen hat, ruft er über die Startseite die entsprechende Seite auf, in der er dann seine Email Adresse eintragen kann. Wenn er dies getan hat, und das Formular absendet, wird automatisch eine Email an ihn generiert, die einen Link zur Rücksetzung des Passworts beinhaltet. Dieser ist ab Versenden der Email genau 24 Stunden gültig. Für diese Funktion ist in der Datenbank *Fileshare* eine extra Tabelle *Passwortreset* enthalten, die aus drei Spalten -der Email, der ID und dem Verfalldatum- besteht. Öffnet man vor Ablauf der 24 Stunden diesen Link, so wird man auf eine Seite umgeleitet, auf der man dann sein Passwort ändern kann.



Mitbearbeitete Dateien:
>loeschen.php
>pwvergessen.php
>Anmeldung.php
>anmeldung.php
>benutzerEmail.php
>utilities.php
>frontedStyle.css
>Registrierung.php
>emailaenderung.php
>upload.php

Genaueres siehe Github
https://github.com/matheus23/School-Project