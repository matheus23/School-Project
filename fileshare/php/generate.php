<?php

function generateBanner() {
	return "<i><h1 id='banner'><span id='bannerLeft'>Mango</span><span id='bannerRight'>share</span></h1></i>";
}

function generateHeader($content) {
	return ("<div id='header'>".$content."</div>");
}

function generateHeaderBanner() {
	return generateHeader(generateBanner());
}

function generateHeaderBannerLogout() {
	return generateHeader(generateBanner().generateLogout());
}

function generateLogout() {
	return 
		"<div id='abmelden'>".
			(isset($_SESSION["semail"]) ? "<span>Angemeldet als: ".$_SESSION["semail"]."</span>" : "").
			"<br/><a href='../abmelden.php' id='abmeldenLink'>abmelden <img src='../../img/door_in.png' /></a>".
		"</div>";
}
?>