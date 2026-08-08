<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

$db = (new Database())->getConnection();

$message = '';
$error = '';

// Traitement des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            // Désactiver temporairement les vérifications de clés étrangères pour cette session de suppression
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            $db->beginTransaction();

            // 1. Supprimer les présences liées
            $stmt_pres = $db->prepare("DELETE FROM presences WHERE adherent_id = ?");
            $stmt_pres->execute([$id]);

            // 2. Supprimer les paiements liés
            $stmt_pay = $db->prepare("DELETE FROM paiements WHERE adherent_id = ?");
            $stmt_pay->execute([$id]);

            // 3. Supprimer les inscriptions liées
            $stmt_ins = $db->prepare("DELETE FROM inscriptions WHERE adherent_id = ?");
            $stmt_ins->execute([$id]);

            // 4. Supprimer l'adhérent
            $stmt = $db->prepare("DELETE FROM adherents WHERE id = ?");
            $stmt->execute([$id]);

            $db->commit();
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            $message = "Adhérent et toutes ses données associées supprimés avec succès.";
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
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
                $stmt = $db->prepare("UPDATE adherents SET nom=?, prenom=?, email=?, telephone=?, statut=? WHERE id=?");
                $stmt->execute([$nom, $prenom, $email, $telephone, $statut, $id]);
                $message = "Adhérent mis à jour avec succès.";
            } else {
                $stmtMax = $db->query("SELECT MAX(id) as max_id FROM adherents");
                $resMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
                $nextId = intval($resMax['max_id'] ?? 0) + 1;
                $numero_licence = 'LIC-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                $stmt = $db->prepare("INSERT INTO adherents (numero_licence, nom, prenom, email, telephone, statut) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$numero_licence, $nom, $prenom, $email, $telephone, $statut]);
                $message = "Nouvel adhérent ajouté avec succès (Licence générée : $numero_licence).";
            }
        } catch (Exception $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

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
    <title>Dabakh Fitness - Gestion des Adhérents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold text-danger"><i class="fas fa-users"></i> Dabakh Fitness</h2>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adherentModal" onclick="resetForm()">
                <i class="fas fa-user-plus"></i> Nouvel Adhérent
            </button>
        </div>

        <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white">Liste des Licences (<?= count($adherents) ?>)</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Licence</th>
                            <th>Adhérent</th>
                            <th>Contact</th>
                            <th>QR Code</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adherents as $adh): 
                            $licence = $adh['numero_licence'];
                            $url = $base_url . urlencode($licence);
                            $qr_src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($url);
                            $safe_filename = 'QR_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $licence) . '.png';
                        ?>
                            <tr>
                                <td><span class="badge bg-dark"><?= htmlspecialchars($licence) ?></span></td>
                                <td><?= htmlspecialchars($adh['nom'] . ' ' . $adh['prenom']) ?></td>
                                <td><?= htmlspecialchars($adh['email']) ?></td>
                                <td>
                                    <img src="<?= $qr_src ?>" style="width: 50px;" class="border p-1">
                                    <a href="<?= $qr_src ?>" download="<?= $safe_filename ?>" class="btn btn-sm btn-link"><i class="fas fa-download"></i></a>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" onclick='editAdherent(<?= json_encode($adh) ?>)'><i class="fas fa-edit"></i></button>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression de cet adhérent et de ses données ?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $adh['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="adherentModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="" method="POST">
        <div class="modal-header"><h5 class="modal-title" id="modalTitle">Ajouter un Adhérent</h5></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="save"><input type="hidden" name="id" id="adh_id">
            <input type="text" class="form-control mb-2" name="nom" id="adh_nom" placeholder="Nom" required>
            <input type="text" class="form-control mb-2" name="prenom" id="adh_prenom" placeholder="Prénom" required>
            <input type="email" class="form-control mb-2" name="email" id="adh_email" placeholder="Email" required>
            <input type="text" class="form-control mb-2" name="telephone" id="adh_telephone" placeholder="Téléphone">
            <select class="form-select" name="statut" id="adh_statut">
                <option value="actif">Actif</option><option value="suspendu">Suspendu</option><option value="archive">Archivé</option>
            </select>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-danger">Enregistrer</button></div>
    </form></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('modalTitle').innerText = 'Ajouter un Adhérent';
            document.getElementById('adh_id').value = '';
        }
        function editAdherent(adh) {
            document.getElementById('modalTitle').innerText = 'Modifier l\'Adhérent';
            document.getElementById('adh_id').value = adh.id;
            document.getElementById('adh_nom').value = adh.nom;
            document.getElementById('adh_prenom').value = adh.prenom;
            document.getElementById('adh_email').value = adh.email;
            document.getElementById('adh_telephone').value = adh.telephone;
            new bootstrap.Modal(document.getElementById('adherentModal')).show();
        }
    </script>
</body>
</html>
