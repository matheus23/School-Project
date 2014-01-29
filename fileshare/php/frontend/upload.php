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
$menu->add(new Menupunkt("upload","Upload","upload.php",true));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php"));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"));
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Upload</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
</head>
<body>
<?=generateHeader(generateBanner()."<a class='confirm' href='../abmelden.php' id='abmelden'>abmelden</a>");?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
		<div id="panel">
			<h1>Upload</h1>
			<div id="uiWrapper">
				<table width="100%">
					<tr><td>
						<!--
						<table align="left"  border="1">
							<tr><td><h3>Mit Gruppe teilen:</h3></td></tr>							
						</table>
						//-->
						<table align="center">
							<form method="post" enctype="multipart/form-data">
								<tr><td align="center"><h4>Datei für Upload auswählen<h4></td></tr>
								<tr><td align="center"><input type="file" name="file"></td></tr>
								<tr><td align="center"><br><input type="submit" value="Hochladen"></td></tr>
							</form>
							<tr><td><?php
								$kb = 1024;
								$mb = 1024*$kb;
								
						#	if ( ! empty ( $_FILES['file']['name'] ) ){
									#if (file_exists("/var/www/github-yannick/fileshare/uploads/" . $_FILES["file"]["name"])){
    								#	echo $_FILES["file"]["name"] . " already exists. ";
      								#}
    								#else{
   										move_uploaded_file($_FILES["file"]["tmp_name"],
    									"/var/www/github-yannick/fileshare/uploads/" . $_FILES["file"]["name"]);
    									echo "Gespeichert in: " . "/var/www/github-yannick/fileshare/uploads/" . $_FILES["file"]["name"];
    								#}
						#	}
								?>
							</td></tr>
						</table>
					</td></tr>
				</table>
			</div>
		</div>
	</div>
<script>
	$(".confirm").click(function() {
		var abmelden = confirm("Willst du dich wirklich abmelden?!?");
		if (!abmelden){
			return false;
		}
	});
</script>
</body>
</html>