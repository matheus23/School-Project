<?php
include_once "../utilities.php";
function generateHTMLGruppen($db){
	$semail = $_SESSION["semail"];
	$gruppenHTML="";
	$sql="SELECT ID,Name FROM Gruppe WHERE ModeratorEmail='$semail'";
	$db->query($sql)->fold(
		function ($ergebnis) use(&$gruppenHTML){
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
?>