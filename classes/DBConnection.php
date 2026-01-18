<?php
if (!defined('DB_SERVER')) {
    require_once("../initialize.php");
}

class DBConnection {

    private $host = 'localhost';
    private $username = 'u782741555_pupbinanonline';
    private $password = 'Elibomrats1@';
    private $database = 'u782741555_pupbinanonline';

    public $conn;

    public function __construct() {
        if (!isset($this->conn)) {

            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_error) {
                die('Database connection failed: ' . $this->conn->connect_error);
            }
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
