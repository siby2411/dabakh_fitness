<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

$db = (new Database())->getConnection();

// Désactivation temporaire du mode strict
try {
    $db->exec("SET SESSION sql_mode = ''");
} catch (Exception $e) {}

// 1. CRÉATION ET MISE À JOUR DE LA TABLE DES AVIS
try {
    $db->exec("CREATE TABLE IF NOT EXISTS avis_adherents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note INT NOT NULL DEFAULT 5,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $queries = [
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS numero_licence VARCHAR(255) NULL",
        "ALTER TABLE avis_adherents MODIFY COLUMN numero_licence VARCHAR(255) NULL",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS is_anonymous TINYINT(1) DEFAULT 0",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS appreciation_salle TEXT",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS ameliorations TEXT",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS accueil TEXT",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS coaching_reproches TEXT",
        "ALTER TABLE avis_adherents ADD COLUMN IF NOT EXISTS coaching_compliments TEXT"
    ];

    foreach ($queries as $q) {
        try { $db->exec($q); } catch (Exception $e) {}
    }
} catch (Exception $e) {
    die("Erreur de configuration de la base de données : " . $e->getMessage());
}

// Récupération de la liste des adhérents
$adherents_list = [];
try {
    $stmt_list = $db->query("SELECT numero_licence, nom, prenom FROM adherents WHERE numero_licence IS NOT NULL AND numero_licence != '' ORDER BY nom ASC, prenom ASC");
    $adherents_list = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Récupération de la licence passée dans l'URL (ex: ?numero_licence=... ou ?licence=...)
$url_licence = trim($_GET['numero_licence'] ?? ($_GET['licence'] ?? ''));

$message = '';
$success = false;
$debug_error = '';

// 2. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $numero_licence = null;

    if (!$is_anonymous) {
        // Priorité à la valeur postée, sinon on reprend celle de l'URL si elle était figée
        $numero_licence = !empty($_POST['numero_licence']) ? trim($_POST['numero_licence']) : (!empty($_POST['url_licence']) ? trim($_POST['url_licence']) : null);
    }

    $note = intval($_POST['note'] ?? 5);
    $appreciation = trim($_POST['appreciation_salle'] ?? '');
    $ameliorations = trim($_POST['ameliorations'] ?? '');
    $accueil = trim($_POST['accueil'] ?? '');
    $reproches = trim($_POST['coaching_reproches'] ?? '');
    $compliments = trim($_POST['coaching_compliments'] ?? '');

    try {
        $stmt = $db->prepare("INSERT INTO avis_adherents 
            (numero_licence, is_anonymous, note, appreciation_salle, ameliorations, accueil, coaching_reproches, coaching_compliments) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $numero_licence, 
            $is_anonymous, 
            $note, 
            $appreciation, 
            $ameliorations, 
            $accueil, 
            $reproches, 
            $compliments
        ]);
        
        $message = "Merci pour votre retour ! Votre avis nous aide à améliorer Dabakh Fitness.";
        $success = true;
    } catch (PDOException $e) {
        $message = "Une erreur est survenue lors de l'envoi de votre avis.";
        $debug_error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donner un avis - Dabakh Fitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .form-card { border-top: 5px solid #dc3545; }
        .rating-stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 0.5rem; }
        .rating-stars input { display: none; }
        .rating-stars label { font-size: 2rem; color: #ccc; cursor: pointer; transition: color 0.2s; }
        .rating-stars input:checked ~ label, .rating-stars label:hover, .rating-stars label:hover ~ label { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-danger"><i class="fas fa-bullhorn"></i> Dabakh Fitness</h2>
                    <p class="text-muted">Aidez-nous à vous offrir la meilleure expérience possible !</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $success ? 'success' : 'danger' ?> text-center shadow-sm">
                        <p class="fs-5 mb-0"><?= $message ?></p>
                        <?php if (!empty($debug_error)): ?>
                            <hr>
                            <small class="text-danger font-monospace">Détail technique : <?= htmlspecialchars($debug_error) ?></small>
                        <?php endif; ?>
                        <?php if($success): ?>
                            <br><a href="donner_avis.php" class="btn btn-outline-success mt-3">Saisir un autre avis</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <div class="card shadow form-card bg-white border-0">
                    <div class="card-body p-4 p-md-5">
                        <form action="" method="POST" id="avisForm">
                            
                            <!-- Identification de l'Adhérent -->
                            <div class="mb-4 bg-light p-3 rounded border">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-id-card text-danger"></i> Identification de l'Adhérent</label>
                                
                                <?php if (!empty($url_licence)): ?>
                                    <!-- Si un QR code avec licence est scanné, on verrouille l'adhérent proprement -->
                                    <input type="hidden" name="url_licence" value="<?= htmlspecialchars($url_licence) ?>">
                                    <div class="alert alert-success py-2 mb-0">
                                        <i class="fas fa-check-circle"></i> Connecté via votre QR code (Licence : <strong><?= htmlspecialchars($url_licence) ?></strong>)
                                    </div>
                                <?php else: ?>
                                    <!-- Mode normal sans QR code spécifique -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                                        <label class="form-check-label fw-semibold text-secondary" for="is_anonymous">
                                            Rester Anonyme (Ne pas lier à mon profil)
                                        </label>
                                    </div>

                                    <div id="selectAdherentContainer">
                                        <select name="numero_licence" id="numero_licence" class="form-select">
                                            <option value="">-- Sélectionnez votre profil (Nom, Prénom & Licence) --</option>
                                            <?php foreach ($adherents_list as $adh): ?>
                                                <option value="<?= htmlspecialchars($adh['numero_licence']) ?>">
                                                    <?= htmlspecialchars($adh['nom'] . ' ' . $adh['prenom'] . ' [Licence: ' . $adh['numero_licence'] . ']') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Choisissez votre nom dans la liste déroulante ou cochez "Rester Anonyme".</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Note Globale -->
                            <div class="mb-4 bg-light p-3 rounded text-center">
                                <label class="form-label fw-bold d-block mb-2">Note globale de votre expérience</label>
                                <div class="rating-stars">
                                    <input type="radio" id="star5" name="note" value="5" checked><label for="star5" title="Excellent"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="note" value="4"><label for="star4" title="Très bien"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="note" value="3"><label for="star3" title="Moyen"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="note" value="2"><label for="star2" title="Médiocre"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="note" value="1"><label for="star1" title="Mauvais"><i class="fas fa-star"></i></label>
                                </div>
                            </div>

                            <!-- 1. La Salle -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-dumbbell text-danger"></i> Comment vous trouvez la salle ?</label>
                                <textarea name="appreciation_salle" class="form-control border-danger border-opacity-25" rows="2" placeholder="Ex: Spacieuse, bien équipée, etc..." required></textarea>
                            </div>

                            <!-- 2. Améliorations -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-tools text-warning"></i> Quelles sont les choses à améliorer dans la salle ?</label>
                                <textarea name="ameliorations" class="form-control" rows="2" placeholder="Vos suggestions d'amélioration..."></textarea>
                            </div>

                            <!-- 3. L'accueil -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-smile text-primary"></i> Que pensez-vous de l’accueil à la salle ?</label>
                                <textarea name="accueil" class="form-control" rows="2" placeholder="Ex: Chaleureux, peut mieux faire..." required></textarea>
                            </div>

                            <!-- 4. Reproches Coaching -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-exclamation-circle text-danger"></i> Par rapport au coaching, avez-vous des reproches ?</label>
                                <textarea name="coaching_reproches" class="form-control" rows="2" placeholder="Ex: Manque de suivi, retard... (Laissez vide si aucun)"></textarea>
                            </div>

                            <!-- 5. Compliments Coaching -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="fas fa-award text-success"></i> Ou des compliments à faire ?</label>
                                <textarea name="coaching_compliments" class="form-control" rows="2" placeholder="Ce que vous appréciez chez nos coachs..."></textarea>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-danger btn-lg fw-bold shadow">
                                    <i class="fas fa-paper-plane"></i> Envoyer mon avis
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <p class="small text-muted">Dabakh Fitness - Rue MZ 07, Sacré-Cœur 3 VDN, Dakar</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const anonymousCheckbox = document.getElementById('is_anonymous');
        const selectLicence = document.getElementById('numero_licence');

        if (anonymousCheckbox && selectLicence) {
            anonymousCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    selectLicence.value = "";
                    selectLicence.disabled = true;
                } else {
                    selectLicence.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
