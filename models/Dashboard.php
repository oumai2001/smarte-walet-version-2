<?php
require_once __DIR__ . '/../config/Database.php';

class Dashboard
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    public function getStats($user_id)
    {
        $income = $this->conn->prepare(
            "SELECT SUM(amount) FROM incomes WHERE user_id = :id"
        );
        $expense = $this->conn->prepare(
            "SELECT SUM(amount) FROM expenses WHERE user_id = :id"
        );

        $income->execute([':id' => $user_id]);
        $expense->execute([':id' => $user_id]);

        $totalIncome = $income->fetchColumn() ?? 0;
        $totalExpense = $expense->fetchColumn() ?? 0;

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense
            
        ];
    }
}
