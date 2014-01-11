<?php
//Funktion, zum Senden der E-mail
//Diese Funktion ist ohne Wirkung, solange in der php.ini kein SMTP-Server angegeben ist.
//Um E-Mail-Diesnste (gmail,yahoo,...) zu nutzen, sind scheinbar immer zusätzliche programme oder Module für php notwendig,
//die nicht ins repository passen.
function schickeRegistrierungsEmail($user,$email,$nutzerID){
	$server = $_SERVER["HTTP_HOST"];
	$betreff = "Registrierung abschließen";
	$message =
		"Hallo $user,\n".
		"um deine Registrierung abzuschließen öffne folgenden Link:\n".
		"$server/?nutzerID=$nutzerID";
	mail($email,$betreff,$message);	
}
?>