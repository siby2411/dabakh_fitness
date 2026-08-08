<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';

$db = (new Database())->getConnection();

try {
    // 1. Désactivation temporaire des contraintes
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    // 2. Vider les tables proprement
    $db->exec("TRUNCATE TABLE avis_adherents");
    $db->exec("TRUNCATE TABLE adherents");

    // 3. Réactivation des contraintes
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 4. Repeupler la table adherents en respectant tous les champs obligatoires (NOT NULL sans défaut)
    $stmt = $db->prepare("INSERT INTO adherents (numero_licence, nom, prenom, email, telephone, statut) VALUES (?, ?, ?, ?, ?, ?)");
    
    $fictifs = [
        ['LIC-2026-001', 'Diop', 'Mamadou', 'mamadou.diop@example.com', '771234567', 'actif'],
        ['LIC-2026-002', 'Ndiaye', 'Fatou', 'fatou.ndiaye@example.com', '772345678', 'actif'],
        ['LIC-2026-003', 'Sow', 'Abdoulaye', 'abdoulaye.sow@example.com', '773456789', 'actif'],
        ['LIC-2026-004', 'Fall', 'Aissatou', 'aissatou.fall@example.com', '774567890', 'actif'],
        ['LIC-2026-005', 'Ba', 'Ibrahima', 'ibrahima.ba@example.com', '775678901', 'actif']
    ];

    foreach ($fictifs as $adh) {
        $stmt->execute($adh);
    }

    echo "Succès : La table adherents a été vidée et repeuplée avec 5 nouveaux profils conformes.\n";

} catch (Exception $e) {
    try { $db->exec("SET FOREIGN_KEY_CHECKS = 1"); } catch (Exception $ex) {}
    echo "Erreur lors de la réinitialisation : " . $e->getMessage() . "\n";
}
?>
