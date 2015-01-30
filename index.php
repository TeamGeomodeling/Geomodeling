<?php
// Vérifie s'il y a une session ouverte ; si oui, renvoie vers myprojects.php, sinon vers accueil.php
//require_once 'include/autoload.inc.php';
require_once 'config_bd.ini.php';
require_once 'classes/connexion.class.php';
require_once 'classes/user.class.php';
require_once 'classes/basic_user.class.php';
require_once 'classes/current_user.class.php';

session_start();
try{
	if(CurrentUser::getFromSession()){
		header('location: pages/myprojects.php');
		exit;
	}
	header('location: pages/accueil.php');
	exit;
}
catch(Exception $e){
	header('location: pages/accueil.php');
	exit;
}