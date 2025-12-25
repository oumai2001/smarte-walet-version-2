<?php
session_start();

require_once 'models/Expense.php';
require_once 'models/Category.php';
require_once 'helpers/Validator.php';
require_once 'helpers/functions.php';

$page_title = 'Dépenses';
$error = '';
$success = '';

// Vérifier l'authentification
require_auth();
$user_id = get_user_id();

$expenseObj = new Expense();
$categoryObj = new Category();

// Gestion de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $data = [
        'description' => $_POST['description'] ?? '',
        'amount' => $_POST['amount'] ?? 0,
        'date' => $_POST['expense_date'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
        'user_id' => $user_id
    ];

    if ($expenseObj->create($data)) {
        $success = "Dépense ajoutée avec succès !";
    } else {
        $error = "Erreur lors de l'ajout (vérifiez le montant et la date)";
    }
}

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($expenseObj->delete($id)) {
        $success = "Dépense supprimée avec succès !";
    } else {
        $error = "Erreur lors de la suppression";
    }
}

// Filtres
$category_filter = $_GET['category'] ?? null;
$month_filter = $_GET['month'] ?? null;

// Récupérer les dépenses
$expenses = $expenseObj->getAll($user_id);
if ($category_filter) {
    $expenses = array_filter($expenses, fn($e) => $e['category_id'] == $category_filter);
}
if ($month_filter) {
    $expenses = array_filter($expenses, fn($e) => substr($e['expense_date'], 0, 7) === $month_filter);
}

// Récupérer les catégories de type 'expense'
$categories = $categoryObj->getAll('expense');

include 'includes/header.php';
?>

<div class="content">
    <h2><i class="fas fa-arrow-down"></i> Gestion des Dépenses</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-plus-circle"></i> Ajouter une Dépense</h3>
        
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" required>
        </div>
        
        <div class="form-group">
            <label>Montant (DH)</label>
            <input type="number" step="0.01" name="amount" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Catégorie</label>
            <select name="category_id">
                <option value="">-- Aucune --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" name="add" class="btn btn-success">
            <i class="fas fa-save"></i> Ajouter
        </button>
    </form>

    <!-- Filtres -->
    <form method="GET" class="filters">
        <select name="category" onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="month" name="month" value="<?= $month_filter ?? '' ?>" onchange="this.form.submit()">
        
        <?php if ($category_filter || $month_filter): ?>
            <a href="expenses.php" class="btn btn-small">
                <i class="fas fa-redo"></i> Réinitialiser
            </a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expenses)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        Aucune dépense enregistrée
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['description']) ?></td>
                        <td>
                            <?php
                            $cat_name = '-';
                            if ($expense['category_id']) {
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $expense['category_id']) {
                                        $cat_name = $cat['name'];
                                        break;
                                    }
                                }
                            }
                            echo htmlspecialchars($cat_name);
                            ?>
                        </td>
                        <td style="color: var(--danger); font-weight: bold;">
                            <?= format_amount($expense['amount']) ?>
                        </td>
                        <td><?= format_date($expense['expense_date']) ?></td>
                        <td>
                            <a href="edit_expense.php?id=<?= $expense['id'] ?>" class="btn btn-small">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="?delete=<?= $expense['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('Confirmer la suppression ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>