<?php

/*
	Permet de construire une page à partir d'un design prédéfini dans un HTML.
	Le code obtenu subira sera écrit automatiquement, pas besoin de s'en soucier.
	Nécessite les marqueurs <!--<<CSS>>-->, <!--<<SCRIPT>>--> et <!--<<META>>--> si besoin
	Exemple d'utilisation :
		// Minimum
			$html = new Page('design/page.html');
			$html->insertContent("Coucou !", "<div id='contenu'></div>");
			$html->setTitle("Accueil");
		// Eventuellement
			$html->setCSS("#contenu{color:red;}");
			$html->addCSSLink("design/general.css");
			$html->setScript("document.getElementById('contenu').innerHTML = 'Hey !';");
			$html->addJsLink("design/ajax.js");
			$html->setRobot(false, false);
*/

class Page{
	private $_html; /// code de la page
	const NOM_SITE = "Geomodeling"; /// Nom du site - sera ajouté au titre de chaque page par défaut
	
	////////////////////////////////// METHODES DE BASE //////////////////////////////////
	
	/*
	 * Constructeur de la classe
	 * à instancier avec l'url de l'HTML du design de la page
	 */
	public function __construct($url){
		$this->_html = file_get_contents($url);
	}
	
	/*
	 * Destructeur de la classe
	 * Ecrit le code de la page
	 */
	public function __destruct(){
		// Suppression de la marque CSS du code
		if(preg_match("<!--<<CSS>>-->", $this->_html)){
			$this->_html = preg_replace("#<!--<<CSS>>-->#U", "", $this->_html);
		}
		// Suppression de la marque Javascript du code
		if(preg_match("<!--<<SCRIPT>>-->", $this->_html)){
			$this->_html = preg_replace("#<!--<<SCRIPT>>-->#U", "", $this->_html);
		}
		// Suppression de la marque meta du code
		if(preg_match("<!--<<META>>-->", $this->_html)){
			$this->_html = preg_replace("#<!--<<META>>-->#U", "", $this->_html);
		}
		// Ecriture du code source
		echo $this->_html;
	}
	
	/*
	 * Getter magique
	 */
	public function __get($attr){
		return $this->$attr;
	}
	
	/*
	 * Setter magique
	 */
	public function __set($attr, $val){
		$this->$attr = $val;
	}
	
	////////////////////////////////// METHODES DE LA CLASSE //////////////////////////////////
	
	/*
	 * Insère le contenu dans le code
	 * Si $repeat vaut true, le code s'exécutera à chaque occurence de $destination dans le code, sinon à la première
	 * Exemple d'utilisation : $page->insertContent($contenu, "<div id='contenu'></div>");
	 * /!\ Il ne doit rien y avoir entre les balises ouvrante et fermante, pas même un \n
	 */
	public function insertContent($code, $destination, $repeat=false){
		// On récupère la balise ouvrante et la balise fermante
		$balises = explode("><", $destination);
		$balises[0] .= ">";
		$balises[1] = "<".$balises[1];
		
		// On prépare le format REGEX et on ajoute l'option U si $repeat est par défaut
		$destination = "#".$destination."#";
		if(!$repeat){
			$destination .= "U";
		}
		
		// On modifie la cible et on renvoie
		$this->_html = preg_replace($destination, $balises[0].$code.$balises[1], $this->_html);
	}
	
	/*
	 * Change le titre de la page
	 * Si $nomSite vaut true, on ajoute le nom du site à la fin
	 */
	public function setTitle($title, $nomSite=true){
		if($nomSite){
			$title .= " - ".self::NOM_SITE;
		}
		$this->insertContent($title, "<title></title>");
	}
	
	/*
	 * Ajoute un script CSS dans le code source
	 * Nécessite "<!--<<CSS>>-->" dans le fichier HTML du design
	 */
	public function setCSS($css){
		$css =<<<CSS
	<style type='text/css'>
		{$css}
	</style>
CSS;
		$this->_html = preg_replace("#<!--<<CSS>>-->#U", "<!--<<CSS>>-->".$css, $this->_html);
	}
	
	/*
	 * Ajoute le lien d'un script CSS dans le code source
	 * Nécessite "<!--<<CSS>>-->" dans le fichier HTML du design
	 */
	public function addCSSLink($url){
		$url =<<<HTML
	<link rel='stylesheet' href='{$url}'>
HTML;
		$this->_html = preg_replace("#<!--<<CSS>>-->#U", $url."\n<!--<<CSS>>-->", $this->_html);
	}
	
	/*
	 * Ajoute un script Javascript dans le code source
	 * Nécessite "<!--<<SCRIPT>>-->" dans le fichier HTML du design
	 */
	public function setScript($js){
		$js =<<<JS
	<script language='javascript'>
		{$js}
	</script>
JS;
		$this->_html = preg_replace("#<!--<<SCRIPT>>-->#U", "<!--<<SCRIPT>>-->".$js, $this->_html);
	}
	
	/*
	 * Ajoute le lien d'un script Javascript dans le code source
	 * Nécessite "<!--<<SCRIPT>>-->" dans le fichier HTML du design
	 */
	public function addJsLink($url){
		$url =<<<HTML
	<script language='javascript' src='{$url}'></script>
HTML;
		$this->_html = preg_replace("#<!--<<SCRIPT>>-->#U", $url."\n<!--<<SCRIPT>>-->", $this->_html);
	}
	
	/*
	 * Ajout de la meta robot
	 * Nécessite "<!--<<META>>-->" dans le fichier HTML du design
	 */
	public function setRobot($index=true, $follow=true){
		$str =<<<HTML
	<meta name='robots' content='
HTML;
		$str .= ($index) ? 'index' : 'noindex';
		$str .= ", ";
		$str .= ($follow) ? 'follow' : 'nofollow';
		$str .= "' />";
		$this->_html = preg_replace("#<!--<<META>>-->#U", $str."\n<!--<<META>>-->", $this->_html);
	}
}