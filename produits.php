<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['nom'])) {
    $stmt = $db->prepare("INSERT INTO produits (nom, prix, stock) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nom'], $_POST['prix'], $_POST['stock']]);
}

$produits = $db->query("SELECT * FROM produits ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Produits</h2>
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4"><input type="text" name="nom" class="form-control" placeholder="Nom produit" required></div>
        <div class="col-md-3"><input type="number" name="prix" class="form-control" placeholder="Prix" required></div>
        <div class="col-md-2"><input type="number" name="stock" class="form-control" placeholder="Stock"></div>
        <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Ajouter Produit</button></div>
    </form>

    <table class="table table-hover">
        <thead class="table-dark"><tr><th>Code</th><th>Nom</th><th>Prix</th><th>Stock</th></tr></thead>
        <tbody>
            <?php foreach($produits as $p): ?>
            <tr>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($p['code_produit']) ?></span></td>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= number_format($p['prix'], 0, ',', ' ') ?> F</td>
                <td><?= $p['stock'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
