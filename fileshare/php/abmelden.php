<?php
	include "../php/utilities.php";
	session_start();
	session_destroy();
	header("Location: http://".host.dirname($_SERVER["REQUEST_URI"])."/Anmeldung.php");
?>