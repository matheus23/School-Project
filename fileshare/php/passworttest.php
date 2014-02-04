<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Startseite LGÖ-Schoolproject</title>
</head>
<body>
<?php
include "../php/utilities.php";
include_once "../securimage/securimage.php";

debugModus();

$data = $_POST;
$nrt = new Nachrichten("#fehlerListe");
$securimage = new Securimage();
if (alleSchluesselGesetzt($data, "eanmeld", "pwanmeld")) {
	$emaila = strtolower($_POST["eanmeld"]);
	$pwa = ($_POST["pwanmeld"]);
	$db = oeffneBenutzerDB($nrt);
	$pwTest = benutzerPwTest($db, $emaila, $pwa);
	if ($pwTest == WRONG_EMAIL) {
		echo "Falsche Email";
	} 
	elseif ($pwTest == WRONG_COMBINATION) {
		echo "Falsches Passwort";
	}
	elseif ($pwTest == PASSWORD_PASS) {
		echo "True";
	}
}
$fehlerjs = $nrt->toJsCode();
?>
<script src="../js/pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>