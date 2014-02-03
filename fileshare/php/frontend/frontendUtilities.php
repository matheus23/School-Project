<?php
include_once "../utilities.php";
include_once "Menu.php";

function generateHTMLGruppen($db){
	$semail = $_SESSION["semail"];
	$gruppenHTML="";
	$sql="SELECT ID,Name FROM Gruppe WHERE ModeratorEmail='$semail'";
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
	$semail = $_SESSION["semail"];
	$mitgliederHTML="";
	$sql="SELECT Gruppenmitglieder.NutzerEmail FROM Gruppe,Gruppenmitglieder WHERE Gruppe.ModeratorEmail='$semail' AND Gruppe.ID='$GruppenID' AND Gruppe.ID = Gruppenmitglieder.GruppenID";
	$db->query($sql)->fold(
		function ($ergebnis) use(&$mitgliederHTML,$db){
			while($mitglied = $ergebnis->fetch_array(MYSQLI_ASSOC)){
				$email = $mitglied["NutzerEmail"];
				$sql="SELECT Nutzername FROM Benutzer WHERE Email='$email'";
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
				$mitgliederHTML .= "<div class='listenelement' data-email='$email'><span class='listenlabel'>$nutzername - $email</span></div>";
			}
		},
		function($fehlerNachricht){
		}
	);
	return $mitgliederHTML;
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
	$db = oeffneBenutzerDB($nrt);
	$sql="SELECT Email FROM Benutzer  WHERE ID='$nutzerID'";
	$email;
	$db->query($sql)->fold(
		function ($ergebnis) use (&$email){
			$email = $ergebnis->fetch_array()[0];
		},
		function($fehlerNachricht) use (&$nrtGruppe) {
			$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			echo json_encode(array("nrt"=>$nrt->toJsCode()));
			die();
		}
	);
	return $email;
}

function EmailZuNutzerID($email,$nrt){
	$db = oeffneBenutzerDB($nrt);
	$sql="SELECT ID FROM Benutzer WHERE Email='$email'";
	$id;
	$db->query($sql)->fold(
		function ($ergebnis) use (&$id){
			$id = $ergebnis->fetch_array()[0];
		},
		function($fehlerNachricht) use (&$nrtGruppe) {
			$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			echo json_encode(array("nrt"=>$nrt->toJsCode()));
			die();
		}
	);
	return $id;
}

$frontendMenu = array(
  new Menupunkt("dashboard","Dashboard","dashboard.php"),
  new Menupunkt("download","Download","download.php"),
  new Menupunkt("upload","Upload","upload.php"),
  new Menupunkt("gruppen","Gruppen","gruppen.php"),
  new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"),
  new Menupunkt("schluesselverwaltung","Schlüsselverwaltung","schluesselverwaltung.php")
);

function istAngemeldet() {
	return (isset($_SESSION["semail"])) && ($_SESSION["semail"] != "");
}

function umleitenZuAnmeldung() {
	header("Location: http://".host.dirname(dirname($_SERVER["REQUEST_URI"]))."/Anmeldung.php");
}

function leiteUmWennNichtAngemeldet() {
	if (!istAngemeldet()) {
		session_destroy();
		umleitenZuAnmeldung();
	}
}
?>