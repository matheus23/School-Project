# Dokumentation

## Projektorganisation

Zur Projektorganisation und -synchronisierung haben wir verschiedene Online-Dienste und Programme verwendet:

### Git
Git ist ein *Versionsverwaltungssystem* das von uns dazu verwendet wurde, Quelltext gleichzeitig auf mehreren PCs von mehreren Programmierern zu editieren und entwickeln und letztendlich zusammenzu-*merge*-en.

Ein weiterer Vorteil ist außerdem die Möglichkeit sich in der *commit-history* die Änderungen anzeigen zu lassen und diese auch rückgängig machen zu können (siehe Bild bei "[Github](#github)").

### Github
Unser Projekt wird auf einem zentralen Server auf [github.com/matheus23/School-Project](github.com/matheus23/School-Project) gespeichert. Dort ist auch ein umfassendes Userinterface bereitgestellt zum Anzeigen von Commits, Branches und dem Projekt mit den Dateien selbst.

Um den Code auf Github ändern zu können, muss der Projekt-Admin (matheus23) die jeweiligen User, die die Berechtigung zum Ändern bekommen sollen, zu der Contributers-Liste hinzufügen. ![Github und Git](http://i.imgur.com/6EE8idk.png)

### Raspberry
Auf dem Rapsberry sind im Ordner */var/www/* mehrere Unterordner zu finden mit den Namen *github-[name]* für die jeweiligen Programmierer. Diese Unterordner enthalten jeweils ein git-repository in dem der Code editiert wird und die Änderungen Dokumentiert werden.

Der Raspberry ist über das World-Wide-Web mit der Domain **rpi.limond.de** erreichbar. So ist es für uns (zusammen mit *Shell-in-a-Box* und *Shiftedit*) möglich, von überall aus den Code für unser Projekt zu entwickeln.

### Shiftedit
Shiftedit.net ist ein Web-IDE mit dem es möglich ist Code direkt auf einem (aus sich von Stackedit.net aus) fremden Server zu entwickeln. Dies verwenden wir um auf dem Raspberry mit Syntax-Highlighting und Syntax-Checking Dateien editieren zu können. Das funktioniert sicher, da sich stackedit.net per ssh mit dem Raspberry verbindet und man das Passwort angeben muss.

Des weiteren erlaubt uns Shiftedit sogar eine parallele **Echtzeiteditierung** einer Datei. So konnten wir zum Beispiel Fehler zu dritt finden und verbessern, obwohl wir alle zuhause waren.
![Stackedit.net](http://i.imgur.com/bJGCs6g.png)

### Shell-in-a-Box
Shell-in-a-Box ist ein Programm, bzw. Webserver der auf dem Raspberry (zu diesem Zeitpunkt) auf Port 4200 eingerichtet ist und es ermöglich, eine ssh-Konsole im Browser zu öffnen.

So können wir von überall aus auf das Filesystem des Raspberrys zugreifen und beispielsweise *git pull* oder *git push* Befehle ausführen.
![Shell-in-a-Box](http://imgur.com/ADwloSS.png)

### Skype
Zur Kommunikation außerhalb der Schulstunden verwendeten wir *Skype*, mit welchem wir einen Gruppenchat erstellten und uns so per instant-messaging austauschten. Außerdem ist es so möglich, Dateien zu verschicken, falls beispielsweise ein Screenshot eines Fehlers geteilt werden soll.

### Trello
Trello ist eine Website, mit welcher man Todo-Listen erstellen kann. Diese sehen so aus: ![Trello.com](http://i.imgur.com/OAcoLna.png)

Mit diesen ermöglicht Trello uns unsere Gedanken schnell zu dokumentieren, sortieren und einzuordnen und zudem können wir so zeigen, woran wer gerade arbeitet.

## Vorteile unserer Projektorganisierung

Da fast alle unsere verwendeten Entwicklungsmittel web-basiert sind, können wir von praktisch jedem PC, welcher einen neuen Browser und Internetzugang hat Entwickeln und Testen.

Das ist insofern nötig, da wir sowohl in der Schule als auch Zuhause arbeiten, und ungerne unsere Daten hin und her kopieren.

Desweiteren haben wir den Vorteil, dass das Projekt auf einem zentralen Server läuft, und nicht auf den Computern, auf denen Entwickelt wird. Der Vorteil davon ist, dass wir direkt auf dem Produktionssystem testen, d.h. die Programme werden sofort mit den Konfigurationen und dem Filesystem des Raspberrys getestet.
