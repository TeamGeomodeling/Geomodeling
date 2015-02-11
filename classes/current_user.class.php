<?php

/**
 * Représentation PHP de l'utilisateur courant, basée sur la classe BasicUser
 * Gère la session utilisateur
 * L'utilisateur est pioché dans la table 'user' de la base de données
 */
class CurrentUser extends BasicUser{
	public static $_user;	/// Objet User avec les informations de l'utilisateur
	
	/**
	 * Surcharge de la méthode BasicUser::setInfos()
	 * Répartit les informations entre les attributs depuis la base de données
	 */
	public static function setInfos($infos){
		self::$_user = new User($infos);
	}
	
	/**
	 * Ajoute un utilisateur dans la base de données
	 * @param id : identifiant de l'utilisateur à ajouter
	 * @param login : login de l'utilisateur
	 * @param pass : mot de passe de l'utilisateur, passé deux fois sous sha1 (== sha1(sha1($pass)))
	 */
	public static function add($id, $login, $pass){
		return Connexion::prepared(<<<SQL
			UPDATE user
			SET loginU = ?, passwordU = ?
			WHERE idU = ?
SQL
		, array($login, $pass, $id));
	}
	
	/**
	 * Retire un utilisateur de la base de données
	 * @param id : identifiant de l'utilisateur à enlever
	 */
	public static function remove($id){
		return Connexion::prepared(<<<SQL
			UPDATE user
			SET loginU = NULL, passwordU = NULL
			WHERE idU = ?
SQL
		, array($id));
	}
	
	/**
	 * Change le mot de passe d'un utilisateur
	 * @param id : identifiant de l'utilisateur dont on veut changer le mot de passe
	 * @param pass : le nouveau mot de passe de l'utilisateur, passé deux fois sous sha1 (== sha1(sha1($pass)))
	 */
	public static function changePassword($id, $pass){
		$res = Connexion::prepared(<<<SQL
			UPDATE user
			SET passwordU = ?
			WHERE idU = ?
SQL
		, array($pass, $id));
		if($res) $_SESSION[self::$_session][self::session_password] = $pass;
		return $res;
	}
}