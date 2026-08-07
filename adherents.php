<?php
require_once 'config/database.php';
include 'header.php';
$database = new Database();
$db = $database->getConnection();

// Détection dynamique de l'hôte et du chemin de base (compatible DHCP / IP changeante)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri_path = dirname($_SERVER['PHP_SELF']);
$base_url = $protocol . "://" . $host . ($uri_path == '/' || $uri_path == '\\' ? '' : $uri_path);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $numero_licence = 'OM-' . date('Y') . '-' . rand(1000, 9999);
    $query = "INSERT INTO adherents (numero_licence, nom, prenom, email, telephone, date_naissance, adresse, discipline_principale)
              VALUES (:num, :nom, :prenom, :email, :tel, :naissance, :adresse, :discipline)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':num' => $numero_licence,
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':email' => $_POST['email'],
        ':tel' => $_POST['telephone'],
        ':naissance' => $_POST['date_naissance'],
        ':adresse' => $_POST['adresse'],
        ':discipline' => $_POST['discipline_principale']
    ]);
    
    // URL complète pointant directement vers donner_avis.php avec la licence
    $url_avis = $base_url . "/donner_avis.php?licence=" . $numero_licence;
    $success = "Adhérent ajouté avec succès ! Licence : <strong>" . $numero_licence . "</strong>";
    $success_qr = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($url_avis);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM adherents WHERE statut='actif' ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$adherents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_query = "SELECT COUNT(*) as total FROM adherents WHERE statut='actif'";
$total = $db->query($total_query)->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total / $limit);
?>

<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-users"></i> Gestion des Adhérents & QR Codes</h3>
            <a href="avis_admin.php" class="btn btn-sm btn-warning"><i class="fas fa-comments"></i> Gérer les Avis</a>
        </div>
        <div class="card-body">
            
            <!-- Notifications d'envoi d'e-mail -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'email_sent'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Le QR Code a été envoyé par e-mail à l'adhérent avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'email_error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Erreur lors de l'envoi de l'e-mail. Vérifiez la configuration du serveur mail.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($success)): ?>
                <div class="alert alert-success text-center">
                    <p><?= $success ?></p>
                    <?php if(isset($success_qr)): ?>
                        <div class="mt-2">
                            <p class="mb-1">QR Code de l'adhérent (Scannez pour donner votre avis) :</p>
                            <img src="<?= $success_qr ?>" alt="QR Code Adhérent" class="img-thumbnail bg-white p-2">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAdherentModal">
                <i class="fas fa-plus"></i> Nouvel Adhérent
            </button>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Licence</th><th>Nom & Prénom</th><th>Email</th><th>Téléphone</th><th>Discipline</th><th>QR Code</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($adherents as $a): 
                            $url_qr_adherent = $base_url . "/donner_avis.php?licence=" . $a['numero_licence'];
                        ?>
                        <tr>
                            <td><strong><?= $a['numero_licence'] ?></strong></td>
                            <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                            <td><?= htmlspecialchars($a['email']) ?></td>
                            <td><?= $a['telephone'] ?></td>
                            <td><span class="badge bg-info"><?= $a['discipline_principale'] ?></span></td>
                            <td>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=<?= urlencode($url_qr_adherent) ?>" alt="QR">
                            </td>
                            <td>
                                <a href="adherent_details.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-info" title="Détails"><i class="fas fa-eye"></i></a>
                                <a href="envoyer_qrcode.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning" title="Envoyer le QR Code par Email" onclick="return confirm('Envoyer le QR code par e-mail à <?= htmlspecialchars($a['email']) ?> ?');"><i class="fas fa-envelope"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages > 1): ?>
            <nav><ul class="pagination"><?php for($i=1;$i<=$total_pages;$i++): ?>
                <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?></ul></nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addAdherentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5><i class="fas fa-user-plus"></i> Nouvel Adhérent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Nom *</label><input type="text" name="nom" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Prénom *</label><input type="text" name="prenom" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Téléphone</label><input type="tel" name="telephone" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Date Naissance</label><input type="date" name="date_naissance" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Discipline Principale</label>
                            <select name="discipline_principale" class="form-control">
                                <option>Boxe Anglaise</option><option>Karaté</option><option>Jiu-Jitsu Brésilien</option>
                                <option>Muay Thai</option><option>CrossFit</option><option>Yoga</option><option>Kickboxing</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3"><label>Adresse</label><textarea name="adresse" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
