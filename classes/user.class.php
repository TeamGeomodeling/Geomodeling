<?php

/**
 * Représentation PHP d'un utilisateur
 */
class User{
	public $_id;				    /// Identifiant de l'utilisateur dans la bd
	public $_pseudo;				/// Pseudo de l'utilisateur
	public $_projects = array();    /// Identifiants des projets que l'utilisateur a créé; un tableau d'objets de type projet est récupérable via la méthode projects()

	// Caches
	private $_cache_projets = array();	    /// Résultat de projets()
	
	/**
	 * Constructeur de la classe
	 * @param donnees : tableau associatif, avec pour clés les clés qu'aurait le résultat
	 *                  de la requête 'SELECT * FROM user WHERE ...'
	 */
	public function __construct($donnees){
		$this->_id = htmlentities($donnees['idU']);
		$this->_pseudo = htmlentities(to_maj($donnees['loginU']));
		$this->_projects = Connexion::prepared(<<<SQL
			SELECT p.id
			FROM project p, user u
			WHERE p.creatorP=u.id
			AND u.id = ?
			ORDER BY p.id
SQL
		, array($donnees['id']))->each(function($res){
			return array(
				'id' => htmlentities($res['id']),
			);
		});
	}
	
	/**
	 * Récupère et renvoie un utilisateur depuis la base de données à partir de son identifiant
	 * @param identifiant : l'id de l'utilisateur
	 * @return l'utilisateur recherché, ou false si la requête n'a renvoyé aucun résultat
	 */
	public static function recupDepuisBD($id){
		return new User(Connexion::prepared(<<<SQL
			SELECT idU, loginU
			FROM user
			WHERE idU = ?
SQL
		, array($id)));
	}
	
	/**
	 * Ajoute users à la base de données
	 * @param props : tableau associatif avec pour clés les colonnes à remplir 
	 * @return le résultat de la requête
	 */
	public static function bd_ajouter($props) {
		$res = Connexion::prepared(<<<SQL
			INSERT INTO user (loginU)
			VALUES (:loginU)
SQL
			,array(
				':loginU' => $props['loginU']
			)
		);
	}
	
	/**
	 * Retourne un tableau d'objets Projets créés par l'utilisateur
	 */
	public function projects(){
		if(!$this->_cache_projects){
			$this->_cache_projects = Connexion::prepared(<<<SQL
				SELECT idP
				FROM project
				WHERE creatorP = ?
				ORDER BY idP DESC
SQL
			, array($this->_id))->each(function($res){
				return Project::recupDepuisBD($res['id']);
			});
		}
		return $this->_cache_fiches;
	}
}