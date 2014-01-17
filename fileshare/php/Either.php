<?php

// Einfachste implementation von Either, die mir spontan einfällt.
/*
Das Konzept funktioniert folgendermaßen:

Wenn eine Funktion nicht in 100% der Fälle erfolgreich ist,
dann wird nicht (wie sonst üblich) eine Fehlernachricht zurückgegeben
oder nicht 'false' ODER das ergebnis returnt,
sondern der datentyp Either.

Either hat zwei Subklassen (bzw. Implementaitionen):
Left und Right

Eine Funktion, die möglicherweise einen Fehler retrunen kann,
returnt bei
	- Erfolg Left und bei
	- Fehler Right
Sowohl in der Left als auch in der Right Klasse wird eine Variable gespeichert.
Im häufigsten Fall ist das bei Left der datentyp der normalerweise bei 
Erfolg returnt werden würde, und bei Right ein String mit der Fehlernachricht.

Falls man nun z.B. ein $db->query("SELECT ..."); macht, kann man mithilfe von
anonymen Funktionen und der Funktion "fold" (aus Either) die Fehlerbehandlung
durchführen:

$nrt = new Nachrichten(...);
$db->query("SELECT ...")->fold(
	// Right<mysql_result>:
	function($ergebnis) use (&$nrt) {
		$nrt->okay("Das ergebnis war " . var_dump($ergebnis) . ".");
	}, 
	// Left<string>:
	function($fehlernachricht) use (&$nrt) {
		$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlernachricht");
	});

Das "use ($variable)" bei Funktionen funktioniert wie das "global $variable;" 
mit globalen Variablen.
Hat man keine normale Variable, sondern eine Klasse, sollte man 
"use (&$klassenVariable)" verwenden.
*/
interface Either {
	// If Either is of type Either<A, B> then
	// $ifLeft is of type (A) -> C and
	// $ifRight is of type (B) -> C
	public function fold($ifLeft, $ifRight);
}

class Left implements Either {

	private $value;
	
	public function __construct($value) {
		$this->value = $value;
	}
	
	public function fold($ifLeft, $ifRight) {
		return $ifLeft($this->value);
	}
}

class Right implements Either {

	private $value;
	
	public function __construct($value) {
		$this->value = $value;
	}
	
	public function fold($ifLeft, $ifRight) {
		return $ifRight($this->value);
	}
}
?>