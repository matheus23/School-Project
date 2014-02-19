<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
require_once(rootdir."fileshare/php/generate.php");
require_once(rootdir."fileshare/php/frontend/frontendUtilities.php"); // Definiert auch $frontendMenu
require_once(rootdir."fileshare/php/frontend/Menu.php");

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
	<script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
    <script src="../../js/jsUtilities.js"></script>
</head>
<body>
<?=generateHeaderBannerLogout();?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Dashboard</h1>
		<div id="kompatibilitaet" class="boxMitRand">
			<p>Browser-Status:</p>
			<ul>
			</ul>
		</div>
	</div>
</div>
<script src="../../js/dashboard.js"></script>
</body>
</html>