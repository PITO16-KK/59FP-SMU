<?php
class Database {
    private static $conn;

    private static function getEnv($key, $default = null) {
        $env = @parse_ini_file(__DIR__ . '/../.env');
        return $env[$key] ?? $default;
    }

    public static function getConnection() {
        if(!self::$conn){
            $host = self::getEnv('DB_HOST', 'localhost');
            $dbname = self::getEnv('DB_NAME', 'smu');
            $user = self::getEnv('DB_USER', 'root');
            $pass = self::getEnv('DB_PASS', '');

            try {
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Connection failed: ".$e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
