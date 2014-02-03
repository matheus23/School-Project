<?php
class Menupunkt{
	public $titel;
	public $seite;
	public $id;
	public function __construct($id,$titel, $seite) {
		$this->titel = $titel;
		$this->seite = $seite;
		$this->id = $id;
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
	public function __construct($menupunkte=array(), $aktiverMenupunkt) {
		$this->menupunkte = $menupunkte;
		$this->aktiverMenupunkt = $aktiverMenupunkt;
	}
	public function add($menupunkt) {
		array_push($this->menupunkte,$menupunkt);
	}	
	public function toHTML() {
		$html="";
		foreach($this->menupunkte as $menupunkt){
			$class = "menupunkt";
			$class .=  ($menupunkt->id == $this->aktiverMenupunkt) ? " aktiv" : "";
			$html.="<div class='$class' id='$menupunkt->id'><span>$menupunkt->titel</span><a href='$menupunkt->seite'></a></div>\n";
		}
		return $html;
	}
}
?>