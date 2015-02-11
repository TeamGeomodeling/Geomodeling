<?php

Levenshtein::setParams(
	'project',	// Table de la colonne à examiner
	'nameP',    // Colonne à examiner
	4			// Distance de Levenshtein (=> http://fr.wikipedia.org/wiki/Distance_de_Levenshtein) maximale
);

/**
 * Permet d'avoir des propositions de résultat sur un moteur de recherche, par exemple si l'utilisateur a fait une faute de frappe
 * Utile ici pour rechercher des projets par nom
 */
class Levenshtein{
	private static $_table = null; /// Table dans la base de donnée dans laquelle on a accès à la colonne _nameColumn
	private static $_nameColumn = null; /// Colonne dans la base de données contenant les noms sur lesquels sera effectuée la recherche
	
	private static $_precision = null; /// Nombre de lettres max de différence entre le mot entré et les mots auxquels il est comparé
	private static $_results = null; /// Tableau des résultats de l'algorithme de Levenshtein, avec les entrées associées
	
	/**
	 * Retourne le résultat le plus proche de la chaine passée en paramètre
	 * @param string : la chaine à comparer
	 * @return la chaine la plus proche, false s'il n'y en a pas d'assez précisément proche (à comprendre avec une distance de Levenshtein inférieure à _precision par rapport au paramètre)
	 */
	public static function nearest($string){
		self::setResults($string);
		$res = '';
		$res_lev = 255;
		foreach(self::$_results as $entry){
			if($entry['levenshtein'] < $res_lev){
				$res = $entry[self::$_nameColumn];
				$res_lev = $entry['levenshtein'];
			}
		}
		return ($res !== '' && $res_lev <= self::$_precision) ? $res : false;
	}
	
	/**
	 * Retourne un tableau avec les entrées ayant au plus _precision de distance de levenshtein avec la chaine passé en paramètre
	 * @param string : la chaine à comparer
	 * @return un tableau avec les entrées correspondantes
	 */
	public static function getArray($string){
		self::setResults($string);
		$res = array();
		foreach(self::$_results as $entry){
			if($entry['levenshtein'] <= self::$_precision){
				$res[] = $entry[self::$_nameColumn];
			}
		}
		return $res;
	}
	
	/**
	 * Configure les attributs statiques de la classe
	 * @param table : nouvelle valeur de _table
	 * @param nameColumn : nouvelle valeur de _nameColumn
	 * @param precision : nouvelle valeur de _precision
	 */
	public static function setParams($table, $nameColumn, $precision){
		self::$_table = $table;
		self::$_nameColumn = $nameColumn;
		self::$_precision = $precision;
	}
	
	/**
	 * Définit l'attribut _results
	 * @param string : chaine à comparer avec ceux de la base de données
	 */
	private static function setResults($string){
		self::$_results = array();
		$table = self::$_table;
		$nom_col = self::$_nameColumn;
		$req = Connexion::query(<<<SQL
			SELECT {$nom_col}
			FROM {$table}
SQL
		);
		while($ligne = $req->fetch()){
			self::$_results[] = $ligne;
		}
		for($i=0 ; $i<sizeof(self::$_results) ; $i++){
			self::$_results[$i]['levenshtein'] = levenshtein($string, self::$_results[$i][$nom_col]);
		}
	}
}