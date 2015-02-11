<?php
// Page de connexion
/*
	Pas de vérification
*/
require_once '../include/autoload.inc.php';

// S'il y a un utilisateur dans la session, on le supprime (déco)
CurrentUser::destroy();

// Si un formulaire de connexion a été envoyé, on tente de le connecter
if(!empty($_POST['login']) && !empty($_POST['password'])){
	// Si on y arrive, redirection vers newproject.php
	if(CurrentUser::connect('login', 'password')){
		header('location: ../pages/newproject.php');
		exit;
	}
	// Sinon, retour sur la page
		header('location: ../pages/connexion.php');
		exit;
}

// Construction de la page
$html = new Page('../html/design.html');
$html->setTitle("Connexion");
$html->setCSS(<<<CSS
	body{
		margin: 50px 0;
	}
CSS
);

$html->addJsLink('../js/sha1.js');
$html->setScript(<<<JS
	$(function(){
		// Si le cookie a rentré un nom automatiquement dans la textbox, on focus le champ de mot de passe, sinon celui du login
		if($.inArray(document.getElementById('form_login').value, ['', 'Login']) > -1) $('#form_login').focus();
		else $('#form_pass').focus();
		// Hashage du mot de passe avant l'envoi du formulaire
		$('#connexion').submit(function(){
			document.getElementById('form_password').value = SHA1(SHA1(document.getElementById('form_pass').value));
		});
	});
JS
);
$content = '';

// Controle et traitement
if(isset($_POST['submit'])){
	if(empty($_POST['form_login']) or empty($_POST['login-pass']))
	{
		//message d'erreur
	}
	else if (isset($_POST['newaccount'])) //clic sur le bouton créer un compte
	{
		$pseudo=($_POST['form_login']);
		$pass=($_POST['login-pass']);
		try {
			$newuser= Connexion::prepared(<<<SQL
				INSERT INTO user
				VALUES(:pseudo, :pass)
SQL
			, array(
				':pseudo' => $pseudo,
				':pass' => $pass,
			));
			//message ok
		}
		catch(PDOException $e)
		{
			//message erreur bdd
		}
	}
	else if (isset($_POST['login'])) //clic sur le bouton login
	{
		if(CurrentUser::connect('login', 'password')){
			header('location: ../pages/newproject.php');
		}
	}
}

// Formulaire de connexion
$cookie = CurrentUser::getCookie(); // Remplissage du champ de login avec la valeur trouvée dans le cookie s'il y en a
$cookie_login = $cookie ? 'value=\''.$cookie.'\' ' : '';

$content .=<<<HTML
	<div class="container">
		<div id='connexion' method='POST' action='../pages/connexion.php' class="login-form">
		<p>Vous devez vous connecter pour pouvoir utiliser l'application.<br />
		Veuillez entrer un login et un mot de passe.<br></p>
			<form id='connexion' method='POST' action='../pages/connexion.php'>
	            <div class="form-group">
	              <input type="text" id='form_login' name='login' placeholder='Login' {$cookie_login} class="form-control login-field" value="" placeholder="Login">
	              <label class="login-field-icon fui-user" for="login-name"></label>
	            </div>

	            <div class="form-group">
	              <input type='password' class="form-control login-field" value="" placeholder="Password" id="login-pass">
	              <label class="login-field-icon fui-lock" for="login-pass"></label>
	              <input type='hidden' id='form_password' name='password' />
	            </div>

	            <input id="login" name="login" type='submit' class="btn btn-primary btn-lg btn-block" value="Se connecter">
	            <input id="newaccount" name="newaccount" type='submit' class="btn btn-primary btn-lg btn-block" value="Créer un compte">					
			</form>
		</div>
	</div>
HTML;


$html->insertContent($content, "<section id='content'></section>");