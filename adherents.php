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
            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            $db->beginTransaction();

            $stmt_pres = $db->prepare("DELETE FROM presences WHERE adherent_id = ?");
            $stmt_pres->execute([$id]);

            $stmt_pay = $db->prepare("DELETE FROM paiements WHERE adherent_id = ?");
            $stmt_pay->execute([$id]);

            $stmt_ins = $db->prepare("DELETE FROM inscriptions WHERE adherent_id = ?");
            $stmt_ins->execute([$id]);

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
    <style>
        /* Styles spécifiques pour l'impression de la carte de membre */
        @media print {
            body * {
                visibility: hidden !important;
            }
            #printArea, #printArea * {
                visibility: visible !important;
            }
            #printArea {
                position: fixed;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                display: block !important;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .member-card {
            width: 420px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            border: 2px solid #dc3545;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow: hidden;
            position: relative;
        }
        .member-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(220, 53, 69, 0.15);
            border-radius: 50%;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between mb-4 no-print">
            <h2 class="fw-bold text-danger"><i class="fas fa-users"></i> Dabakh Fitness</h2>
            <div>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adherentModal" onclick="resetForm()">
                    <i class="fas fa-user-plus"></i> Nouvel Adhérent
                </button>
            </div>
        </div>

        <?php if ($message): ?><div class="alert alert-success no-print"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger no-print"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="card shadow border-0 no-print">
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
                            
                            $phone_clean = preg_replace('/[^0-9]/', '', $adh['telephone'] ?? '');
                            $whatsapp_msg = urlencode("Bonjour " . $adh['prenom'] . ", voici votre QR Code Dabakh Fitness (Licence : " . $licence . ") : " . $qr_src);
                            $whatsapp_url = "https://wa.me/" . $phone_clean . "?text=" . $whatsapp_msg;
                        ?>
                            <tr>
                                <td><span class="badge bg-dark"><?= htmlspecialchars($licence) ?></span></td>
                                <td><?= htmlspecialchars($adh['nom'] . ' ' . $adh['prenom']) ?></td>
                                <td><?= htmlspecialchars($adh['email']) ?><br><small class="text-muted"><?= htmlspecialchars($adh['telephone']) ?></small></td>
                                <td>
                                    <img src="<?= $qr_src ?>" style="width: 50px;" class="border p-1 bg-white">
                                    <a href="<?= $qr_src ?>" download="<?= $safe_filename ?>" class="btn btn-sm btn-link" title="Télécharger"><i class="fas fa-download"></i></a>
                                    <?php if (!empty($phone_clean)): ?>
                                        <a href="<?= $whatsapp_url ?>" target="_blank" class="btn btn-sm btn-success text-white" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-dark" onclick='printCard(<?= json_encode($adh) ?>, "<?= $qr_src ?>")' title="Imprimer la carte"><i class="fas fa-id-card"></i></button>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editAdherent(<?= json_encode($adh) ?>)' title="Modifier"><i class="fas fa-edit"></i></button>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $adh['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Zone dédiée à l'affichage et l'impression de la carte modernisée -->
    <div id="printArea" class="d-none my-4">
        <div class="member-card p-4 mx-auto">
            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-3 position-relative" style="z-index: 2;">
                <div>
                    <h4 class="m-0 fw-bold text-danger tracking-wider"><i class="fas fa-dumbbell"></i> DABAKH FITNESS</h4>
                    <small class="text-uppercase text-muted" style="letter-spacing: 2px; font-size: 0.75rem;">Carte de Membre</small>
                </div>
                <span class="badge bg-danger text-uppercase px-3 py-2 rounded-pill shadow-sm" id="cardStatut" style="font-size: 0.8rem;">ACTIF</span>
            </div>
            
            <div class="row align-items-center my-3 position-relative" style="z-index: 2;">
                <div class="col-7">
                    <div class="mb-2">
                        <span class="d-block text-muted small uppercase" style="font-size: 0.7rem;">Nom & Prénom</span>
                        <h5 class="fw-bold mb-0 text-truncate" id="cardNomPrenom" style="font-size: 1.1rem;">NOM Prénom</h5>
                    </div>
                    <div class="mb-2">
                        <span class="d-block text-muted small" style="font-size: 0.7rem;">N° de Licence</span>
                        <span class="font-monospace text-warning fw-bold" id="cardLicence" style="font-size: 0.95rem;">LIC-0000-000</span>
                    </div>
                    <div>
                        <span class="d-block text-muted small" style="font-size: 0.7rem;">Téléphone</span>
                        <span class="text-white-55 small" id="cardTel">-</span>
                    </div>
                </div>
                <div class="col-5 text-center">
                    <div class="bg-white p-2 rounded-3 shadow d-inline-block">
                        <img id="cardQrImg" src="" alt="QR Code" style="width: 110px; height: 110px; display: block;">
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pt-2 border-top border-secondary text-white-50 position-relative" style="font-size: 0.7rem; z-index: 2;">
                <i class="fas fa-map-marker-alt text-danger"></i> Sacré-Cœur 3 VDN, Dakar &bull; <i class="fas fa-phone text-danger"></i> Service Adhérent
            </div>
        </div>
    </div>

    <!-- Modal Ajout / Modification -->
    <div class="modal fade" id="adherentModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="" method="POST">
        <div class="modal-header"><h5 class="modal-title" id="modalTitle">Ajouter un Adhérent</h5></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="save"><input type="hidden" name="id" id="adh_id">
            <input type="text" class="form-control mb-2" name="nom" id="adh_nom" placeholder="Nom" required>
            <input type="text" class="form-control mb-2" name="prenom" id="adh_prenom" placeholder="Prénom" required>
            <input type="email" class="form-control mb-2" name="email" id="adh_email" placeholder="Email" required>
            <input type="text" class="form-control mb-2" name="telephone" id="adh_telephone" placeholder="Téléphone (ex: 221770000000)">
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
            document.getElementById('adh_nom').value = '';
            document.getElementById('adh_prenom').value = '';
            document.getElementById('adh_email').value = '';
            document.getElementById('adh_telephone').value = '';
            document.getElementById('adh_statut').value = 'actif';
        }
        function editAdherent(adh) {
            document.getElementById('modalTitle').innerText = 'Modifier l\'Adhérent';
            document.getElementById('adh_id').value = adh.id;
            document.getElementById('adh_nom').value = adh.nom;
            document.getElementById('adh_prenom').value = adh.prenom;
            document.getElementById('adh_email').value = adh.email;
            document.getElementById('adh_telephone').value = adh.telephone;
            document.getElementById('adh_statut').value = adh.statut || 'actif';
            new bootstrap.Modal(document.getElementById('adherentModal')).show();
        }
        function printCard(adh, qrSrc) {
            document.getElementById('cardNomPrenom').innerText = adh.nom.toUpperCase() + ' ' + adh.prenom;
            document.getElementById('cardLicence').innerText = adh.numero_licence;
            document.getElementById('cardTel').innerText = adh.telephone || 'Non renseigné';
            document.getElementById('cardStatut').innerText = (adh.statut || 'actif').toUpperCase();
            document.getElementById('cardQrImg').src = qrSrc;

            let printArea = document.getElementById('printArea');
            printArea.classList.remove('d-none');
            
            window.print();

            printArea.classList.add('d-none');
        }
    </script>
</body>
</html>
