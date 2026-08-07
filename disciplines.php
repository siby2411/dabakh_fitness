<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $t_enfant = $_POST['tarif_enfant'];
    $t_adulte = $_POST['tarif_adulte'];
    $t_femme = $_POST['tarif_femme'];

    $stmt = $db->prepare("INSERT INTO disciplines (nom, description, tarif_enfant, tarif_adulte, tarif_femme) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nom, $description, $t_enfant, $t_adulte, $t_femme])) {
        $message = "<div class='alert alert-success'>Discipline ajoutée avec succès ! Elle apparaîtra sur le diaporama de l'accueil.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Erreur lors de l'enregistrement.</div>";
    }
}

$disciplines = $db->query("SELECT * FROM disciplines")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Gestion des Disciplines & Tarifs par Catégorie</h2>
    <?= $message ?>
    
    <form method="POST" class="card p-4 shadow-sm mb-5">
        <div class="mb-3">
            <label class="form-label">Nom de la Discipline</label>
            <input type="text" name="nom" class="form-control" required placeholder="Ex: Karaté, Fitness, Cardio...">
        </div>
        <div class="mb-3">
            <label class="form-label">Description (pour le diaporama)</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Tarif Enfants (F)</label>
                <input type="number" name="tarif_enfant" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tarif Adultes (F)</label>
                <input type="number" name="tarif_adulte" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tarif Femmes (F)</label>
                <input type="number" name="tarif_femme" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer la discipline</button>
    </form>

    <h3>Disciplines Actives (Alimentant le Diaporama)</h3>
    <div class="row">
        <?php foreach($disciplines as $d): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= htmlspecialchars($d['nom']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($d['description']) ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Enfants : <strong><?= number_format($d['tarif_enfant'], 0, ',', ' ') ?> F</strong></li>
                            <li class="list-group-item">Adultes : <strong><?= number_format($d['tarif_adulte'], 0, ',', ' ') ?> F</strong></li>
                            <li class="list-group-item">Femmes : <strong><?= number_format($d['tarif_femme'], 0, ',', ' ') ?> F</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
