# Dokumentation

## Projektorganisation

Zur Projektorganisation und -synchronisierung haben wir verschiedene Online-Dienste und Programme verwendet:

 * **git**: ist ein *Versionsverwaltungssystem* das von uns dazu verwendet wurde, Quelltext gleichzeitig auf mehreren PCs von mehreren 
Programmierern zu editieren und entwickeln und letztendlich zusammenzu-*merge*-en. Ein weiterer Vorteil ist außerdem die Möglichkeit 
sich in der *commit-history* die Änderungen anzeigen zu lassen und diese auch rückgängig machen zu können.
 * **github**: Unser Projekt wird auf einem zentralen Server auf 
[github.com/matheus23/School-Project](github.com/matheus23/School-Project) gespeichert. Dort ist auch ein umfassendes Userinterface 
bereitgestellt zum Anzeigen von Commits, Branches und dem Projekt mit den Dateien selbst. Um den Code auf github ändern zu können, 
muss der Projekt-admin (matheus23) die jeweiligen User, die die Berechtigung zum ändern bekommen sollen, zu der Contributers-Liste 
hinzufügen.
 * **raspberry**: Auf dem Rapsberry sind im Ordner */var/www/* mehrere Unterordner zu finden mit den Namen *github-[name]* für die 
jeweiligen Programmierer und diese Unterordner enthalten jeweils ein git-repository in dem der Code editiert wird und die Änderungen 
Dokumentiert werden. Der Raspberry ist über das World-Wide-Web mit der Domain rpi.limond.de erreichbar. So ist es für uns (zusammen 
mit shell-in-a-box und shiftedit) möglich, von überall aus den Code für unser Projekt zu entwickeln.
 * **shiftedit**: shiftedit.net ist ein Web-IDE mit dem es möglich ist Code direkt auf einem (aus sich von Stackedit.net aus) fremden 
Server zu entwickeln. Dies verwenden wir um auf dem Raspberry mit Syntax-Highlighting und Syntax-Checking Dateien editieren zu können. 
Dies ist sicher, da sich stackedit.net per ssh mit dem Raspberry verbindet und man das Passwort angeben muss.
 * **shell-in-a-box**: Ist ein Programm, bzw. Webserver der auf dem Raspberry (zu diesem Zeitpunkt) auf Port 4200 zuhört und es 
ermöglich, eine ssh-konsole im Browser zu öffnen.
 * **Skype**:
 * **trello**:
