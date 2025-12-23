<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Validator.php';

class Income
{
    private $conn;
    private $amount;
    private $description;
    private $date;
    private $category_id;
    private $user_id;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    // Create income
    public function create($data)
    {
        if (
            !Validator::amount($data['amount']) ||
            !Validator::date($data['date'])
        ) {
            return false;
        }

        $sql = "INSERT INTO incomes
                (description, amount, income_date, category_id, user_id)
                VALUES (:description, :amount, :date, :category, :user)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':description' => Validator::clean($data['description']),
            ':amount'      => $data['amount'],
            ':date'        => $data['date'],
            ':category'    => $data['category_id'],
            ':user'        => $data['user_id']
        ]);
    }

    // Get all incomes by user
    public function getAll($user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM incomes WHERE user_id = :id"
        );
        $stmt->execute([':id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get income by id
    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM incomes WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get incomes by category
    public function getByCategory($category_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM incomes WHERE category_id = :cat"
        );
        $stmt->execute([':cat' => $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update income
    public function update($id, $data)
    {
        if (!Validator::amount($data['amount'])) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "UPDATE incomes
             SET description = :d,
                 amount = :a,
                 income_date = :date,
                 category_id = :c
             WHERE id = :id"
        );

        return $stmt->execute([
            ':d'    => Validator::clean($data['description']),
            ':a'    => $data['amount'],
            ':date' => $data['date'],
            ':c'    => $data['category_id'],
            ':id'   => $id
        ]);
    }

    // Delete income
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM incomes WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
