<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

$db = null;
try {
    $db = (new Database())->getConnection();
} catch (Exception $e) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div></div>");
}

// Récupération des avis avec les informations de l'adhérent (si disponible)
$avis_list = [];
$db_error = '';

try {
    $query = "SELECT a.*, 
                     COALESCE(CONCAT(adh.nom, ' ', adh.prenom), 'Anonyme / Non lié') AS adherent_nom 
              FROM avis_adherents a 
              LEFT JOIN adherents adh ON a.numero_licence = adh.numero_licence 
              ORDER BY a.id DESC";
              
    $stmt = $db->query($query);
    $avis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la jointure échoue (ex: structure différente), on essaie de récupérer juste la table des avis
    try {
        $stmt = $db->query("SELECT * FROM avis_adherents ORDER BY id DESC");
        $avis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        $db_error = $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Avis Dabakh Fitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-header { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-danger"><i class="fas fa-clipboard-list"></i> Administration - Avis des Adhérents</h2>
                <p class="text-muted">Consultez les retours enregistrés pour Dabakh Fitness</p>
            </div>
            <div>
                <a href="donner_avis.php" class="btn btn-outline-danger" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le formulaire</a>
            </div>
        </div>

        <?php if (!empty($db_error)): ?>
            <div class="alert alert-danger shadow-sm">
                <strong>Erreur SQL :</strong> <?= htmlspecialchars($db_error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-comments"></i> Liste des avis reçus (<?= count($avis_list) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($avis_list)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted fs-5">Aucun avis n'a encore été enregistré dans la base de données.</p>
                        <a href="donner_avis.php" class="btn btn-danger btn-sm mt-2">Soumettre un premier avis</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#ID</th>
                                    <th>Date</th>
                                    <th>Adhérent / Profil</th>
                                    <th>Note</th>
                                    <th>Salle</th>
                                    <th>Accueil</th>
                                    <th>Améliorations / Coaching</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($avis_list as $avis): ?>
                                    <tr>
                                        <td class="fw-bold">#<?= $avis['id'] ?></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($avis['date_creation'] ?? 'N/A') ?></small></td>
                                        <td>
                                            <?php if (!empty($avis['is_anonymous']) && $avis['is_anonymous'] == 1): ?>
                                                <span class="badge bg-secondary">Anonyme</span>
                                            <?php else: ?>
                                                <span class="fw-semibold text-dark">
                                                    <?= htmlspecialchars($avis['adherent_nom'] ?? ($avis['numero_licence'] ?? 'Anonyme')) ?>
                                                </span>
                                                <?php if (!empty($avis['numero_licence'])): ?>
                                                    <br><small class="text-muted">Licence: <?= htmlspecialchars($avis['numero_licence']) ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark fs-6">
                                                <i class="fas fa-star"></i> <?= intval($avis['note'] ?? 5) ?>/5
                                            </span>
                                        </td>
                                        <td><div style="max-width: 200px; word-break: break-word;"><?= htmlspecialchars($avis['appreciation_salle'] ?? '-') ?></div></td>
                                        <td><div style="max-width: 200px; word-break: break-word;"><?= htmlspecialchars($avis['accueil'] ?? '-') ?></div></td>
                                        <td>
                                            <small class="d-block text-secondary"><strong>Améliorations:</strong> <?= htmlspecialchars($avis['ameliorations'] ?? '-') ?></small>
                                            <small class="d-block text-secondary"><strong>Reproches:</strong> <?= htmlspecialchars($avis['coaching_reproches'] ?? '-') ?></small>
                                            <small class="d-block text-secondary"><strong>Compliments:</strong> <?= htmlspecialchars($avis['coaching_compliments'] ?? '-') ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="small text-muted">Dabakh Fitness - Rue MZ 07, Sacré-Cœur 3 VDN, Dakar</p>
        </div>
    </div>
</body>
</html>
