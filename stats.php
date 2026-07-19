<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();
$type = $_GET['type'] ?? 'paiements_adherent';

$query = "";
if ($type == 'paiements_adherent') {
    $query = "SELECT a.nom, a.prenom, p.date_paiement, p.montant, p.statut FROM paiements p JOIN adherents a ON p.adherent_id = a.id";
} elseif ($type == 'paiements_discipline') {
    $query = "SELECT d.nom, SUM(p.montant) as total FROM paiements p JOIN adherents a ON p.adherent_id = a.id JOIN disciplines d ON a.discipline_id = d.id GROUP BY d.nom";
} elseif ($type == 'rentabilite') {
    $query = "SELECT d.nom, SUM(p.montant) as profit FROM paiements p JOIN adherents a ON p.adherent_id = a.id JOIN disciplines d ON a.discipline_id = d.id WHERE p.statut = 'valide' GROUP BY d.nom ORDER BY profit DESC";
}
$results = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-5 pt-5">
    <h3>Rapport : <?= htmlspecialchars(str_replace('_', ' ', $type)) ?></h3>
    <table class="table table-striped">
        <?php foreach ($results as $row): ?>
            <tr><?php foreach ($row as $col): ?><td><?= htmlspecialchars($col) ?></td><?php endforeach; ?></tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
