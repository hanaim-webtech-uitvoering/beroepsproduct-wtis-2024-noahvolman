<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    echo 'Toegang geweigerd. Je moet ingelogd zijn om deze pagina te bekijken.';
    exit();
}

$username = $_SESSION['username'];

// Haal alle bestellingen van de ingelogde gebruiker op
$db = maakVerbinding();
$stmt = $db->prepare('SELECT P.order_id, P.status, P.address, P.datetime FROM Pizza_Order P WHERE P.client_username = ?');
$stmt->execute([$username]);
$order_details = $stmt->fetchAll();

function getStatusName($status) {
    switch ($status) {
        case 1:
            return 'wordt verwerkt';
        case 2:
            return 'bezig';
        case 3:
            return 'voltooid';
        default:
            return 'onbekend';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemaakte Bestellingen</title>
    <style>
        table, td, th { border: 1px solid black; padding: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; }
    </style>
</head>
<body>
    <nav>
        <a href="menu.php">Menu</a>
        <a href="gemaakteBestellingen.php">Gemaakte Bestellingen</a>
        <a href="logout.php">Uitloggen</a>
    </nav>
    <h1>Gemaakte Bestellingen</h1>
    <?php
    if (!empty($order_details)) {
        echo '<table>';
        echo '<tr><th>Order ID</th><th>Status</th><th>Adres</th><th>Datum en Tijd</th><th>Details</th></tr>';
        foreach ($order_details as $detail) {
            $status_name = getStatusName($detail['status']);
            echo '<tr>';
            echo '<td>' . htmlspecialchars($detail['order_id']) . '</td>';
            echo '<td>' . htmlspecialchars($status_name) . '</td>';
            echo '<td>' . htmlspecialchars($detail['address']) . '</td>';
            echo '<td>' . htmlspecialchars($detail['datetime']) . '</td>';
            echo '<td><a href="detailOverzicht.php?order_id=' . htmlspecialchars($detail['order_id']) . '">Bekijk Details</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Geen bestellingen gevonden.</p>';
    }
    ?>
</body>
</html>