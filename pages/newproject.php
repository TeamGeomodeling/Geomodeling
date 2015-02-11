<?php
// Page d'accueil
/*
	Seul les utilisateurs connectés ont accès a cette page
*/

require_once '../include/autoload.inc.php';
//require_once 'config_bd.ini.php';
//require_once '../classes/connexion.class.php';
//require_once '../classes/user.class.php';
//require_once '../classes/basic_user.class.php';
//require_once '../classes/current_user.class.php';
session();

$html = new Page('../html/project.html');
$content = '';
$user = CurrentUser::$_user;
$content = '';
$navbar='';
$html->setTitle("Nouveau Projet"); // si nameP vide alors "Nouveau Projet", sinon $nameP

// Contenu de la Navbar 
$navbar=<<<HTML
	<form role="search" class="navbar-form navbar-right">
        <div class="form-group">
          <input type="text" placeholder="Nom du projet" class="form-control">
          <input type="text" placeholder="Courte description" class="form-control">
        </div>
        <button class="btn btn-default" type="submit">Enregistrer</button>
	</form>
HTML;

// Toolbar de transformation
$TransformTB=<<<HTML
  <div id="TransformTool">
    <div class="container">

      <div id="posTool">
        <h6>Position</h6>
        <form class="form-inline" role="form">
            X
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Y
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Z
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
        </form>
      </div>

      <div id="rotTool">
        <h6>Rotation</h6>
        <form class="form-inline" role="form">
            X
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Y
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Z
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
        </form>
      </div>

      <div id="sizeTool">
        <h6>Taille</h6>
        <form class="form-inline" role="form">
            X
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Y
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
            
            Z
            <button type="button" class="btn btn-primary btn-xs"> - </button>
            <input class="form-control typeahead-only input-xs input-sm" type="text" style="position: relative; vertical-align: top; background-color: transparent;">
            <button type="button" class="btn btn-primary btn-xs"> + </button>
        </form>
      </div>

    </div>
  </div>

HTML;


$content .=$TransformTB;

$html->insertContent($content, "<section id='content'></section>");
$html->insertContent($navbar, "<div id'navbar-content'></div>");