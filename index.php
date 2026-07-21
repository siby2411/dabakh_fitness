<?php
// Activation du débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

try {
    $total_adherents = $db->query("SELECT COUNT(*) FROM adherents")->fetchColumn();
    $total_disciplines = $db->query("SELECT COUNT(*) FROM disciplines")->fetchColumn();
    $total_produits = $db->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    $total_ca = $db->query("SELECT SUM(montant) FROM paiements")->fetchColumn() ?: 0;
} catch (Exception $e) {
    // Tables potentiellement non initialisées
}
?>

<!-- ========================================== -->
<!-- MÉTADONNÉES SEO & GOOGLE SEARCH CONSOLE -->
<!-- ========================================== -->
<meta name="google-site-verification" content="VOTRE_CODE_VERIFICATION_GOOGLE_SEARCH_CONSOLE">
<meta name="description" content="Dabakh Fitness à Dakar : Salle de sport, arts martiaux et fitness située à Sacré-Cœur 3 VDN. Votre douleur d'aujourd'hui est votre énergie de demain.">
<meta name="keywords" content="salle de sport Dakar, fitness Sacré Coeur 3 VDN, arts martiaux Sénégal, Dabakh Fitness, musculation Dakar">
<meta name="author" content="Omega Informatique Consulting - Mohamet Siby">
<link rel="sitemap" type="application/xml" title="Sitemap" href="sitemap.xml">

<!-- Balisage Schema.org pour le référencement local Google (SEO Local) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SportsActivityLocation",
  "name": "Dabakh Fitness",
  "image": "https://dabakhfitness.sn/assets/img/logo.png",
  "email": "tagadat0@gmail.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Sacré-Cœur 3 VDN (Entre École Les Petits Pas et Résidences Mamoune)",
    "addressLocality": "Dakar",
    "addressCountry": "SN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 14.7167,
    "longitude": -17.4677
  },
  "url": "https://dabakhfitness.sn",
  "telephone": "+221000000000"
}
</script>

<div class="container my-5">
    
    <!-- En-tête avec Image locale fitness.jpeg et Slogan -->
    <div class="text-center mb-5">
        <div class="mb-3">
            <img src="fitness.jpeg" alt="Dabakh Fitness Centre" class="img-fluid rounded shadow-lg" style="max-height: 250px; width: 100%; object-fit: cover;" onerror="this.style.display='none'">
        </div>
        <h1 class="display-4 fw-bold text-uppercase text-dark mt-3">Dabakh Fitness - Dakar</h1>
        <p class="lead text-primary fw-semibold fst-italic">« Votre douleur d'aujourd'hui est votre énergie de demain »</p>
        <p class="text-muted">Centre de référence en Arts Martiaux & Fitness à Sacré-Cœur 3 VDN (Entre École Les Petits Pas et Résidences Mamoune)</p>
    </div>

    <!-- ========================================== -->
    <!-- DASHBOARD STATISTIQUES & CHIFFRES CLÉS -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h5 class="card-title">Adhérents</h5>
                    <h3><?= number_format($total_adherents, 0, ',', ' ') ?></h3>
                    <a href="adherents.php" class="text-white text-decoration-none small">Gérer &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-dumbbell fa-2x mb-2"></i>
                    <h5 class="card-title">Disciplines</h5>
                    <h3><?= number_format($total_disciplines, 0, ',', ' ') ?></h3>
                    <a href="disciplines.php" class="text-white text-decoration-none small">Configurer &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <h5 class="card-title">Produits / Boutique</h5>
                    <h3><?= number_format($total_produits, 0, ',', ' ') ?></h3>
                    <a href="produits.php" class="text-dark text-decoration-none small">Voir stocks &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow-sm border-0 py-3">
                <div class="card-body">
                    <i class="fas fa-wallet fa-2x mb-2"></i>
                    <h5 class="card-title">Chiffre d'Affaires</h5>
                    <h3><?= number_format($total_ca, 0, ',', ' ') ?> F</h3>
                    <a href="statistiques.php" class="text-white text-decoration-none small">Rapports &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Diaporama des disciplines (connecté dynamiquement à la base de données) -->
    <div id="diaporamaDisciplines" class="carousel slide mb-5 shadow rounded overflow-hidden bg-dark" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            try {
                $stmt = $db->query("SELECT * FROM disciplines");
                $disciplines = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                $active = "active";
                
                if(empty($disciplines)) {
                    echo '<div class="carousel-item active p-5 text-white text-center">
                            <h3>Bienvenue chez Dabakh Fitness</h3>
                            <p>Découvrez nos disciplines adaptées pour enfants, adultes et femmes.</p>
                          </div>';
                } else {
                    foreach($disciplines as $d) {
                        $t_enf = isset($d['tarif_enfant']) ? number_format($d['tarif_enfant'], 0, ',', ' ') : 0;
                        $t_adu = isset($d['tarif_adulte']) ? number_format($d['tarif_adulte'], 0, ',', ' ') : 0;
                        $t_fem = isset($d['tarif_femme']) ? number_format($d['tarif_femme'], 0, ',', ' ') : 0;
                        
                        echo '<div class="carousel-item '.$active.' p-5 text-white text-center">
                                <h2 class="fw-bold">'.htmlspecialchars($d['nom']).'</h2>
                                <p class="lead">'.htmlspecialchars($d['description']).'</p>
                                <div class="mt-3">
                                    <span class="badge bg-primary fs-6 me-2">Enfants : '.$t_enf.' F</span>
                                    <span class="badge bg-success fs-6 me-2">Adultes : '.$t_adu.' F</span>
                                    <span class="badge bg-info fs-6">Femmes : '.$t_fem.' F</span>
                                </div>
                              </div>';
                        $active = "";
                    }
                }
            } catch (Exception $e) {
                echo '<div class="carousel-item active p-5 bg-danger text-white text-center">
                        <h3>Bienvenue chez Dabakh Fitness</h3>
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

    <!-- Section Accès rapide aux fonctionnalités clés -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-primary fs-1"><i class="fas fa-user-plus"></i></div>
                    <h3 class="card-title h5 fw-bold">Gestion des Adhérents</h3>
                    <p class="card-text text-muted">Inscrivez de nouveaux membres, suivez leurs abonnements et accédez à leurs dossiers détaillés.</p>
                    <a href="adherents.php" class="btn btn-outline-primary mt-2">Espace Adhérents</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-success fs-1"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h3 class="card-title h5 fw-bold">Facturation & Stocks</h3>
                    <p class="card-text text-muted">Générez des factures et contrôlez automatiquement la décrémentation des stocks de la boutique.</p>
                    <a href="factures.php" class="btn btn-outline-success mt-2">Gérer les Factures</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-3">
                <div class="card-body">
                    <div class="mb-3 text-warning fs-1"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="card-title h5 fw-bold">Calendrier & Planning</h3>
                    <p class="card-text text-muted">Consultez l'emploi du temps des cours, des sessions d'arts martiaux et des programmes de fitness.</p>
                    <a href="calendrier.php" class="btn btn-outline-warning mt-2">Voir le calendrier</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Calendriers & Événements -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title mb-3"><i class="fas fa-calendar-check text-warning"></i> Calendrier des Séances & Événements</h3>
                    <p class="text-muted">Récapitulatif des cours de la semaine et des stages programmés à Sacré-Cœur 3 VDN.</p>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
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
    <!-- INTÉGRATION GOOGLE MAPS CORRIGÉE (IFRAME SECURISÉ) -->
    <!-- ========================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title mb-3"><i class="fas fa-map-marker-alt text-danger"></i> Où nous trouver à Dakar</h3>
                    <p class="text-muted">
                        Situé à <strong>Sacré-Cœur 3 VDN, Dakar, Sénégal</strong> (Exactement positionné entre l'<strong>école Les Petits Pas</strong> et les <strong>Résidences Mamoune</strong>).
                    </p>
                    
                    <!-- Conteneur iframe natif pour éviter toute erreur de clé API JavaScript manquante -->
                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.123456789!2d-17.4677!3d14.7167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xec1732123456789%3A0x123456789abcdef!2sSacre-Coeur%203%2C%20Dakar%2C%20Senegal!5e0!3m2!1sfr!2ssn!4v1710000000000!5m2!1sfr!2ssn" 
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
