<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

$db = (new Database())->getConnection();

$message = '';
$error = '';

// Traitement des actions CRUD (Ajout / Modification / Suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM adherents WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Adhérent supprimé avec succès.";
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    } elseif ($action === 'save') {
        $id = intval($_POST['id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $statut = trim($_POST['statut'] ?? 'actif');

        try {
            if ($id > 0) {
                // Modification (on ne touche pas au numéro de licence existant)
                $stmt = $db->prepare("UPDATE adherents SET nom=?, prenom=?, email=?, telephone=?, statut=? WHERE id=?");
                $stmt->execute([$nom, $prenom, $email, $telephone, $statut, $id]);
                $message = "Adhérent mis à jour avec succès.";
            } else {
                // Génération automatique et transparente du numéro de licence
                $stmtMax = $db->query("SELECT MAX(id) as max_id FROM adherents");
                $resMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
                $nextId = intval($resMax['max_id'] ?? 0) + 1;
                $numero_licence = 'LIC-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                // Ajout
                $stmt = $db->prepare("INSERT INTO adherents (numero_licence, nom, prenom, email, telephone, statut) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$numero_licence, $nom, $prenom, $email, $telephone, $statut]);
                $message = "Nouvel adhérent ajouté avec succès (Licence générée : $numero_licence).";
            }
        } catch (Exception $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

// Récupération de la liste des adhérents
$adherents = [];
try {
    $stmt = $db->query("SELECT * FROM adherents ORDER BY nom ASC, prenom ASC");
    $adherents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$base_url = "http://127.0.0.1:8000/donner_avis.php?numero_licence=";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Adhérents & QR Codes - Dabakh Fitness</title>
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
                <h2 class="fw-bold text-danger"><i class="fas fa-users"></i> Dabakh Fitness - Gestion des Adhérents</h2>
                <p class="text-muted">Répertoire complet, CRUD et QR Codes d'avis intégrés</p>
            </div>
            <div>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adherentModal" onclick="resetForm()">
                    <i class="fas fa-user-plus"></i> Nouvel Adhérent
                </button>
                <a href="avis_admin.php" class="btn btn-outline-danger ms-2"><i class="fas fa-comments"></i> Voir les avis</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success shadow-sm"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card"></i> Liste des Licences (<?= count($adherents) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($adherents)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted fs-5">Aucun adhérent trouvé dans la base de données.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>N° Licence</th>
                                    <th>Nom & Prénom</th>
                                    <th>Contact</th>
                                    <th>Statut</th>
                                    <th>QR Code (Avis)</th>
                                    <th class="text-end">Actions CRUD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($adherents as $adh): 
                                    $url = $base_url . urlencode($adh['numero_licence']);
                                    $qr_src = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($url);
                                ?>
                                    <tr>
                                        <td><span class="badge bg-dark font-monospace"><?= htmlspecialchars($adh['numero_licence']) ?></span></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($adh['nom'] . ' ' . $adh['prenom']) ?></td>
                                        <td>
                                            <small class="d-block"><i class="fas fa-envelope text-muted"></i> <?= htmlspecialchars($adh['email'] ?? '-') ?></small>
                                            <small class="d-block"><i class="fas fa-phone text-muted"></i> <?= htmlspecialchars($adh['telephone'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $statut = $adh['statut'] ?? 'actif';
                                            $badge_bg = ($statut === 'actif') ? 'bg-success' : 'bg-secondary';
                                            ?>
                                            <span class="badge <?= $badge_bg ?>"><?= ucfirst($statut) ?></span>
                                        </td>
                                        <td>
                                            <img src="<?= $qr_src ?>" alt="QR Code" style="width: 55px; height: 55px;" class="border p-1 bg-white rounded shadow-sm" title="QR Code d'avis encapsulé">
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= $url ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Tester le lien">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary" onclick='editAdherent(<?= json_encode($adh) ?>)' title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet adhérent ?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $adh['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <!-- Modal Ajout / Modification (Sans champ numero_licence) -->
    <div class="modal fade" id="adherentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalTitle"><i class="fas fa-user-plus"></i> Ajouter un Adhérent</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" id="adh_id" value="">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" class="form-control" name="nom" id="adh_nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Prénom</label>
                                <input type="text" class="form-control" name="prenom" id="adh_prenom" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" id="adh_email" required placeholder="nom@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Téléphone</label>
                            <input type="text" class="form-control" name="telephone" id="adh_telephone" placeholder="+221 ...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Statut</label>
                            <select class="form-select" name="statut" id="adh_statut">
                                <option value="actif">Actif</option>
                                <option value="suspendu">Suspendu</option>
                                <option value="archive">Archivé</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus"></i> Ajouter un Adhérent';
            document.getElementById('adh_id').value = '';
            document.getElementById('adh_nom').value = '';
            document.getElementById('adh_prenom').value = '';
            document.getElementById('adh_email').value = '';
            document.getElementById('adh_telephone').value = '';
            document.getElementById('adh_statut').value = 'actif';
        }

        function editAdherent(adh) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier l\'Adhérent';
            document.getElementById('adh_id').value = adh.id;
            document.getElementById('adh_nom').value = adh.nom;
            document.getElementById('adh_prenom').value = adh.prenom;
            document.getElementById('adh_email').value = adh.email || '';
            document.getElementById('adh_telephone').value = adh.telephone || '';
            document.getElementById('adh_statut').value = adh.statut || 'actif';
            
            var myModal = new bootstrap.Modal(document.getElementById('adherentModal'));
            myModal.show();
        }
    </script>
</body>
</html>
