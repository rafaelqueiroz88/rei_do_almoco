<?php

namespace App\Config {

    use PDO;
    use PDOException;

    class Database{
               
        private static $host        = "localhost";
        private static $db_name     = "rei_almoco";
        private static $username    = "root";
        private static $password    = "";
        public static $conn;

        public static function GetConnection() {

            self::$conn = null;

            try {

                self::$conn = new PDO( 
                    "mysql:host=" . self::$host . 
                    ";dbname=" . self::$db_name, self::$username, self::$password
                );
                self::$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            }
            catch( PDOException $exception ) {
                die( "Falha detectada: " . $exception->getMessage() );
            }
            
            return Database::$conn;
        }
    }
}