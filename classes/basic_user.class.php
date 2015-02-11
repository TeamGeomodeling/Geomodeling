<?php

////////////////////////////////////// double sha1 pour le mdp

// Configuration de la classe
BasicUser::setParams(
	'user',		    // Table dans la BD
	'idU',			// Colonne de l'id de l'utilisateur dans la BD
	'loginU',		// Colonne du login de l'utilisateur dans la BD
	'passwordU',	// Colonne du mot de passe de l'utilisateur dans la BD
	'user',			// Nom de la clé dans la $_SESSION pour l'utilisateur
	'user_login'	// Nom du cookie contenant le login de l'utilisateur
);

/**
 * Classe basique d'utilisateur
 * Facilite la création d'une session utilisateur et sa gestion
 * Il suffit de faire une classe fille pour avoir une version personnalisée en fonction du besoin ; si on veut ajouter des attributs
 *   et leur donner une valeur à la connexion, il suffira de surcharger la méthode setInfos()
 * 
 * /!\ Le mot de passe provenant du formulaire de connexion doit correspondre à celui dans la base de données, il faut donc que le formulaire utilise un
 *   algorithme de hachage en js (=> www.movable-type.co.uk) pour sécuriser la connexion
 *
 * Exemple :
 * class User{
 *   private static $_age;
 *   private static $_nom;
 *   
 *   function setInfos($infos){
 *     self::$_age = 18;
 *     self::$_nom = $infos['nom'];
 *   }
 * }
 */
class BasicUser{
	public static $_id = null; /// Id de l'utilisateur
	public static $_login = null; /// Login de l'utilisateur
	public static $_password = null; /// Mot de passe de l'utilisateur
	
	protected static $_userTable = null; /// Nom de la table des données de l'utilisateur dans la base de données
	protected static $_idColumn = null; /// Nom de la colonne avec l'identifiant de l'utilisateur dans la base de données
	protected static $_loginColumn = null; /// Nom de la colonne avec le login de l'utilisateur dans la base de données
	protected static $_passwordColumn = null; /// Nom de la colonne avec le mot de passe de l'utilisateur dans la base de données
	
	protected static $_cookie = null; /// Nom du cookie dans lequel stocker le login de l'utilisateur
	
	protected static $_session = null; /// Nom de la colonne dans la session dans laquelle sont stockées les données de l'utilisateur
	const session_id = 'id'; /// $_SESSION[self::$_session][self::session_id] == id de l'utilisateur courant
	const session_password = 'pass'; /// $_SESSION[self::$_session][self::session_password] == mot de passe (crypté) de l'utilisateur courant
	
	/**
	 * Connecte l'utilisateur à l'aide des informations reçues via un formulaire
	 * /!\ Le mot de passe provenant du formulaire doit correspondre à celui dans la base de données, il faut donc que le formulaire utilise un
	 *     algorithme de hachage en js (=> http://www.movable-type.co.uk) pour sécuriser la connexion
	 * @param post_login : nom du champ du formulaire où on peut trouver le login de l'utilisateur
	 * @param post_password : nom du champ du formulaire où on peut trouver le mot de passe (après cryptage en js) de l'utilisateur
	 * @return true si tout est ok, faux sinon (si on n'a pas trouvé d'utilisateur)
	 */
	public static function connect($post_login, $post_password){
		$infos = self::get($_POST[$post_login], $_POST[$post_password]); // recup des informations sur l'utilisateur
		if(!$infos) return false; // si on n'a pas trouvé d'utilisateur
		
		self::setBasicInfos($infos); // Remplissage des infos basiques (id, login et mot de passe)
		static::setInfos($infos); // Remplissage des infos complémentaires, utilisées par la classe fille
		
		// Répartition des informations importantes pour maintenir la session (id et mot de passe)
		if(!isset($_SESSION))
			session_start();
		$_SESSION[self::$_session] = array(
			self::session_id => self::$_id,
			self::session_password => self::$_password
		);
		
		setcookie(self::$_cookie, self::$_login); // on place maintenant un cookie avec le login de l'utilisateur
		
		return true;
	}
	
	/**
	 * Déconnecte l'utilisateur
	 */
	public static function destroy(){
		if(!isset($_SESSION))
			session_start();
		if(!empty($_SESSION[self::$_session]))
			unset($_SESSION[self::$_session]);
	}
	
	/**
	 * Récupère les informations disponibles sur l'utilisateur dans la base de données
	 * Peut être redéfinie dans la classe fille, dans ce cas penser à redéfinir également setBasicInfos(), qui utilise ce résultat
	 * Ici, le résultat correspondra à toutes les colonnes de la table définie dans les paramètres de la classe (setParams)
	 * @return un tableau associatif avec les valeurs du résultat de la requête
	 */
	protected static function get($id_login, $pass){
		$table = self::$_userTable;
		$id_login_col = (intval($id_login) != 0) ? self::$_idColumn : self::$_loginColumn;
		$pass_col = self::$_passwordColumn;
		return Connexion::prepared(<<<SQL
			SELECT *
			FROM {$table}
			WHERE {$id_login_col} = :id_login
			AND {$pass_col} = :pass
SQL
		, array(
			':id_login' => $id_login,
			':pass' => $pass
		))->first();
	}
	
	/**
	 * Récupère les informations dans la session pour vérifier l'utilisateur puis le connecter
	 * @return true si l'utilisateur est valide, false sinon
	 */
	public static function getFromSession(){
		if(!isset($_SESSION)
		|| empty($_SESSION[self::$_session])
		|| empty($_SESSION[self::$_session][self::session_id])
		|| empty($_SESSION[self::$_session][self::session_password]))
			return false;
		
		$infos = self::get($_SESSION[self::$_session][self::session_id], $_SESSION[self::$_session][self::session_password]); // recup des informations sur l'utilisateur
		if(!$infos) return false; // si on n'a pas trouvé d'utilisateur
		static::setBasicInfos($infos); // Remplissage des infos basiques (id, login et mot de passe)
		static::setInfos($infos); // Remplissage des infos complémentaires, utilisées par la classe fille
		return true;
	}
	
	/**
	 * Répartit les informations dans le tableau passé en paramètre dans les attributs de classe _id, _login et _password
	 * @param infos : tableau des informations sur l'utilisateur tiré d'une requête sur la base de données
	 */
	protected static function setBasicInfos($infos){
		self::$_id = $infos[self::$_idColumn];
		self::$_login = $infos[self::$_loginColumn];
		self::$_password = $infos[self::$_passwordColumn];
	}
	
	/**
	 * Définit les informations qu'on voudra rajouter dans la classe fille
	 * Méthode à redéfinir dans la classe fille si on veut ajouter des informations autres que l'id, login et mot de passe
	 * @param infos : tableau d'informations résultant de la méthode get()
	 */
	protected static function setInfos($infos) { return true; }
	
	/**
	 * Donne les valeurs voulues aux attributs dont la classe à besoin pour fonctionner
	 * @param userTable : nouvelle valeur de _userTable
	 * @param idColumn : nouvelle valeur de _idColumn
	 * @param loginColumn : nouvelle valeur de _loginColumn
	 * @param passwordColumn : nouvelle valeur de _passwordColumn
	 * @param session : nouvelle valeur de _session
	 * @param cookie : nouvelle valeur de _cookie
	 */
	public static function setParams($userTable, $idColumn, $loginColumn, $passwordColumn, $session, $cookie){
		self::$_userTable = $userTable;
		self::$_idColumn = $idColumn;
		self::$_loginColumn = $loginColumn;
		self::$_passwordColumn = $passwordColumn;
		self::$_session = $session;
		self::$_cookie = $cookie;
	}
	
	/**
	 * Renvoie le login enregistré dans le cookie, ou false s'il n'y a pas de cookie
	 */
	public static function getCookie(){
		return (empty($_COOKIE[self::$_cookie])) ? false : $_COOKIE[self::$_cookie];
	}
}