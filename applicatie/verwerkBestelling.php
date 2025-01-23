<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'gast';
    $_SESSION['client_name'] = 'Gast Gebruiker';
}

$username = $_SESSION['username'];
$client_name = $_SESSION['client_name'];
$address = $_SESSION['address'];
$bestelling = $_SESSION['bestelling'];
$totaal_bedrag = 0;

foreach ($bestelling as $product => $details) {
    $totaal_bedrag += $details['aantal'] * $details['prijs'];
}

$db = maakVerbinding();

try {
    // Begin een transactie
    $db->beginTransaction();

    // Voeg de bestelling toe aan de Pizza_Order tabel
    $stmt = $db->prepare('INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$username , $client_name, 'abrouwer', date('Y-m-d H:i:s'), 1, $address]);
    $order_id = $db->lastInsertId();

    // Voeg de producten toe aan de Pizza_Order_Product tabel
    $stmt = $db->prepare('INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) VALUES (?, ?, ?)');
    foreach ($bestelling as $product => $details) {
        $stmt->execute([$order_id, $product, $details['aantal']]);
    }

    // Commit de transactie
    $db->commit();

    // Leeg de bestelling in de sessie
    unset($_SESSION['bestelling']);

    echo 'Bestelling succesvol geplaatst!';
} catch (Exception $e) {
    // Rol de transactie terug bij een fout
    $db->rollBack();
    echo 'Fout bij het plaatsen van de bestelling: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Verwerken</title>
</head>
<body>
    <a href="menu.php">Terug naar menu</a>
</body>
</html>