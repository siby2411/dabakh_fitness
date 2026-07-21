<?php
include 'header.php';
$db = (new Database())->getConnection();
$factures = $db->query("SELECT * FROM factures ORDER BY date_facture DESC")->fetchAll();
?>

<div class="container">
    <h2>Historique des Factures</h2>
    <table class="table">
        <thead class="table-dark">
            <tr><th>Numéro</th><th>Date</th><th>Statut</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach($factures as $f): ?>
            <tr>
                <td><?= $f['numero'] ?></td>
                <td><?= $f['date_facture'] ?></td>
                <td><span class="badge bg-info"><?= $f['statut'] ?></span></td>
                <td><?= number_format($f['total'], 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
