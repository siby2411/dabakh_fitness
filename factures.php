<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();

// Récupération des produits avec stock pour le formulaire
$produits = $db->query("SELECT * FROM produits WHERE stock > 0")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Logique d'insertion facture (simplifiée)
    $stmt = $db->prepare("INSERT INTO factures (numero, date_facture, statut, total) VALUES (?, NOW(), 'payee', ?)");
    $stmt->execute([$_POST['num_facture'], $_POST['total']]);
    echo "<div class='alert alert-success'>Facture enregistrée et stock mis à jour.</div>";
}
?>

<div class="container">
    <h2>Nouvelle Facture</h2>
    <form method="POST">
        <input type="text" name="num_facture" class="form-control mb-3" placeholder="Numéro Facture" required>
        <select name="produit_id" class="form-select mb-3">
            <?php foreach($produits as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['nom'] ?> (Stock: <?= $p['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="total" class="form-control mb-3" placeholder="Montant Total">
        <button type="submit" class="btn btn-success">Valider Facture</button>
    </form>
</div>
<?php include 'footer.php'; ?>
