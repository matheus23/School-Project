#Dokumentation von Luca

###Die Benutzerkontoverwaltung:

Die Verwaltung des Benutzers ist grob in zwei Teile geteilt:

 * **Das Benutzerkonto**
 * **Die Funktionen**

In der Datei **Benutzerkonto.php** ist alles geschrieben, was der Nutzer sehen kann:

 * Die Wahl welche Daten er ändern möchte:
     *  Den Namen ändern
     *  Die Email ändern
     *  Das Passwort ändern
     *  Den Account löschen

**Dies ist über Javascript gelöst:**
Bei einem Klick auf die jeweilige Zeile im Benutzerkonto wird ein Javascript ausgeführt.
Diese lädt die jeweilig ausgewählte Form auf dem php-file und lässt sie dem Nutzer anzeigen.
Wenn nun der Nutzer auf eine weitere Zeile klickt, um diese aufzurufen, wird erst die alte Form geschlossen und nicht mehr angezeigt, damit man dann im nächsten Schritt die neu Ausgewählte anzeigen kann. So überlagen sich die einzelnen Formulare nicht.

**Die Eingabe:**
Die Eingabe ist eigentlich sehr simpel. Wir haben zu jeder Funktion jeweils eine Form mit der Methode *post* und den benötigten Eingabeinformationen, welche mit den verschiedenen *Input Types*, in unserem Fall *text*, *email* und *password*,  realisiert sind. Alle Forms können per *submit* an den nächsten Schritt übergeben werden.
Wenn dies passiert ist, wird im Formular gecheckt, welche Eingabe vom Nutzer getroffen worden ist, also ob er etwas an seinen Daten ändern oder seinen Account löschen möchte. Dies ist über eine einfache *if*-Abfrage gelöst.

**Sind alle Eingaben gemacht:**
Als erstes wird nun eine Funktion aufgerufen, die checkt, ob der Nutzer auch alle benötigten Eingaben getroffen hat.
Ist dies nicht der Fall wird die ausgewählte Aktion nicht ausgeführt, der Nutzer bekommt dafür eine Fehlermeldung, dass nicht alle benötigten Eingaben gemacht worden sind.
Falls aber alle Daten gegeben sind, kommen die Funktionen und die Fehlerausgabe ins Spiel.


In den **websiteFunktionen** ist alles geschrieben, was der Nutzer nicht sehen kann:

* Die ausgeführte Funktion:
     * Check, ob alle Daten richtig sind
     * Rückgabe von Fehlern bzw. die gelungene Änderung
     * Die Datenbankänderungen

**Nutzereingabe wird überprüft:**
Als erstes wird von php getestet ob alle Eingaben durch den Nutzer richtig eingegeben worden sind. Durch eine Funktion wird als erstes mit der Datenbank abgeglichen, ob die eingegebene Email zu einem Nutzer zugeordnet werden kann. Falls nein, wird die Funktion nicht mehr weiter ausgeführt und dem Nutzer wird eine Fehlermeldung angezeigt.
Im Falle der Passwortänderung kommt jetzt eine Abfrage ob das neue Passwort mit der Wiederholung übereinstimmt. Falls nein, erscheint eine Fehlermeldung.
Im Falle ja, geht es zum nächsten Punkt, der Kontrollo ob dass Passwort zur Email stimmt. Dies ist wieder in einer weiteren Funktion geregelt und wird bei allen Änderungen abgefragt.
Sollte diese letzte Abfrage auch korrekt sein, kommt es zum letzten Punkt.

**Datenbank wird geupdatet**
In diesem letzten Punkt wird ganz einfach die Datenbankinformation mit der jeweiligen Änderung ersetzt. Hierzu nutzen wir die Email, da diese eindeutig ist.
Falls es hierbei zu einem Fehler kommen sollte, folgt eine Fehlermeldung. Diese ist aber vorallem für den Entwickler von nutzen, da er so weiß die Datenbank hatte einen Fehler. Auf einer fertigen Seite sollte es zu diesm Fehler nicht kommen.

###Fehlerabfrage bei der Benutzerkontenverwaltung
Dem Nutzer können fünf Grundlegene Fehler angezeigt werden:

 * Es wurden keine Angaben gemacht
 * Die eingebene Email ist nicht vorhanden
 * Email und Passwort stimmen nicht überein
 * Im Falle der Passwortänderung: Passwort und dessen Wiederholung stimmen nicht überein
 * Es gab einen Datenbankfehler

Diese Fehler werden über eine switch - case Funktion ausgewählt und angezeigt.

Mitbearbeitete Dateien:
 
> Anmeldung.php benutzerkonto.php emailaenderung.php namensaenderung.php passwortaenderung.php loeschung.php registrierung.php dashboard.php 

Genaueres siehe Github https://github.com/matheus23/School-Project
