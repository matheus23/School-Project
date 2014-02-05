<?php
class Menupunkt{
	public $titel;
	public $seite;
	public $id;
	public $icon;
	public function __construct($id,$titel, $seite, $icon="none") {
		$this->titel = $titel;
		$this->seite = $seite;
		$this->id = $id;
		$this->icon = $icon;
	}
}
//Zum Bauen des Seitenmenüs
//Bespielverwendung:
//$menu = new Menu();
//$menu->add(new Menupunkt("test_ID","Test_Titel","test_pfad.php"));
//$menu->add(new Menupunkt("test_ID2","Test_Titel2","test_pfad2.php"));
//$menu->add(new Menupunkt("test_ID3","Test_Titel3","test_pfad3.php",true));<--Seite gerade aktiv (vom Nutzer ausgewählt)
//...
//$menu->toHTML() generiert den HTML-Text
class Menu {
	public $menupunkte;
	public $aktiverMenupunkt;
	public $pathZuStammordner;
	public function __construct($menupunkte=array(), $aktiverMenupunkt, $pathZuStammordner) {
		$this->menupunkte = $menupunkte;
		$this->aktiverMenupunkt = $aktiverMenupunkt;
		$this->pathZuStammordner = $pathZuStammordner;
	}
	public function add($menupunkt) {
		array_push($this->menupunkte,$menupunkt);
	}	
	public function toHTML() {
		$html="";
		foreach($this->menupunkte as $menupunkt){
			$class = "menupunkt";
			$class .=  ($menupunkt->id == $this->aktiverMenupunkt) ? " aktiv" : "";
			$path = $this->pathZuStammordner . $menupunkt->icon;
			$html.=
				"<div class='$class' id='$menupunkt->id'>".
					"<a href='$menupunkt->seite'>".
						"<span>".
							($menupunkt->icon == "none" ? "" : "<img class='menuIcon' src='$path' />").
							$menupunkt->titel.
						"</span>".
					"</a>".
				"</div>\n";
		}
		return $html;
	}
}
?>