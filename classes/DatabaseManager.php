<?php
/**
 * Created by PhpStorm.
 * User: Melle Dijkstra
 * Date: 21-4-2015
 * Time: 14:52
 * @author Melle Dijkstra
 */

class DatabaseManager {

    // The singleton instance
	private static $instance = null;

    private $db;

    /**
     * Get the singleton instance of the DatabaseManager
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param null $driver_options
     * @return DatabaseManager
     * @throws Exception if method fails it throws an Exception();
     */
    public static function Instance($dsn = "mysql:host=localhost;dbname=cheery", $username = "cheery_mod", $password = DB_PASS, $driver_options = null) {
		if (self::$instance == null) {
            try{
                self::$instance = new DatabaseManager($dsn, $username, $password, $driver_options);
            } catch(Exception $e) {
                throw new Exception($e->getMessage(),$e->getCode());
            }
		}
		return self::$instance;
	}

	/**
	 * Destroy the instance
	 */
	public static function DestroyInstance() {
		if (self::$instance != null) {
			unset(self::$instance);
			self::$instance = null;
		}
	}

	/**
	 * Creates a PDO instance.
	 * @param string $dsn The DSN, (mysql:host=HOST;dbname=DB)
	 * @param string|bool $username The user name for the DSN string. This parameter is optional for some PDO drivers.
	 * @param string|bool $password The password for the DSN string. This parameter is optional for some PDO drivers.
	 * @param array|bool $driver_options A key=>value array of driver-specific connection options
	 */
	private function __construct($dsn, $username, $password, $driver_options) {
		if(!$this->db) {
			try {
				if(is_array($driver_options)) {
					$this->db = new PDO($dsn, $username, $password, $driver_options);
				} else {
					$this->db = new PDO($dsn, $username, $password);
				}
				$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				die("PDO Connection ERROR: " . $e->getMessage() . "<br/>");
			}
		}
	}

	/**
	 * Gets the database object from this class
	 * @return PDO
	 */
	public function getDB()
	{
		return $this->db;
	}

	/**
	 * Get all the records of a table
	 * @param string $table The table name
	 * @param string|int $order The column by which it has to be ordered
	 * @return array|bool The array with all the records or false on failure
	 */
    protected function getEverything($table,$order = 1) {
        if($stmt = $this->db->prepare('SELECT * FROM '.($table).' ORDER BY :order')) {
			if($stmt->execute(["order"=>$order])) {
				return $stmt->fetchAll();
			} else {
				return false;
			}
        } else {
            return false;
        }
    }

}