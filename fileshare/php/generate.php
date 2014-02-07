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
	return generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden <img src='../../img/door_in.png' /></a>");
}
?>