<?php
class Database {
    private $host = "localhost";
    private $db_name = "prayer_tracker";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to ensure proper handling of special characters
            $this->conn->set_charset("utf8");
            
            return $this->conn;
        } catch(Exception $e) {
            // Rethrow the exception instead of echoing
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }
}
?> 