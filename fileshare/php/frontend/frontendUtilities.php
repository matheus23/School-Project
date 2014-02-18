<?php
include_once dirname(__FILE__) . "/../utilities.php";
//include_once dirname(__FILE__) . "/Menu.php";
require_once(rootdir."fileshare/php/frontend/Menu.php");

function generateHTMLGruppen($db){
	$seid = $_SESSION["seid"];
	$gruppenHTML="";
	$sql="SELECT ID,Name FROM Gruppe WHERE ModeratorID='$seid'";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$gruppenHTML){
			while($gruppe = $ergebnis->fetch_array(MYSQLI_ASSOC)){
				$id = $gruppe["ID"];
				$name = $gruppe["Name"];
				$gruppenHTML .= "<div class='listenelement' data-id='$id'><span class='listenlabel'>$name</span></div>";
			}
		},
		function($fehlerNachricht){
		}
	);
	return $gruppenHTML;
}
function generateHTMLMitglieder($db,$GruppenID){
	$seid = $_SESSION["seid"];
	$mitgliederHTML="";
	$sql="SELECT Gruppenmitglieder.NutzerID FROM Gruppe,Gruppenmitglieder WHERE Gruppe.ModeratorID='$seid' AND Gruppe.ID='$GruppenID' AND Gruppe.ID = Gruppenmitglieder.GruppenID";
	$db->query($sql)->fold(
		function ($ergebnis) use(&$mitgliederHTML,$db){
			while($mitglied = $ergebnis->fetch_array(MYSQLI_ASSOC)){
				$nutzerID = $mitglied["NutzerID"];
				$email = NutzerIDZuEmail($nutzerID,null);
				$sql="SELECT Nutzername FROM Benutzer WHERE ID='$nutzerID'";
				$nutzername = "";
				$db->query($sql)->fold(
					function ($ergebnis) use(&$nutzername){
						while($nutzer = $ergebnis->fetch_array(MYSQLI_ASSOC)){
							$nutzername=$nutzer["Nutzername"];
						}
					},
					function($fehlerNachricht){
					}
				);
				$mitgliederHTML .= "<div class='listenelement' data-nutzerid='$nutzerID'><span class='listenlabel'>$nutzername - $email</span></div>";
			}
		},
		function($fehlerNachricht){
		}
	);
	return $mitgliederHTML;
}

function aktuelleNutzerIDJavaScript(){
	$seid = $_SESSION["seid"];
	return "<script>var seid='$seid';</script>";
}

//Token gegen das ungewollte abschicken von formulardaten durch fremde Seiten
//Benötigt eine offene Session
class CSRFSchutz{
	public $token;
	public function __construct($token=""){
		$this->token=$token;
	}
	public function neu(){//Erstellung eines neuen Tokens (pro request oder mehrere requests)
		$_SESSION["token"] = sichereID("CSRFToken_",30);
		$this->token = $_SESSION["token"];
		return $this;
	}
	public function get(){//Gibt den Token zurück
		return $this->token;
	}
	public function post(){//Liest den Token aus den Formulardaten
		if(alleSchluesselGesetzt($_POST,"CSRFToken")){
			$this->token = $_POST["CSRFToken"];
		}
		else{
			$this->token = "";
		}
		return $this;
	}
	public function pruefe(){//Prüft ob der jetzige Token mit der Sessionvariable übereinstimmt
		if($this->token == $_SESSION["token"]){
			return true;
		}
		else{
			return false;
		}
	}
	public function autoUmleitung(){
		if (!$this->pruefe()){
			session_destroy();//Löscht die Session serverseitig
			header("Location: http://".host.dirname(dirname($_SERVER["REQUEST_URI"]))."/Anmeldung.php");//Umleitung zur Anmeldung
			die();//Verhindert das weitere Ausführen von Code
		}
	}
	public function genHTML(){//Generiert ein Input mit dem Token
		$token = $this->token;
		return "<input type='hidden' id='CSRFToken' name='CSRFToken' value='$token'/>";
	}
	public function genJS(){//Generiert javascript mit dem Token
		$token = $this->token;
		return "<script>var CSRFToken='$token'</script>";
	}
}

function NutzerIDZuEmail($nutzerID,$nrt){
	if(!isset($nrt)) $nrt = new Nachrichten("","");//Nachrichten-dummy, falls intern ohne Fehlerbehandlung benutzt
	$db = oeffneBenutzerDB($nrt);
	$sql="SELECT Email FROM Benutzer  WHERE ID='$nutzerID'";
	$email;
	$db->query($sql)->fold(
		function ($ergebnis) use (&$email){
			$email = $ergebnis->fetch_array()[0];
		},
		function($fehlerNachricht) use (&$nrt) {
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
	return $email;
}

function EmailZuNutzerID($email){//Nachrichten-dummy, falls intern ohne Fehlerbehandlung benutzt
	if(!isset($nrt)) $nrt = new Nachrichten("","");//Nachrichten-dummy, falls intern ohne Fehlerbehandlung benutzt
	$db = oeffneBenutzerDB($nrt);
	$sql="SELECT ID FROM Benutzer WHERE Email='$email'";
	$id;
	$db->query($sql)->fold(
		function ($ergebnis) use (&$id){
			$id = $ergebnis->fetch_array()[0];
		},
		function($fehlerNachricht) use (&$nrt) {
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
	return $id;
}

$frontendMenu = array(
  new Menupunkt("dashboard","Dashboard","dashboard.php", "img/house.png"),
  new Menupunkt("download","Download","download.php", "img/application_put.png"),
  new Menupunkt("upload","Upload","upload.php", "img/application_get.png"),
  new Menupunkt("gruppen","Gruppen","gruppen.php", "img/group.png"),
  new Menupunkt("konto","Benutzerkonto","benutzerkonto.php", "img/user.png"),
  new Menupunkt("schluesselverwaltung","Schlüsselverwaltung","schluesselverwaltung.php", "img/key.png")
);

function istAngemeldet() {
	return (isset($_SESSION["semail"])) && ($_SESSION["semail"] != "");
}

function umleitenZuAnmeldung() {
	header("Location: http://".host."/".githubdir."/fileshare/php/Anmeldung.php");
}

function leiteUmWennNichtAngemeldet() {
	if (!istAngemeldet()) {
		session_destroy();
		umleitenZuAnmeldung();
	}
}
?>