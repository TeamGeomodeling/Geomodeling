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
---------------------------------------------------- Pagination ---------------------------------------------------
-----------------------------------------------------------------------------------------------------------------*/
	$pagination=<<<HTML
		<div class="pagination">
            <ul>
              <li class="previous"><a href="#fakelink" class="fui-arrow-left"></a></li>
              <li class=""><a href="#fakelink">1</a></li>
              <li><a href="#fakelink">2</a></li>
              <li><a href="#fakelink">3</a></li>
              <li><a href="#fakelink">4</a></li>
              <li><a href="#fakelink">5</a></li>
              <li><a href="#fakelink">6</a></li>
              <li><a href="#fakelink">7</a></li>
              <li><a href="#fakelink">8</a></li>
              <li><a href="#fakelink">9</a></li>
              <li><a href="#fakelink">10</a></li>
              <li class="pagination-dropdown dropup active">
                <a href="#fakelink" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fui-triangle-up"></i>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="#fakelink">10–20</a></li>
                  <li><a href="#fakelink">20–30</a></li>
                  <li><a href="#fakelink">40–50</a></li>
                </ul>
              </li>
              <li class="next"><a href="#fakelink" class="fui-arrow-right"></a></li>
            </ul>
          </div>
HTML;
	$html->insertContent($pagination, "</section><footer>");


/*------------------------------------------------------------------------------------------------------------------
------------------------------------------- Contenu de la page Projects --------------------------------------------
------------------------------------------------------------------------------------------------------------------*/
	$content .=<<<HTML
	
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
HTML;

$html->setTitle("Projects");
$html->insertContent($content, "<section id='content'></section>");