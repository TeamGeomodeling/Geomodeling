<?php

// Désactive les erreurs (évite les avertissements de standards à chaque changement de version de php)
// error_reporting(0);


/**
 * Effectue un require_once sur un fichier situé dans un dossier 'classes' sous le format 'nom_de_classe.class.php'
 * Permet de donner des noms de fichiers plus lisibles, par exemple la classe XMLHttpRequest aura pour fichier xml_http_request.class.php
 * En cas de changement d'architecture (déplacement des classes php), il suffira de modifier $path
 */
function __autoload($class){
	$path = '../classes/'; // Dossier où sont situées les classes
	$extension = '.class.php'; // Extension du fichier
	
	// Si la classe est entièrement en majuscules, on va chercher directement le fichier avec le nom en minuscule
	if($class == strtoupper($class)){
		require_once $path.strtolower($class).$extension;
		return;
	}
	
	$class = strtolower($class[0]).substr($class, 1);
	
	$a = ord('a');
	$z = ord('z');
	$A = ord('A');
	$Z = ord('Z');
	$lastIndex = 0;
	for($i=1 ; $i<strlen($class) ; $i++){
		$ascii = ord($class[$i]);
		$lastAscii = ord($class[$i-1]);
		$nextAscii = (strlen($class)>$i+1) ? ord($class[$i+1]) : -1;
		if(($ascii >= $A && $ascii <= $Z)
		&& (($nextAscii >= $a && $nextAscii <= $z)
		|| ($nextAscii >= $A && $nextAscii <= $Z && $lastAscii >= $a && $lastAscii <= $z))){
			$path .= strtolower(substr($class, $lastIndex, $i-$lastIndex)).'_';
			$lastIndex = $i;
		}
	}
	$path .= strtolower(substr($class, $lastIndex)).$extension;
	require_once $path;
}

/**
 * Fonction appelée sur toutes les pages excepté la page accueil.php et la page projects.php
 * Tente de récupérer l'utilisateur depuis la session ; si ça ne marche pas, on retourne à la page d'accueil
 */
function session(){
	if(!isset($_SESSION))
		session_start();
		
	/*try{
		if(!CurrentUser::getFromSession()){
			header('location: ../pages/accueil.php');
			exit;
		}
	}
	catch(Exception $e){
		header('location: ../pages/accueil.php');
		exit;
	}*/
}

/**
 * Met la première lettre de la chaîne passée en paramètre en majuscule
 */
function to_maj($string){
	return strtoupper(substr($string, 0, 1)).substr($string, 1);
}

/**
 * Redirige vers la page d'accueil qui affichera un message d'accès non accordé à la page
 */

function no_access(){
	header('location: ../pages/accueil.php?redirect=permission');
	exit;
}