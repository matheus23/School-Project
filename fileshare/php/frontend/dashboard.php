<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
include_once "../generate.php";
include_once "frontendUtilities.php"; // Definiert auch $frontendMenu
include_once "Menu.php";

session_start();
leiteUmWennNichtAngemeldet();
$menu = new Menu($frontendMenu, "dashboard", "../../");
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/frontend.js"></script>
</head>
<body>
<?=generateHeaderBannerLogout();?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Dashboard</h1>
	</div>
</div>
</body>
</html>