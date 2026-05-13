<?php
class Database {
    private $db_path;
    public $conn;

    public function __construct() {
        $this->db_path = __DIR__ . '/../data/evaccination.sqlite';
        if (!is_dir(dirname($this->db_path))) {
            mkdir(dirname($this->db_path), 0755, true);
        }
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("sqlite:" . $this->db_path);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("PRAGMA foreign_keys = ON;");
            $this->conn->exec("PRAGMA journal_mode = WAL;");
        } catch (PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die("Database connection failed.");
        }
        return $this->conn;
    }
}
