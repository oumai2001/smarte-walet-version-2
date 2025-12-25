<?php
session_start();

require_once 'models/Income.php';
require_once 'models/Category.php';
require_once 'helpers/Validator.php';
require_once 'helpers/functions.php';

$page_title = 'Revenus';
$error = '';
$success = '';
//__call overrid ktktab fo9 mno polymorphisme fih overrid ou overlod method kdir tretment deferent 3la 7savb objet li 3titha li kykon mhiriti mn classe li dart fih dik method

// Vérifier l'authentification
require_auth();
$user_id = get_user_id();

$incomeObj = new Income();
$categoryObj = new Category();

// Gestion de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $data = [
        'description' => $_POST['description'] ?? '',
        'amount' => $_POST['amount'] ?? 0,
        'date' => $_POST['income_date'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
        'user_id' => $user_id
    ];

    if ($incomeObj->create($data)) {
        $success = "Revenu ajouté avec succès !";
    } else {
        $error = "Erreur lors de l'ajout (vérifiez le montant et la date)";
    }
}

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($incomeObj->delete($id)) {
        $success = "Revenu supprimé avec succès !";
    } else {
        $error = "Erreur lors de la suppression";
    }
}

// Filtres
$category_filter = $_GET['category'] ?? null;
$month_filter = $_GET['month'] ?? null;

// Récupérer les revenus
$incomes = $incomeObj->getAll($user_id);
if ($category_filter) {
    $incomes = array_filter($incomes, fn($i) => $i['category_id'] == $category_filter);
}
if ($month_filter) {
    $incomes = array_filter($incomes, fn($i) => substr($i['income_date'], 0, 7) === $month_filter);
}

// Récupérer les catégories de type 'income'
$categories = $categoryObj->getAll('income');

include 'includes/header.php';
?>

<div class="content">
    <h2><i class="fas fa-arrow-up"></i> Gestion des Revenus</h2>
    
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
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-plus-circle"></i> Ajouter un Revenu</h3>
        
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
            <input type="date" name="income_date" value="<?= date('Y-m-d') ?>" required>
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
            <a href="incomes.php" class="btn btn-small">
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
            <?php if (empty($incomes)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                        Aucun revenu enregistré
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($incomes as $income): ?>
                    <tr>
                        <td><?= htmlspecialchars($income['description']) ?></td>
                        <td>
                            <?php
                            $cat_name = '-';
                            if ($income['category_id']) {
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $income['category_id']) {
                                        $cat_name = $cat['name'];
                                        break;
                                    }
                                }
                            }
                            echo htmlspecialchars($cat_name);
                            ?>
                        </td>
                        <td style="color: var(--success); font-weight: bold;">
                            <?= format_amount($income['amount']) ?>
                        </td>
                        <td><?= format_date($income['income_date']) ?></td>
                        <td>
                            <a href="edit_income.php?id=<?= $income['id'] ?>" class="btn btn-small">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="?delete=<?= $income['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('Confirmer la suppression ?')">
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