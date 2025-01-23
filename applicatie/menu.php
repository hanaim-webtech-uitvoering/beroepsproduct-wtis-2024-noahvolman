<?php
require_once 'db_connectie.php';
session_start();

// Controleer of de bestel-sessie bestaat, anders initialiseer je deze
if (!isset($_SESSION['bestelling'])) {
    $_SESSION['bestelling'] = [];
}

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
}

// Verwerk formulierinvoer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product'], $_POST['aantal'])) {
    $product = $_POST['product'];
    $aantal = (int)$_POST['aantal'];

    if ($aantal > 0) {

             // Haal de prijs van het product op
             $db = maakVerbinding();
             $stmt = $db->prepare('SELECT price FROM Product WHERE name = ?');
             $stmt->execute([$product]);
             $result = $stmt->fetch();
             $prijs = $result['price'];

        // Voeg het product toe aan de bestelling
        if (isset($_SESSION['bestelling'][$product])) {
            $_SESSION['bestelling'][$product] += $aantal;
        } else {
            $_SESSION['bestelling'][$product] = ['aantal' => $aantal, 'prijs' => $prijs];
        }
    }
}

// Haal menu-items op uit de database
$db = maakVerbinding();
$query = 'SELECT name AS product, price AS prijs FROM Product ORDER BY type_id DESC';
$data = $db->query($query);

$html_table = '<table>';
$html_table .= '<tr><th>Product</th><th>Prijs</th><th>Aantal</th></tr>';

while ($rij = $data->fetch()) {
    $product = htmlspecialchars($rij['product']);
    $prijs = htmlspecialchars($rij['prijs']);

    // Formulier voor toevoegen aan bestelling
    $html_table .= "<tr>
        <td>$product</td>
        <td>€$prijs</td>
        <td>
            <form method='post'>
                <input type='hidden' name='product' value='$product'>
                <input type='number' name='aantal' min='1' value='1' style='width: 50px;'>
                <button type='submit'>Toevoegen</button>
            </form>
        </td>
    </tr>";
}

$html_table .= '</table>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
        table, td, th { border: 1px solid black; padding: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; }
    </style>
</head>
<body>
<nav>
        <a href="menu.php">Menu</a>
        <a href="gemaakteBestellingen.php">Mijn Bestellingen</a>
        <a href="logout.php">Uitloggen</a>
    </nav>
    <h1>Menu</h1>
    <?php echo $html_table; ?>

    <h2>Jouw Bestelling</h2>
    <?php
    if (!empty($_SESSION['bestelling'])) {
        echo '<ul>';
        $totaal_bedrag = 0;
        foreach ($_SESSION['bestelling'] as $product => $details) {
            $aantal = $details['aantal'];
            $prijs = $details['prijs'];
            $bedrag = $aantal * $prijs;
            $totaal_bedrag += $bedrag;
            echo "<li>$product: $aantal x €$prijs = €$bedrag</li>";
        }
        echo "<li><strong>Totaal: €$totaal_bedrag</strong></li>";
        echo '</ul>';
        echo '<form method="post" action="verwerkBestelling.php">
                <button type="submit">Bestelling Plaatsen</button>
              </form>';
    } else {
        echo '<p>Je hebt nog niets toegevoegd aan je bestelling.</p>';
    }
    ?>
</body>
</html>
