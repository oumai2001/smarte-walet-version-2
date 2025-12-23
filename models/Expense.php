<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Validator.php';

class Expense
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


    public function create($data)
    {
        if (
            !Validator::amount($data['amount']) ||
            !Validator::date($data['date'])
        ) {
            return false;
        }

        $sql = "INSERT INTO expenses
                (description, amount, expense_date, category_id, user_id)
                VALUES (:description, :amount, :date, :category, :user)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':description' => Validator::clean($data['description']),
            ':amount' => $data['amount'],
            ':date' => $data['date'],
            ':category' => $data['category_id'],
            ':user' => $data['user_id']
        ]);
    }

  
    public function getAll($user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expenses WHERE user_id = :id"
        );
        $stmt->execute([':id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expenses WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function getByCategory($category_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expensse WHERE category_id = :cat"
        );
        $stmt->execute([':cat' => $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function update($id, $data)
    {
        if (!Validator::amount($data['amount'])) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "UPDATE expenses
             SET description = :d,
                 amount = :a,
                 expense_date = :date,
                 category_id = :c
             WHERE id = :id"
        );

        return $stmt->execute([
            ':d' => Validator::clean($data['description']),
            ':a' => $data['amount'],
            ':date' => $data['date'],
            ':c' => $data['category_id'],
            ':id' => $id
        ]);
    }

   
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM expenses WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
