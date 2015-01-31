<?php
// Page d'accueil
/*
	Tout le monde a accès à la page
*/
require_once '../include/autoload.inc.php';
//require_once 'config_bd.ini.php';
require_once '../classes/connexion.class.php';
require_once '../classes/user.class.php';
require_once '../classes/basic_user.class.php';
require_once '../classes/current_user.class.php';
session();
$html = new Page('../html/design.html');
$content = '';
$user = CurrentUser::$_user;
$hello_user='';

//===== Gestion du formulaire de mot de passe
$html->addJsLink('../js/sha1.js');
/*$html->setScript(<<<JS
	$(function(){
		$('#change_pass').submit(function(){
			var verif = (
				document.getElementById('form_old_pass').value != '' &&
				document.getElementById('form_new_pass').value != '' &&
				document.getElementById('form_confirm_pass').value != '' &&
				document.getElementById('form_new_pass').value == document.getElementById('form_confirm_pass').value
			);
			
			if(verif){
				document.getElementById('form_old_pass').value = SHA1(SHA1(document.getElementById('form_old_pass').value));
				document.getElementById('form_new_pass').value = SHA1(SHA1(document.getElementById('form_new_pass').value));
				document.getElementById('form_confirm_pass').value = SHA1(SHA1(document.getElementById('form_confirm_pass').value));
				return true;
			}
			else{
				document.getElementById('form_old_pass').value = '';
				document.getElementById('form_new_pass').value = '';
				document.getElementById('form_confirm_pass').value = '';
				$('#changer_mdp_erreur').text("Tous les champs ne sont pas remplis, ou vous n'avez pas entré deux fois le même mot de passe");
				return false;
			}
		});
	});
JS
);*/

//===== Gestion de la redirection vers cette page
$redirect_message = '';
if(!empty($_GET['redirect'])){
	if($_GET['redirect'] == 'permission') $redirect_message = "<span style='color:red'>Vous avez été reconduit à cette page car l'accès à la page demandée vous a été refusée.</span><br />";
}

//===== Contenu
/*if(acces(array(EVALUATEUR, CHEF_DE_SERVICE, DIRECTEUR))){
	// Liste des personnes à évaluer pour la journée
	$a_evaluer = Connexion::prepared(<<<SQL
		SELECT DISTINCT salarie, id "fiche"
		FROM fiche
		WHERE DATE_FORMAT(date_entretien, '%d/%m/%Y') = :date
		AND evaluateur = :id
		AND dernier_entretien IS NULL
SQL
		, array(':date' => date('d/m/Y'), ':id' => $user->_id)));

	$liste_a_evaluer = '';
	if($a_evaluer && sizeof($a_evaluer) > 0){
		$liste_a_evaluer = '<ul>';
		foreach($a_evaluer as $line){
			$pers = $line['personne'];
			$liste_a_evaluer .=<<<HTML
			<li>
				<a href='../pages/employes.php?id={$pers->_id}'>{$pers->_prenom} {$pers->_nom}</a>
				- <a class='begin' href='../pages/eae.php?id={$line['fiche']}'>Commencer l'évaluation</a>
				- <a class='begin change_date' href='#{$pers->_id}'>Reporter l'entretien</a>
			</li>
HTML;
		}
		$liste_a_evaluer .= '</ul>';
	}
	else
		$liste_a_evaluer = 'Personne à évaluer aujourd\'hui';*/
	
/*------------------------------------------------------------------------------------------------------------------
----------------------------------------------- Contenu de la Navbar -----------------------------------------------
------------------------------------------------------------------------------------------------------------------*/
	//Si User non connecté, propose de Sign In (et redirige vers myprojects.php) ou Register (et redirige vers register.php)
	$navbar_login=<<<HTML
		<form class="navbar-form navbar-right" role="form">
			<div class="form-group">
				<input type="text" placeholder="Pseudo" class="form-control">
			</div>
			<div class="form-group">
				<input type="password" placeholder="Password" class="form-control">
			</div>
			<button type="submit" class="btn btn-primary">Sign in</button>
			<button type="submit" class="btn btn-primary">Register</button>
		</form>
HTML;

	$navbar_connected=<<<HTML
		<div id="bs-example-navbar-collapse-14" class="collapse navbar-collapse">
	    	<p class="navbar-text navbar-right">Signed in as <a class="navbar-link" href="#">{$hello_user}</a></p>
	    </div>
HTML;

	if(CurrentUser::getFromSession()){
		$hello_user=$_user->_pseudo;
		$html->insertContent($navbar_connected, "<div id'navbar-content'></div>");
	}
	$html->insertContent($navbar_login, "<div id'navbar-content'></div>");

/*-----------------------------------------------------------------------------------------------------------------
------------------------------------------ Exemples de projets publiques ------------------------------------------
-----------------------------------------------------------------------------------------------------------------*/
	$projects=<<<HTML
		<div class="col-sm-6 col-md-4">
		  <div class="thumbnail">
		    <img data-src="holder.js/100%x200" alt="100%x200" src="" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
		    <div class="caption">
		      <h3>Thumbnail label</h3>
		      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
		      <p><a role="button" class="btn btn-primary" href="#">Button</a> <a role="button" class="btn btn-default" href="#">Button</a></p>
		    </div>
		  </div>
		</div>
HTML;



/*------------------------------------------------------------------------------------------------------------------
------------------------------------------- Contenu de la page d'accueil -------------------------------------------
------------------------------------------------------------------------------------------------------------------*/
	$content .=<<<HTML
	<div class="geo-header">
		<div class="geologo container">
			<div class="geo-headline">
				<h1 class="geo-logo">
					<div class="logo"></div>
					Geomodeling<br>
					<small>Une application web de dessin 2D/3D</small>
				</h1>
			</div> 
			<div class="row geo-row">
				<div class="col-xs-12 col-md-4"></div>
				<div class="col-xs-12 col-md-4">
					<a href="connexion.php" class="btn btn-block btn-hg btn-primary btnsolo"> Go! </a>
				</div>
				<div class="col-xs-12 col-md-4"></div>
			</div>
		</div>
	</div>

	<div class="projects">
		<div class="container geo-projects">
			<div class="geo-projects">
				<h3>Quelques projets réalisés avec Geomodeling</h3>
				      
	      <div class="row">
	        <div class="col-xs-12 col-md-4">
	          <a class="thumbnail" href="#">
	            <img src="../../img/formes3.png" alt="..."/>
	          </a>
	          <p>Un projet créé avec Geomodeling, avec des cubes, des spheres, et plein d'autres polygones réguliers.
	        </div>
	        <div class="col-xs-12 col-md-4">
	          <a class="thumbnail" href="#">
	            <img src="../../img/formes3.png" alt="..."/>
	          </a>
	          <p>Un projet créé avec Geomodeling, avec des cubes, des spheres, et plein d'autres polygones réguliers.
	        </div>
	        <div class="col-xs-12 col-md-4">
	          <a class="thumbnail" href="#">
	            <img src="../../img/formes3.png" alt="..."/>
	          </a>
	          <p>Un projet créé avec Geomodeling, avec des cubes, des spheres, et plein d'autres polygones réguliers.
	        </div>
	      </div>

	      <div class="row">
	        <div class="col-xs-12 col-md-4"></div>
	        <div class="col-xs-12 col-md-4">
	          <a href="recentsprojects.php" class="btn btn-block btn-hg btn-primary btnsolo"> En voir plus! </a>
	        </div>
	        <div class="col-xs-12 col-md-4"></div>
	      </div>		
		</div>
	</div>
	</div>

	<div class="presentation">
		<div class="container">
			<div class="geo-pres">
				<div class="row">
			        <div class="col-xs-12 col-md-4" id="project-geo">
			          <h5>Le projet</h5>
			          <p><div class="row">
				          	<div class="col-xs-12 col-md-4"></div>
				          	<div class="col-xs-12 col-md-4"><img src="../../img/virus.png" alt="..."/></div>
				          	<div class="col-xs-12 col-md-4"></div>
			          </div></p>
			          <p><b>Geomodeling</b> est une application web de dessin 3D assisté par ordinateur, utilisant WebGL et THREE.js.
			          <p>Créé dans le cadre du projet de L.P. Web et Mobile de l'INSSET de Saint-Quentin (02), promo 2014 - 2015.</p>
			        	
			        </div>
			        <div class="col-xs-12 col-md-4">
			          <h5>L'INSSET</h5>
			          <p><a class="thumbnail" href="http://www.insset.u-picardie.fr/">
			            <img src="../../img/insset.jpg" alt="...">
			          </p></a>
			          <p><b>L'INSSET</b> est une composante de L'Université de Picardie Jules Verne, proposant un catalogue de formations autour des métiers du web, des systèmes embarqués, de la logistique ainsi que de la conception et simulation de produits.
			          <p>À Saint-Quentin en Picardie, l'INSSET propose des formations depuis la Licence (post-bac) jusqu'au Master (bac+5), réalisables en formation initiale ou bien en alternance.
			        </div>
			        <div class="col-xs-12 col-md-4">
			          <h5>L'équipe</h5>
			          <p><b>Clothilde "AlyxAleone" Haristouy</b><br>
			          Née en 1991. Titulaire d'un diplome d'analyste programmeur à l'Exia.Cesi de Reims, elle aime les jolis sites web, le thé, Android, sa guitare, et les TCG.
			          
			          <p><b>Nicolas "Brainiac" Naskret</b><br>
			          Né en 1993. Actuellement en alternance à France 3 Picardie, à Amiens. Quand il ne développe pas, il aime défier Clothilde sur Hearthstone.
			        </div>
		        </div>
			</div>
		</div>
	</div>

HTML;


$html->setTitle("Home");
$html->insertContent($content, "<section id='content'></section>");