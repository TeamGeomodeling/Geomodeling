<?php

if(!isset($config_added))	require_once '../config_bd.ini.php';

// Configuration du singleton
Connexion::setParams(
	BD_NAME,		// BD
	BD_LOGIN,		// Utilisateur
	BD_PASSWORD,	// Mot de passe
	BD_DRIVER,		// Driver
	BD_HOST			// Hôte
);

/**
 * Singleton facilitant l'interaction avec une ou plusieurs bases de données en utilisant PDO
 */
class Connexion{
	private static $_singleton = null;			/// Singleton
	private static $_driver = null;				/// Driver (mysql ou oci)
	private static $_host = null;				/// Hôte, vaut null si le driver est oci (inutile)
	private static $_database = null;			/// Base de donnée ciblée
	private static $_user = null;				/// Utilisateur connecté
	private static $_password = null;			/// Mot de passe de l'utilisateur
	private static $_encrypt_function = null;	/// Fonction à utiliser pour le cryptage d'entrées dans la bd
	private $_pdo;								/// Objet de connexion PDO
	
	/**
	 * Constructeur de la classe, appelé par la méthode getInstance
	 */
	private function __construct(){
		// On vérifie d'abord que tous les paramètres d'instanciation sont utilisables
		if((self::$_driver == null && self::$_host == null)
		|| self::$_driver == null
		|| self::$_database == null
		|| self::$_user === null
		|| self::$_password === null)
			throw new Exception("Construction impossible : des paramètres de connexion sont absents");
		// Instanciation de l'objet PDO
		$this->_pdo = new PDO($this->getDSN(), self::$_user, self::$_password);
		
		// Mise en place du mode "Exception" pour les erreurs PDO
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
		// Cas particulier de MySQL
        switch ($this->_pdo->getAttribute(PDO::ATTR_DRIVER_NAME)){
            case 'mysql' :
                // Pour que cela fonctionne sur MySQL...
                $this->_pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                // Passage du client MySQL en utf-8
                $this->_pdo->query("SET CHARACTER SET 'utf8'");
				break;
            case 'oci' :
                // Tris selon la langue française
                $this->_pdo->query("ALTER SESSION SET NLS_SORT = FRENCH");
            break;
        }
	}
	
	/**
	 * Destructeur de la classe, déconnexion et remise à null du singleton
	 */
	public function __destruct(){
		if (!is_null($this->_pdo)){
			$this->_pdo = null;
			self::$_singleton = null;
		}
	}
	
	/**
	 * Retourne le DSN (Data Source Name)
	 * @return le DSN
	 */
	private function getDSN(){
		return (self::$_driver == 'mysql') ? 'mysql:host='.self::$_host.';dbname='.self::$_database : 'oci:dbname='.self::$_database;
	}
	
	/**
	 * Définit les valeurs des attributs statiques
	 * @param $database la base de données ciblée
	 * @param $user l'utilisateur connecté
	 * @param $password le mot de passe de l'utilisateur
	 * @param $driver le driver (mysql ou oci), oci par défaut
	 * @param $host l'hôte dans le cas de mysql, null par défaut (inutile dans le cas d'oci)
	 * @param $encrypt_function la fonction à utiliser pour crypter chaque valeur quand on utilise secure()
	 * @param $encrypt_function la fonction à utiliser pour décrypter chaque valeur quand on utilise secure()
	 */
	public static function setParams($database, $user, $password, $driver='oci', $host=null, $encrypt_function=null, $decrypt_function=null){
		self::$_driver = $driver;
		self::$_host = $host;
		self::$_database = $database;
		self::$_user = $user;
		self::$_password = $password;
		self::$_encrypt_function = $encrypt_function;
		SuperStatement::setDecryptFunction($decrypt_function);
	}
	
	/**
	 * Modifie la base de données ciblée par les prochaines requêtes
	 * @param la nouvelle base ciblée
	 */
	public static function setDatabase($database){
		self::$_database = $database;
		self::$_singleton = null;
	}
	
	/**
	 * Récupère le singleton et instancie l'objet PDO via le constructeur
	 * @return le singleton
	 */
	public static function getInstance(){
		if(self::$_singleton == null){
			self::$_singleton = new Connexion();
		}
		return self::$_singleton;
	}
	
	/**
	 * Facilite l'utilisation d'un PreparedStatement - utile pour une requête à sécuriser pour l'exécuter directement
	 * Prépare une requête et l'exécute directement avec les paramètres passés en paramètre
	 * @param $request l'instruction de la requête préparée
	 * @param $params le tableau des valeurs à donner pour la requête
	 */
	public function prepared($request, $params=array()){
		$stmt = self::prepare($request);
		$stmt->execute($params);
		return $stmt;
	}
	
	/**
	 * Crypte les données à passer à la requête SI ce sont des Strings ; les données qui n'en sont pas ne seront pas cryptées
	 */
	public function secure($request, $params=array()){
		$stmt = self::prepare($request);
		$crypted_params = array();
		foreach($params as $key => $val){
			if(is_string($val))
				$crypted_params[$key] = call_user_func(self::$_encrypt_function, $val);
			else
				$crypted_params[$key] = $val;
		}
		$stmt->execute($crypted_params);
		$stmt->setDecrypt(true);
		return $stmt;
	}
	
	/**
	 * Simplifie l'appel d'une méthode (évite d'avoir à utiliser la méthode getInstance)
	 * @param $method la méthode appelée
	 * @param $arguments le tableau des arguments
	 * @return ce que la méthode devrait retourner
	 */
	public static function __callStatic($method, $arguments){
		$singleton = self::getInstance();
		return call_user_func_array(array($singleton, $method), $arguments);
	}
	
	/**
	 * Permet d'appeller les méthodes de PDO
	 * Si le résultat est un PDOStatement, on le retourne encapsulé dans un SuperStatement
	 * @param $method la méthode appelée
	 * @param $arguments le tableau des arguments
	 * @return le résultat de la méthode appelée, ou un SuperStatement si un PDOStatement devait être retourné
	 */
	public function __call($method, $arguments){
		$res = call_user_func_array(array($this->_pdo, $method), $arguments);
		return ($res instanceof PDOStatement) ? new SuperStatement($res) : $res;
	}
}

/**
 * Encapsulation de l'objet PDOStatement
 * Permet d'ajouter des méthodes utiles
 */
class SuperStatement{
	private $_statement;						/// L'objet encapsulé
	private $_decrypt = false;					/// Booléen à true s'il faut décrypter le statement
	private static $_decrypt_function = null;	/// Fonction à utiliser pour le décryptage de sorties depuis la bd
	
	/**
	 * Constructeur
	 */
	public function __construct($stmt){
		$this->_statement = $stmt;
	}
	
	/**
	 * Définit la fonction à utiliser pour décrypter une valeur provenant de la base de données
	 */
	public static function setDecryptFunction($func){
		self::$_decrypt_function = $func;
	}
	
	/**
	 * Définit _decrypt
	 */
	public function setDecrypt($decrypt){
		$this->_decrypt = $decrypt;
	}
	
	/**
	 * Décrypte un tableau issu d'un statement passé au fetch
	 */
	public static function decrypt($fetched){
		$res = array();
		foreach($fetched as $key => $val){
			if(!is_numeric($val))
				$res[$key] = call_user_func(self::$_decrypt_function, $val);
			else
				$res[$key] = $val;
		}
		return $res;
	}
	
	/**
	 * Simplifie le traitement de chaque ligne du statement
	 * @param to_do($line, $index) : fonction à exécuter pour chaque ligne du statement
	 *   - line : ligne du statement passée sous fetch()
	 *   - index : index de la ligne dans l'ensemble des résultats
	 * @param if_empty : fonction ) exécuter si la requête ne retourne aucun résultat
	 * @param fetch_assoc : indique si on veut un tableau uniquement associatif ; si à false, la valeur de PDO par défaut est utilisée, cad PDO::FETCH_BOTH
	 * @return un tableau avec les return de tous les appels de to_do s'il a pu être appelé, le résultat de if_empty sinon, false s'il n'existe pas
	 */
	public function each($to_do, $if_empty=null, $fetch_assoc=true){
		$i = 0;
		$res = array();
		$fetch_mode = ($fetch_assoc) ? PDO::FETCH_ASSOC : null;
		while($line = $this->_statement->fetch($fetch_mode)){
			if($this->_decrypt) $line = self::decrypt($line);
			$res[] = $to_do($line, $i);
			$i++;
		}
		if($i > 0) return $res;
		if($if_empty) return $if_empty();
		return false;
	}
	
	/**
	 * S'occupe de la première ligne de résultat du statement
	 * @param to_do : fonction à exécuter une fois la ligne du statement passée sous fetch()
	 * @param if_empty : fonction ) exécuter si la requête ne retourne aucun résultat
	 * @param fetch_assoc : indique si on veut un tableau uniquement associatif ; si à false, la valeur de PDO par défaut est utilisée, cad PDO::FETCH_BOTH
	 * @return selon les paramètres :
	 *   - le résultat de to_do s'il est fourni et que la requête a donné un résultat
	 *   - la ligne passée sous fetch() si to_do n'est pas fourni et que la requête a donné un résultat
	 *   - si la requête n'aboutit pas à un résultat et que if_empty est défini, le résultat de if_empty
	 *   - false sinon
	 */
	public function first($to_do=null, $if_empty=null, $fetch_assoc=true){
		$fetch_mode = ($fetch_assoc) ? PDO::FETCH_ASSOC : null;
		if($line = $this->_statement->fetch($fetch_mode)){
			if($this->_decrypt) $line = self::decrypt($line);
			if($to_do) return $to_do($line);
			else return $line;
		}
		if($if_empty) return $if_empty();
		return false;
	}
	
	/**
	 * S'occupe de la dernière ligne de résultat du statement
	 * @param to_do : fonction à exécuter une fois la ligne du statement passée sous fetch()
	 * @param if_empty : fonction ) exécuter si la requête ne retourne aucun résultat
	 * @param fetch_assoc : indique si on veut un tableau uniquement associatif ; si à false, la valeur de PDO par défaut est utilisée, cad PDO::FETCH_BOTH
	 * @return selon les paramètres :
	 *   - le résultat de to_do s'il est fourni et que la requête a donné un résultat
	 *   - la ligne passée sous fetch() si to_do n'est pas fourni et que la requête a donné un résultat
	 *   - si la requête n'aboutit pas à un résultat et que if_empty est défini, le résultat de if_empty
	 *   - false sinon
	 */
	public function last($to_do=null, $if_empty=null, $fetch_assoc=true){
		$res = null;
		$fetch_mode = ($fetch_assoc) ? PDO::FETCH_ASSOC : null;
		while($line = $this->_statement->fetch($fetch_mode))
			$res = $line;
		if($res){
			if($this->_decrypt) $res = self::decrypt($res);
			if($to_do) return $to_do($res);
			else return $res;
		}
		if($if_empty) return $if_empty();
		return false;
	}
	
	/**
	 * Permet d'utiliser les méthodes de l'objet PDOStatement
	 * @param $method la méthode appelée
	 * @param $arguments le tableau des arguments
	 * @return le résultat de la méthode appelée
	 */
	public function __call($method, $arguments){
		return call_user_func_array(array($this->_statement, $method), $arguments);
	}
}

// ************************************************************************ //
/**
Exemple d'utilisation :

require_once 'connexion.class.php';
Connexion::setParams('db_name', 'user', 'password', 'mysql', 'localhost');

$requete = Connexion::query(<<<SQL
	SELECT *
	FROM table
SQL
)->each(function($res){
	// traitement
});

---

$stmt = Connexion::prepared(<<<SQL
	SELECT *
	FROM table
	WHERE id = ?
SQL
, array(4));

while($result = $requete->fetch()){
	// traitement
}
||
$stmt->each(function($line, $index){
	// traitement
});

*/


//////////////////////////


/////////////////