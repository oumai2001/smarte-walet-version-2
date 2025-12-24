<?php
require_once __DIR__ . '/../config/Database.php';

class Category
{
    private $conn;
    private $id;
    private $name;
    private $type; 

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    public function getAll($type = null)
    {
        if ($type) {
            $this->type = $type;

            $stmt = $this->conn->prepare(
                "SELECT * FROM categories WHERE type = :type"
            );
            $stmt->execute([':type' => $this->type]);
        } else {
            $stmt = $this->conn->query(
                "SELECT * FROM categories"
            );
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
