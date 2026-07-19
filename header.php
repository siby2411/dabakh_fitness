<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dabakh Fitness - Gestion Salle de Sport</title>
    
    <!-- CSS Corrigés -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="logo-container">
                    <i class="fas fa-fist-raised logo-icon"></i>
                    <span class="logo-text">Dabakh <span class="logo-highlight">Fitness</span></span>
                    <small class="logo-tagline">Arts Martiaux & Fitness</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="adherents.php"><i class="fas fa-users"></i> Adhérents</a></li>
                    <li class="nav-item"><a class="nav-link" href="formateurs.php"><i class="fas fa-chalkboard-user"></i> Formateurs</a></li>
                    <li class="nav-item"><a class="nav-link" href="calendrier.php"><i class="fas fa-calendar-alt"></i> Calendrier</a></li>
                    <li class="nav-item"><a class="nav-link" href="paiements.php"><i class="fas fa-money-bill-wave"></i> Paiements</a></li>
                    
                    <!-- Menu Rapports Déroulant -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRapports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-alt"></i> Rapports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownRapports">
                            <li><a class="dropdown-item" href="stats.php?type=paiements_adherent">Paiements Adhérent</a></li>
                            <li><a class="dropdown-item" href="stats.php?type=paiements_discipline">Paiements Discipline</a></li>
                            <li><a class="dropdown-item" href="stats.php?type=rentabilite">Rentabilité</a></li>
                        </ul>
                    </li>

                    <!-- Menu Boutique - Version Forcée -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           id="navbarDropdownBoutique" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                           <i class="fas fa-shopping-cart"></i> Boutique
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownBoutique">
                            <li><a class="dropdown-item" href="produits.php">Gestion Produits</a></li>
                            <li><a class="dropdown-item" href="factures.php">Nouvelle Facture</a></li>
                            <li><a class="dropdown-item" href="stats_boutique.php">États Financiers</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="fas fa-bell"></i> Actus</a></li>
                    <li class="nav-item"><a class="nav-link" href="statistiques.php"><i class="fas fa-chart-line"></i> Stats</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Script Bootstrap requis pour le menu mobile et les menus déroulants -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- CDN de secours au cas où le fichier local ne se charge pas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <div class="main-content" style="margin-top: 100px;">
