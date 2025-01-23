<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de gebruiker personeel is
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Personnel') {
    echo 'Toegang geweigerd. Alleen personeel kan deze pagina bekijken.';
    exit();
}

// Haal alle bestellingsdetails op
$db = maakVerbinding();
$query = 'SELECT P.order_id, PP.product_name, PP.quantity, P.address, P.datetime FROM Pizza_Order P JOIN Pizza_Order_Product PP ON P.order_id = PP.order_id WHERE P.order_id = :order_id';
$data = $db->query($query);
$order_details = $data->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Overzicht</title>
    <style>
        table, td, th { border: 1px solid black; padding: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; }
    </style>
</head>
<body>
    <h1>Detail Overzicht van Bestellingen</h1>
    <?php
    if (!empty($order_details)) {
        echo '<table>';
        echo '<tr><th>Order ID</th><th>Product Naam</th><th>Aantal</th><th>Adres</th></tr>';
        foreach ($order_details as $detail) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($detail['order_id']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['product_name']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['address']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Geen bestellingen gevonden.</p>';
    }
    ?>
</body>
</html>