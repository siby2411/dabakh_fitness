<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();
?>

<div class="container mt-4">
    <h2><i class="fas fa-chart-line"></i> Tableau de Bord - Statistiques Globales</h2>
    <hr>
    <?php
    try {
        $stmt = $db->query("SELECT d.nom AS discipline, COUNT(a.id) AS total_adherents, SUM(p.montant) AS ca 
                            FROM disciplines d 
                            LEFT JOIN adherents a ON d.id = a.discipline_id 
                            LEFT JOIN paiements p ON a.id = p.adherent_id 
                            GROUP BY d.id, d.nom");
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($stats)) {
            echo "<div class='alert alert-warning'>Aucune donnée statistique disponible pour le moment.</div>";
        } else {
            echo '<table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr><th>Discipline / Catégorie</th><th>Total Adhérents</th><th>Chiffre d\'Affaires</th></tr>
                    </thead>
                    <tbody>';
            foreach($stats as $row) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['discipline']) . "</td>
                        <td>" . $row['total_adherents'] . "</td>
                        <td>" . number_format($row['ca'] ?? 0, 0, ',', ' ') . " F</td>
                      </tr>";
            }
            echo '</tbody></table>';
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur de chargement des statistiques : " . $e->getMessage() . "</div>";
    }
    ?>
</div>

<?php include 'footer.php'; ?>
