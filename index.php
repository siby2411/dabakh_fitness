<?php
// Activation du débogage
ini_set('display_errors', 1); ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion de la configuration de la base de données et du header
require_once 'config/database.php';
include 'header.php';

// Récupération des statistiques réelles pour le Dashboard
$db = (new Database())->getConnection();
$total_adherents = 0;
$total_disciplines = 0;
$total_produits = 0;
$total_ca = 0;
$total_avis = 0;
$moyenne_avis = 0;

try {
    $total_adherents = $db->query("SELECT COUNT(*) FROM adherents")->fetchColumn();
    $total_disciplines = $db->query("SELECT COUNT(*) FROM disciplines")->fetchColumn();
    $total_produits = $db->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    $total_ca = $db->query("SELECT SUM(montant) FROM paiements")->fetchColumn() ?: 0;
    $total_avis = $db->query("SELECT COUNT(*) FROM avis_adherents")->fetchColumn() ?: 0;
    $moyenne_avis = $db->query("SELECT AVG(note) FROM avis_adherents")->fetchColumn() ?: 0;
} catch (Exception $e) {
    // Tables potentiellement non initialisées
}
?>

<!-- ========================================== -->
<!-- MÉTADONNÉES SEO & GOOGLE SEARCH CONSOLE -->
<!-- ========================================== -->
<meta name="google-site-verification" content="VOTRE_CODE_VERIFICATION_GOOGLE_SEARCH_CONSOLE">
<meta name="description" content="Dabakh Fitness & Wellness à Dakar : Salle de sport, arts martiaux et fitness située rue MZ 05 à Sacré-Cœur 3 VDN (à 40 mètres de la rue MZ 07, près des Résidences Mamoune et de l'école Les Petits Pas). Contact Coach Moussa : +221 77 532 37 25.">
<meta name="keywords" content="salle de sport Dakar, Dabakh Fitness Wellness, fitness Sacré Coeur 3 VDN, rue MZ 05, rue MZ 07, Coach Moussa, arts martiaux Sénégal, musculation Dakar">
<meta name="author" content="Dabakh Fitness Consulting - Mohamed Siby">
<link rel="sitemap" type="application/xml" title="Sitemap" href="sitemap.xml">

<!-- Balisage Schema.org pour le référencement local Google (SEO Local précis) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SportsActivityLocation",
  "name": "Dabakh Fitness Wellness",
  "image": "https://dabakhfitness.sn/assets/img/logo.png",
  "email": "sibymohamed24@gmail.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Rue MZ 05 (à 40 mètres de la rue MZ 07, Près des Résidences Mamoune et de l'école Les Petits Pas)",
    "addressLocality": "Sacré-Cœur 3 VDN, Dakar",
    "addressCountry": "SN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 14.7185,
    "longitude": -17.4682
  },
  "url": "https://dabakhfitness.sn",
  "telephone": "+221775323725"
}
</script>

<div class="container my-5">

    <!-- En-tête avec Design Rouge & Blanc et Images Agrandies -->
    <div class="text-center mb-5 p-4 rounded bg-dark text-white shadow-lg border border-danger">
        <div class="mb-4">
            <img src="fitness.jpeg" alt="Dabakh Fitness Wellness Centre" class="img-fluid rounded shadow-lg border border-danger" style="max-height: 300px; width: 100%; object-fit: cover;" onerror="this.style.display='none'">
        </div>

        <h1 class="display-3 fw-bold text-uppercase text-danger mt-3">Dabakh Fitness Wellness - Dakar</h1>
        <p class="lead text-white fw-semibold fst-italic fs-4">« Votre douleur d'aujourd'hui est votre énergie de demain »</p>
        <p class="text-light fs-6">Centre de référence en Arts Martiaux & Fitness à Sacré-Cœur 3 VDN — <strong class="text-danger">Rue MZ 05</strong> (À 40 mètres de la rue MZ 07, près des Résidences Mamoune et de l'école Les Petits Pas | Contact Coach Moussa : <strong class="text-warning">+221 77 532 37 25</strong>)</p>

        <!-- Galerie d'images agrandies et juxtaposées -->
        <div class="row justify-content-center g-3 mt-4">
            <?php
            $images_specifiques = ['logobest1.jpeg', 'logobest2.jpeg', 'logobest3.jpeg', 'tai.jpeg'];
            foreach ($images_specifiques as $img) {
                if (file_exists($img)) {
                    echo '
                    <div class="col-6 col-sm-3 col-md-3">
                        <div class="card bg-black shadow border border-danger h-100 p-1">
                            <img src="'.$img.'" alt="Aperçu '.htmlspecialchars($img).'" class="rounded img-fluid w-100" style="height: 160px; object-fit: cover;">
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- DASHBOARD STATISTIQUES & CHIFFRES CLÉS -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h5 class="card-title">Adhérents & QR</h5>
                    <h3><?= number_format($total_adherents, 0, ',', ' ') ?></h3>
                    <a href="adherents.php" class="text-white text-decoration-none small fw-bold">Gérer &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow-sm border border-danger py-3">
                <div class="card-body">
                    <i class="fas fa-comments fa-2x mb-2 text-warning"></i>
                    <h5 class="card-title">Avis / Note</h5>
                    <h3><?= number_format($moyenne_avis, 1) ?> / 5</h3>
                    <a href="avis_admin.php" class="text-warning text-decoration-none small fw-bold">Lire les avis (<?= $total_avis ?>) &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <h5 class="card-title">Produits / Boutique</h5>
                    <h3><?= number_format($total_produits, 0, ',', ' ') ?></h3>
                    <a href="produits.php" class="text-white text-decoration-none small fw-bold">Voir stocks &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow-sm border border-danger py-3">
                <div class="card-body">
                    <i class="fas fa-wallet fa-2x mb-2 text-danger"></i>
                    <h5 class="card-title">Chiffre d'Affaires</h5>
                    <h3><?= number_format($total_ca, 0, ',', ' ') ?> F</h3>
                    <a href="statistiques.php" class="text-danger text-decoration-none small fw-bold">Rapports &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ACCÈS RAPIDE AUX FONCTIONNALITÉS CLÉS -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5">
        <!-- 1. Gestion Adhérents & QR Code -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-primary bg-primary text-white text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-qrcode"></i></div>
                    <h3 class="card-title h5 fw-bold text-white">Adhérents & Envoi QR Code</h3>
                    <p class="card-text text-light">Inscrivez les membres, générez et envoyez automatiquement leurs QR codes d'accès par e-mail ou WhatsApp.</p>
                    <a href="adherents.php" class="btn btn-warning text-dark mt-2 fw-bold">Espace Adhérents</a>
                </div>
            </div>
        </div>
        <!-- 2. Borne de Saisie des Avis (Adhérent) -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-warning bg-warning text-dark text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-primary fs-1"><i class="fas fa-bullhorn"></i></div>
                    <h3 class="card-title h5 fw-bold text-dark">Borne Avis Adhérent</h3>
                    <p class="card-text text-dark">Ouvrez le module de suggestions (coaching, fonctionnement) accessible via scan de QR code.</p>
                    <a href="donner_avis.php" target="_blank" class="btn btn-primary text-white mt-2 fw-bold">Ouvrir la Borne</a>
                </div>
            </div>
        </div>
        <!-- 3. Lecture Responsable des Avis -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-danger bg-danger text-white text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-comments"></i></div>
                    <h3 class="card-title h5 fw-bold text-white">Lecture des Avis Responsable</h3>
                    <p class="card-text text-light">Consultez, filtrez et analysez les retours et suggestions postés par vos adhérents.</p>
                    <a href="avis_admin.php" class="btn btn-warning text-dark mt-2 fw-bold">Consulter les Avis</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Accès rapide secondaire -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-primary bg-dark text-white text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h3 class="card-title h5 fw-bold text-warning">Facturation & Stocks</h3>
                    <p class="card-text text-light">Générez des factures et contrôlez automatiquement la décrémentation des stocks de la boutique.</p>
                    <a href="factures.php" class="btn btn-outline-warning mt-2 fw-bold">Gérer les Factures</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-primary bg-dark text-white text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="card-title h5 fw-bold text-warning">Calendrier & Planning</h3>
                    <p class="card-text text-light">Consultez l'emploi du temps des cours, des sessions d'arts martiaux et des programmes de fitness.</p>
                    <a href="calendrier.php" class="btn btn-outline-warning mt-2 fw-bold">Voir le calendrier</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border border-primary bg-dark text-white text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-chalkboard-user"></i></div>
                    <h3 class="card-title h5 fw-bold text-warning">Formateurs / Coachs</h3>
                    <p class="card-text text-light">Gérez l'équipe encadrante (Contact Coach Moussa : +221 77 532 37 25) et le suivi des disciplines.</p>
                    <a href="formateurs.php" class="btn btn-outline-warning mt-2 fw-bold">Gérer les Coachs</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Diaporama des disciplines -->
    <div id="diaporamaDisciplines" class="carousel slide mb-5 shadow rounded overflow-hidden bg-black border border-danger" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            try {
                $stmt = $db->query("SELECT * FROM disciplines");
                $disciplines = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                $active = "active";

                if(empty($disciplines)) {
                    echo '<div class="carousel-item active p-5 text-white text-center">
                            <h3>Bienvenue chez Dabakh Fitness Wellness</h3>
                            <p>Découvrez nos disciplines adaptées pour enfants, adultes et femmes.</p>
                          </div>';
                } else {
                    foreach($disciplines as $d) {
                        $t_enf = isset($d['tarif_enfant']) ? number_format($d['tarif_enfant'], 0, ',', ' ') : 0;
                        $t_adu = isset($d['tarif_adulte']) ? number_format($d['tarif_adulte'], 0, ',', ' ') : 0;
                        $t_fem = isset($d['tarif_femme']) ? number_format($d['tarif_femme'], 0, ',', ' ') : 0;

                        echo '<div class="carousel-item '.$active.' p-5 text-white text-center">
                                <h2 class="fw-bold text-danger">'.htmlspecialchars($d['nom']).'</h2>
                                <p class="lead">'.htmlspecialchars($d['description']).'</p>
                                <div class="mt-3">
                                    <span class="badge bg-danger fs-6 me-2">Enfants : '.$t_enf.' F</span>
                                    <span class="badge bg-dark border border-danger fs-6 me-2">Adultes : '.$t_adu.' F</span>
                                    <span class="badge bg-light text-dark fs-6">Femmes : '.$t_fem.' F</span>
                                </div>
                              </div>';
                        $active = "";
                    }
                }
            } catch (Exception $e) {
                echo '<div class="carousel-item active p-5 bg-danger text-white text-center">
                        <h3>Bienvenue chez Dabakh Fitness Wellness</h3>
                        <p>Espace Arts Martiaux, Fitness & Musculation à Dakar.</p>
                      </div>';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#diaporamaDisciplines" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#diaporamaDisciplines" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>

    <!-- Section Calendriers & Événements -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card shadow-sm border border-primary bg-dark text-white">
                <div class="card-body p-4">
                    <h3 class="card-title mb-3 text-warning"><i class="fas fa-calendar-check text-warning"></i> Calendrier des Séances & Événements</h3>
                    <p class="text-light">Récapitulatif des cours de la semaine et des stages programmés à Sacré-Cœur 3 VDN (Contact Coach Moussa : +221 77 532 37 25).</p>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered border-primary align-middle">
                            <thead class="bg-primary text-warning">
                                <tr>
                                    <th>Jour</th>
                                    <th>Discipline</th>
                                    <th>Horaire</th>
                                    <th>Public Cible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lundi / Mercredi / Vendredi</td>
                                    <td>Arts Martiaux / Karaté / Boxe</td>
                                    <td>18h00 - 20h00</td>
                                    <td>Enfants & Adultes</td>
                                </tr>
                                <tr>
                                    <td>Mardi / Jeudi / Samedi</td>
                                    <td>Fitness / Cardio / Renforcement</td>
                                    <td>17h00 - 19h00</td>
                                    <td>Femmes & Mixtes</td>
                                </tr>
                                <tr>
                                    <td>Tous les jours</td>
                                    <td>Musculation Libre</td>
                                    <td>07h00 - 22h00</td>
                                    <td>Tous</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- INTÉGRATION GOOGLE MAPS OPTIMISÉE (ZOOM 18)-->
    <!-- ========================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border border-primary bg-dark text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h3 class="card-title mb-0 text-warning"><i class="fas fa-map-marker-alt text-danger"></i> Où nous trouver - Rue MZ 05 (Sacré-Cœur 3 VDN)</h3>
                        <a href="https://maps.app.goo.gl/t6Zu7BJnBv8dYnkg6?g_st=aw" target="_blank" class="btn btn-sm btn-danger fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> Ouvrir dans Google Maps
                        </a>
                    </div>
                    <p class="text-light">
                        Localisation précise : <strong class="text-warning">Rue MZ 05</strong> (à 40 mètres de la rue MZ 07, près des <strong class="text-warning">Résidences Mamoune</strong> et de l'<strong>école Les Petits Pas</strong>).
                        Contactez le <strong class="text-warning">Coach Moussa</strong> au <strong class="text-warning">+221 77 532 37 25</strong>.
                    </p>

                    <!-- Conteneur iframe avec zoom forcé (z=18) -->
                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm border border-warning">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15446.8!2d-17.4682!3d14.7185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zRGFiYWtoIEZpdG5lc3MgV2VsbG5lc3MgRGFrYXI!5e0!3m2!1sfr!2ssn!4v1710000000000!5m2!1sfr!2ssn&z=18"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
