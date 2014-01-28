<!DOCTYPE HTML>
<?php
include "../utilities.php";
include "../generate.php";

session_start();
debugModus();
if((!isset($_SESSION["semail"]))||($_SESSION["semail"]=="")){//Nicht angemeldet
	session_destroy();
	header("Location: http://".host.dirname(dirname($_SERVER["REQUEST_URI"]))."/Anmeldung.php");//Umleitung zur Anmeldung
}

include "Menu.php";
$menu = new Menu();
$menu->add(new Menupunkt("dashboard","Dashboard","dashboard.php"));
$menu->add(new Menupunkt("download","Download","download.php"));
$menu->add(new Menupunkt("upload","Upload","upload.php"));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php"));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php",true));
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Benutzerkonto</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/frontend.js"></script>
</head>
<body>
<?=generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden</a>");?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Benutzerkontenverwaltung</h1>
		<form method="POST" action="">
		<table>
			<tr><td>Nutername ändern:</td></tr>
			<tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
			<tr><td>Aktuelles Passwort:</td><td><input type="password" name="pwa" id="pwa"></td></tr>
			<tr><td>Neuer Nutzername:</td><td><input type="password" name="pwn" id="pwn"><td></tr>
			<tr><td colspan="2"><input type="submit" value="Nutzername ändern" name="namea"></td></tr>
		</table>
		</form>
		<br>
		<form method="POST" action="">
		<table>
			<tr><td>Email ändern:</td></tr>
		    <tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
		    <tr><td>Neue Email:</td><td><input type="password" name="pwa" id="pwa"></td></tr>
		    <tr><td colspan="2"><input type="submit" value="Email ändern" name="emaila"></td></tr>
		</table>
		</form>
		<br>
		<form method="POST" action="">
		<table>
			<tr><td>Passwort ändern:</td></tr>
			<tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
			<tr><td>Aktuelles Passowort:</td><td><input type="password" name="pwa" id="pwa"></tr></td>
			<tr><td>Neues Passwort:</td><td><input type="password" name="pwn" id="pwn"></td></tr>
			<tr><td>Passwort wiederholen:</td><td><input type="password" name="pwn2" id="pwn2"></td></tr> 
			<tr><td colspan="2"><input type="submit" value="Passwort ändern" name="passworta"></td></tr>
		</form>
		</table>		
	</div>
</body>
</html>