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

// Verwerk adresinvoer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'])) {
    $address = trim($_POST['address']);
    if (!empty($address)) {
        $_SESSION['address'] = $address;
        header('Location: menu.php');
        exit();
    } else {
        $address_error = "Adres is verplicht.";
    }
}

// Verwerk formulierinvoer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product'], $_POST['aantal'])) {
        // Controleer of het adres is ingesteld
        if (!isset($_SESSION['address'])) {
            $address_error = "Je moet eerst je adres invoeren voordat je een bestelling kunt plaatsen.";
        } else {
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
                    $_SESSION['bestelling'][$product]['aantal'] += $aantal;
                } else {
                    $_SESSION['bestelling'][$product] = ['aantal' => $aantal, 'prijs' => $prijs];
                }
                // Redirect naar dezelfde pagina om het POST-verzoek te voorkomen bij het vernieuwen
                header('Location: menu.php');
                exit();
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
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; }
    </style>
</head>
<body>
<nav>
        <?php if ($_SESSION['role'] !== 'guest'): ?>
            <a href="menu.php">Menu</a>
            <a href="gemaakteBestellingen.php">Mijn Bestellingen</a>
        <?php endif; ?>
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
 // Toon het adresformulier als het adres niet is ingesteld
 if (!isset($_SESSION['address'])) {
    echo '<h2>Voer je adres in om een bestelling te plaatsen</h2>';
    if (isset($address_error)) {
        echo '<p style="color:red;">' . htmlspecialchars($address_error) . '</p>';
    }
    echo '<form method="post">
            <label>Adres: <input type="text" name="address" required></label>
            <button type="submit">Opslaan</button>
          </form>';
}
?>
</body>
</html>
