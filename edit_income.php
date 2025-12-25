<?php
session_start();

require_once 'models/Income.php';
require_once 'models/Category.php';
require_once 'helpers/Validator.php';
require_once 'helpers/functions.php';

$page_title = 'Modifier Revenu';
$error = '';
$success = '';

// Vérifier l'authentification
require_auth();
$user_id = get_user_id();

$incomeObj = new Income();
$categoryObj = new Category();

// Récupérer l'ID du revenu
$income_id = $_GET['id'] ?? null;
if (!$income_id) {
    header('Location: incomes.php');
    exit;
}

// Récupérer le revenu
$income = $incomeObj->getById($income_id);
if (!$income) {
    header('Location: incomes.php');
    exit;
}

// Vérifier que le revenu appartient à l'utilisateur connecté
if ($income['user_id'] != $user_id) {
    header('Location: incomes.php');
    exit;
}

// Gestion de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $data = [
        'description' => $_POST['description'] ?? '',
        'amount' => $_POST['amount'] ?? 0,
        'date' => $_POST['income_date'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null
    ];

    if ($incomeObj->update($income_id, $data)) {
        $success = "Revenu modifié avec succès !";
        // Recharger les données
        $income = $incomeObj->getById($income_id);
    } else {
        $error = "Erreur lors de la modification";
    }
}

// Récupérer les catégories
$categories = $categoryObj->getAll('income');

include 'includes/header.php';
?>

<div class="content">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <a href="incomes.php" class="btn btn-small">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <h2><i class="fas fa-edit"></i> Modifier le Revenu</h2>
    </div>
    
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

    <form method="POST" style="background: var(--bg-main); padding: 2rem; border-radius: var(--radius); max-width: 600px;">
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" value="<?= htmlspecialchars($income['description']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Montant (DH)</label>
            <input type="number" step="0.01" name="amount" value="<?= $income['amount'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="income_date" value="<?= $income['income_date'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Catégorie</label>
            <select name="category_id">
                <option value="">-- Aucune --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $income['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" name="update" class="btn btn-success">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="incomes.php" class="btn">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>