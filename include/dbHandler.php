<?php
require_once 'config.php';

class DBHandler {
    private static $conn; 
    
    private function __construct() {
    }

    public static function getPDO(){
        if(self::$conn == null){
            self::connect_database();
        }
        return self::$conn;        
    }

    private static function connect_database() {
        global $host, $db, $user, $password; 

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try {
            $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            self::$conn = new PDO($dsn, $user, $password, $options);
        }
        catch(PDOException $e) {
            error_log("Errore Connessione DB: " . $e->getMessage());
            die("Errore di connessione. Riprova più tardi.");
        }
    }   
}
?>
