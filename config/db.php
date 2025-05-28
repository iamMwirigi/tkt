<?php
class Database {

    private $host = "localhost"; 
    private $db_name = "tkt";
    private $username = "root"; 
    private $password = "31278527"; 
    private $charset = "utf8mb4";
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            // Instead of echoing and exiting here, re-throw the exception.
            // This allows the calling script (e.g., login.php) to catch it and handle it.
            throw $exception;
        }

        return $this->conn;
    }
}
?>