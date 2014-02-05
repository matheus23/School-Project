<?php

function generateBanner() {
	return "<i><h1 id='banner'>Mangoshare</h1></i>";
}

function generateHeader($content) {
	return ("<div id='header'>".$content."</div>");
}

function generateHeaderBanner() {
	return generateHeader(generateBanner());
}

function generateHeaderBannerLogout() {
	return generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden</a>");
}
?>