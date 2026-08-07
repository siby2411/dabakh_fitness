<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();

// Requête : Chiffre d'affaires par produit vendu
$stats = $db->query("
    SELECT 
        l.designation, 
        SUM(l.quantite) as qte_vendue, 
        SUM(l.sous_total) as ca_total 
    FROM facture_lignes l
    JOIN factures f ON l.id_facture = f.id
    WHERE f.statut = 'payee'
    GROUP BY l.designation
")->fetchAll(PDO::FETCH_ASSOC);

$total_global = $db->query("SELECT SUM(total) FROM factures WHERE statut = 'payee'")->fetchColumn();
?>

<div class="container mt-5">
    <h2><i class="fas fa-chart-line"></i> État Financier - Boutique</h2>
    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr><th>Produit</th><th>Quantité vendue</th><th>CA Total</th></tr>
        </thead>
        <tbody>
            <?php foreach($stats as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['designation']) ?></td>
                <td><?= $s['qte_vendue'] ?></td>
                <td><?= number_format($s['ca_total'], 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="table-success">
                <th colspan="2">Chiffre d'Affaires Global</th>
                <th><?= number_format($total_global, 0, ',', ' ') ?> F</th>
            </tr>
        </tfoot>
    </table>
</div>
<?php include 'footer.php'; ?>
