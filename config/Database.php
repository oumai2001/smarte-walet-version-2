<?php

class Database
{
    private $host = "localhost";
    private $db_name = "smarte_walet";
    private $username = "root";
    private $password = "";

    private $conn = null;

    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                    $this->username,
                    $this->password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("Erreur connexion DB : " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
