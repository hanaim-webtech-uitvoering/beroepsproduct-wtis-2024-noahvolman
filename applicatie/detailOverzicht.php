<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    echo 'Toegang geweigerd. Je moet ingelogd zijn om deze pagina te bekijken.';
    exit();
}

if (!isset($_GET['order_id'])) {
    echo 'Geen order ID opgegeven.';
    exit();
}

$order_id = (int)$_GET['order_id'];

// Haal alle bestellingsdetails op
$db = maakVerbinding();
$stmt = $db->prepare('SELECT P.order_id, PP.product_name, PP.quantity, P.address, P.datetime FROM Pizza_Order P JOIN Pizza_Order_Product PP ON P.order_id = PP.order_id WHERE P.order_id = ?');
$stmt->execute([$order_id]);
$order_details = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bestellingsdetails</title>
    <style>
        table, td, th { border: 1px solid black; padding: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Details van de bestelling met order ID <?php echo htmlspecialchars($order_id); ?></h1>
    <?php
    if (!empty($order_details)) {
        echo '<table>';
        echo '<tr><th>Product</th><th>Hoeveelheid</th><th>Adres</th><th>Aangemaakt op:</th></tr>';
        foreach ($order_details as $detail) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($detail['product_name']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['address']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['datetime']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Geen details gevonden voor deze bestelling.</p>';
    }
   // Toon de juiste link op basis van de rol van de gebruiker
   if (isset($_SESSION['role']) && $_SESSION['role'] === 'Personnel') {
    echo '<p><a href="bestellingOverzicht.php">Terug naar bestelling overzicht</a></p>';
} else {
    echo '<p><a href="gemaakteBestellingen.php">Ga terug naar je bestellingen</a></p>';
}
?>
</body>
</html>