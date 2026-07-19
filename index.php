<?php
require_once 'config/database.php';
include 'header.php';
$database = new Database();
$db = $database->getConnection();

// --- 1. Récupération des Statistiques Globales ---
$stats = [];
$stats['adherents'] = $db->query("SELECT COUNT(*) as total FROM adherents WHERE statut='actif'")->fetch(PDO::FETCH_ASSOC)['total'];
$stats['formateurs'] = $db->query("SELECT COUNT(*) as total FROM formateurs WHERE statut='actif'")->fetch(PDO::FETCH_ASSOC)['total'];
$stats['cours'] = $db->query("SELECT COUNT(*) as total FROM cours WHERE actif=1")->fetch(PDO::FETCH_ASSOC)['total'];
$stats['revenus_mois'] = $db->query("SELECT COALESCE(SUM(montant),0) as total FROM paiements WHERE MONTH(date_paiement)=MONTH(CURRENT_DATE()) AND statut='valide'")->fetch(PDO::FETCH_ASSOC)['total'];

// --- 2. Disciplines et Cours ---
$disciplines = $db->query("SELECT * FROM disciplines WHERE actif=1 ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$query_cours = "SELECT c.*, d.nom as discipline, CONCAT(f.prenom,' ',f.nom) as formateur
                FROM cours c
                JOIN disciplines d ON c.discipline_id=d.id
                JOIN formateurs f ON c.formateur_id=f.id
                WHERE c.jour = UPPER(DAYNAME(CURRENT_DATE())) AND c.actif=1
                ORDER BY c.heure_debut";
$cours_jour = $db->query($query_cours)->fetchAll(PDO::FETCH_ASSOC);

// --- 3. Dernières Notifications ---
// On récupère les 5 dernières notifications triées par date d'envoi
$query_notifs = "SELECT * FROM notifications ORDER BY date_envoi DESC LIMIT 5";
$recent_notifications = $db->query($query_notifs)->fetchAll(PDO::FETCH_ASSOC);

// --- 4. Message du jour ---
$messages = [
    "La discipline que vous pratiquez aujourd'hui construit le champion de demain.",
    "Votre seule limite est celle que vous vous imposez.",
    "Chaque séance vous rapproche de votre meilleure version.",
    "Le succès n'est pas un hasard, c'est le résultat de la discipline quotidienne.",
    "Un corps sain dans un esprit sain - La devise de Dabakh Fitness"
];
$message_du_jour = $messages[array_rand($messages)];
?>

<div class="container">
    <!-- Bannière avec logo Dabakh Fitness -->
    <div class="alert alert-info mb-4" style="background: linear-gradient(135deg, #FF4B2B, #FF416C); color: white; border: none; padding: 25px;">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <img src="fitness.jpeg" alt="Logo" style="width: 150px; height: 150px; border-radius: 50%; border: 4px solid white; object-fit: cover; margin-right: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            </div>
            <div>
                <i class="fas fa-quote-left mb-2"></i>
                <h4 class="mb-1">"<?= htmlspecialchars($message_du_jour) ?>"</h4>
                <p class="mb-0">🏆 Dabakh Fitness - L'excellence en mouvement</p>
            </div>
        </div>
    </div>

    <!-- Carrousel des disciplines -->
    <div class="card mb-4">
        <div class="card-header"><h3><i class="fas fa-dumbbell"></i> Nos disciplines à l'honneur</h3></div>
        <div class="card-body">
            <div class="disciplines-slider">
                <div class="slider-track">
                    <?php foreach(array_merge($disciplines, $disciplines) as $d): ?>
                    <div class="discipline-card">
                        <i class="fas fa-fist-raised fa-2x" style="color: #FF4B2B"></i>
                        <h5><?= htmlspecialchars($d['nom']) ?></h5>
                        <p><?= number_format($d['tarif_mensuel'], 0, ',', ' ') ?> F/mois</p>
                        <small><?= $d['age_minimum'] ?> ans minimum</small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Statistiques (4 cartes) -->
    <div class="row">
        <div class="col-md-3"><div class="stat-card"><i class="fas fa-users fa-3x mb-3"></i><h3 class="stat-number"><?= $stats['adherents'] ?></h3><p>Adhérents Actifs</p></div></div>
        <div class="col-md-3"><div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c)"><i class="fas fa-chalkboard-user fa-3x mb-3"></i><h3 class="stat-number"><?= $stats['formateurs'] ?></h3><p>Formateurs Experts</p></div></div>
        <div class="col-md-3"><div class="stat-card" style="background: linear-gradient(135deg, #4facfe, #00f2fe)"><i class="fas fa-calendar-alt fa-3x mb-3"></i><h3 class="stat-number"><?= count($disciplines) ?></h3><p>Disciplines</p></div></div>
        <div class="col-md-3"><div class="stat-card" style="background: linear-gradient(135deg, #43e97b, #38f9d7)"><i class="fas fa-money-bill-wave fa-3x mb-3"></i><h3 class="stat-number"><?= number_format($stats['revenus_mois'], 0, ',', ' ') ?> F</h3><p>Revenus du Mois</p></div></div>
    </div>

    <!-- Section Principale : Cours et Actions -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-calendar-day"></i> Cours du Jour - <?= date('l d/m/Y') ?></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Horaire</th><th>Discipline</th><th>Formateur</th><th>Salle</th><th>Places</th></tr></thead>
                            <tbody>
                                <?php foreach($cours_jour as $cours): ?>
                                <tr><td><?= date('H:i', strtotime($cours['heure_debut'])) ?> - <?= date('H:i', strtotime($cours['heure_fin'])) ?></td><td><strong><?= htmlspecialchars($cours['discipline']) ?></strong></td><td><?= htmlspecialchars($cours['formateur']) ?></td><td><?= htmlspecialchars($cours['salle']) ?></td><td><?= $cours['inscrits'] ?>/<?= $cours['capacite'] ?></td></tr>
                                <?php endforeach; ?>
                                <?php if(empty($cours_jour)): ?><tr><td colspan="5" class="text-center">Aucun cours programmé aujourd'hui</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-bolt"></i> Actions Rapides</div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="adherents.php?action=add" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> Nouvel Adhérent</a>
                        <a href="paiements.php" class="btn btn-success btn-lg"><i class="fas fa-hand-holding-usd"></i> Encaisser Paiement</a>
                        <a href="performance.php" class="btn btn-info btn-lg text-white"><i class="fas fa-chart-line"></i> Suivi Performance</a>
                        <a href="challenges.php" class="btn btn-warning btn-lg"><i class="fas fa-trophy"></i> Défis du Mois</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVELLE SECTION : Notifications & Statistiques Détaillées -->
    <div class="row mt-4 mb-5">
        <!-- Flux de Notifications -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell"></i> Dernières Notifications</span>
                    <a href="notifications.php" class="btn btn-sm btn-light">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach($recent_notifications as $notif): ?>
                            <?php
                                // Déterminer l'icône selon le type
                                $icon = 'fa-info-circle text-info';
                                if($notif['type'] == 'alerte') $icon = 'fa-exclamation-triangle text-danger';
                                if($notif['type'] == 'promo') $icon = 'fa-tag text-success';
                                if($notif['type'] == 'rappel') $icon = 'fa-clock text-warning';
                            ?>
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold"><i class="fas <?= $icon ?> me-2"></i> <?= htmlspecialchars($notif['titre']) ?></h6>
                                    <small class="text-muted"><?= date('d/m à H:i', strtotime($notif['date_envoi'])) ?></small>
                                </div>
                                <p class="mb-1 text-muted small"><?= htmlspecialchars(substr($notif['message'], 0, 80)) ?><?= strlen($notif['message']) > 80 ? '...' : '' ?></p>
                            </li>
                        <?php endforeach; ?>
                        <?php if(empty($recent_notifications)): ?>
                            <li class="list-group-item text-center text-muted py-4">Aucune notification envoyée récemment.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Centre de Rapports et Statistiques -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-chart-pie"></i> Rapports et Analyses Financières
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="stats.php?type=paiements_adherent" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="fas fa-file-invoice-dollar text-primary me-3"></i> Historique des Paiements par Adhérent</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="stats.php?type=paiements_discipline" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="fas fa-layer-group text-success me-3"></i> Chiffre d'Affaires par Discipline</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="stats.php?type=rentabilite" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="fas fa-arrow-trend-up text-danger me-3"></i> Palmarès des Disciplines les plus rentables</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="statistiques.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div><i class="fas fa-chart-bar text-info me-3"></i> Tableau de bord Analytics Complet</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.disciplines-slider { overflow: hidden; position: relative; white-space: nowrap; padding: 10px 0; }
.slider-track { display: inline-block; animation: scroll 30s linear infinite; }
.discipline-card { display: inline-block; width: 220px; margin: 0 10px; padding: 15px; background: #fff; border: 1px solid #eee; border-radius: 10px; text-align: center; vertical-align: top; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
@keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
.stat-card { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px 20px; border-radius: 15px; text-align: center; margin-bottom: 20px; transition: transform 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.stat-card:hover { transform: translateY(-5px); }
.stat-number { font-size: 2.5rem; font-weight: 800; margin: 10px 0; }
.list-group-item-action:hover { background-color: #f8f9fa; }
</style>

<?php include 'footer.php'; ?>
