<?php
class Database {

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $charset = "utf8mb4";
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        // Read credentials from environment variables
        $this->host = getenv('DB_HOST') ?: 'localhost'; // Default to localhost if not set
        $this->port = getenv('DB_PORT') ?: '3306';     // Default MySQL/MariaDB port
        $this->db_name = getenv('DB_NAME');             // It's better if these are always explicitly set
        $this->username = getenv('DB_USER');           // No defaults for user/pass is safer
        $this->password = getenv('DB_PASS');

        $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
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