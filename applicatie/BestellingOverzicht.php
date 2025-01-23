<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de gebruiker personeel is
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Personnel') {
    echo 'Toegang geweigerd. Alleen personeel kan deze pagina bekijken.';
    exit();
}

// Verwerk formulierinvoer voor status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $db = maakVerbinding();
    $stmt = $db->prepare('UPDATE Pizza_Order SET status = ? WHERE order_id = ?');
    $stmt->execute([$status, $order_id]);
}

// Haal alle bestellingsdetails op
$db = maakVerbinding();
$query = 'SELECT order_id,status FROM Pizza_Order';
$data = $db->query($query);
$order_details = $data->fetchAll();

function getStatusName($status) {
    switch ($status) {
        case 1:
            return 'in afwachting';
        case 2:
            return 'bezig';
        case 3:
            return 'voltooid';
        default:
            return 'Unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Overzicht</title>
    <style>
        table, td, th { border: 1px solid black; padding: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; }
    </style>
</head>
<body>
<nav>
    <a href="bestellingOverzicht.php">Bestelling Overzicht</a>
    <a href="logout.php">Uitloggen</a>
</nav>

    <h1>Bestelling Overzicht</h1>
    <?php
    if (!empty($order_details)) {
        echo '<table>';
        echo '<tr><th>Order ID</th><th>Status</th><th>verander status</th><th>Details</th></tr>';
        foreach ($order_details as $detail) {
            $status_name = getStatusName($detail['status']);
            echo '<tr>';
            echo '<td>' . htmlspecialchars($detail['order_id']) . '</td>';
            echo '<td>' . htmlspecialchars($status_name) . '</td>';
            echo '<td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="' . htmlspecialchars($detail['order_id']) . '">
                    <select name="status">
                        <option value="1"' . ($detail['status'] === 1 ? ' selected' : '') . '>in afwachting</option>
                        <option value="2"' . ($detail['status'] === 2 ? ' selected' : '') . '>bezig</option>
                        <option value="3"' . ($detail['status'] === 3 ? ' selected' : '') . '>voltooid</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>';
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