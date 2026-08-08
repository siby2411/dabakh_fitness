<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

try {
    // 1. Afficher les triggers de la base de données
    echo "=== TRIGGERS DANS LA BASE DE DONNÉES ===\n";
    $stmt = $db->query("SHOW TRIGGERS");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($triggers)) {
        echo "Aucun trigger actif trouvé.\n";
    } else {
        foreach ($triggers as $trig) {
            echo "- Trigger : " . $trig['Trigger'] . " sur la table " . $trig['Table'] . " (Événement : " . $trig['Event'] . " " . $trig['Timing'] . ")\n";
        }
    }

    // 2. Vérifier si la table a une fonction de génération automatique (AUTO_INCREMENT ou autre)
    echo "\n=== STRUCTURE DE LA TABLE ADHERENTS ===\n";
    $stmt2 = $db->query("DESCRIBE adherents");
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'numero_licence') {
            echo "Champ numero_licence -> Type: " . $row['Type'] . " | Null: " . $row['Null'] . " | Default: " . ($row['Default'] ?? 'NULL') . "\n";
        }
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>
