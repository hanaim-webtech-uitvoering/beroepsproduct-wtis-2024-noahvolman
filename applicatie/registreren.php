<?php
require_once 'db_connectie.php';
session_start();

function valideerWachtwoord($wachtwoord) {
    // Wachtwoord moet minimaal 10 tekens lang zijn, een hoofdletter, een kleine letter, een cijfer en een speciaal teken bevatten
    $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{10,}$/';
    return preg_match($regex, $wachtwoord);
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $address = sanitizeInput($_POST['address']);

    if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name) && !empty($address)) {
        if (valideerWachtwoord($password)) {
            // Verbinding met database
            $db = maakVerbinding();

            // Controleer of de gebruikersnaam al bestaat
            $stmt = $db->prepare('SELECT COUNT(*) FROM "User" WHERE username = ?');
            $stmt->execute([$username]);
            $user_exists = $stmt->fetchColumn();

            if ($user_exists) {
                $error_message = "Gebruikersnaam bestaat al. Kies een andere gebruikersnaam.";
            } else {
                // Hash het wachtwoord
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert gebruiker
                $stmt = $db->prepare('INSERT INTO "User" (username, password, first_name, last_name, address, role) VALUES (?, ?, ?, ?, ?, ?)');
                $role = 'Client'; // Standaardrol
                try {
                    $stmt->execute([$username, $hashed_password, $first_name, $last_name, $address, $role]);
                    header('Location: inloggen.php');
                    exit(); 
                } catch (Exception $e) {
                    echo "Fout bij registratie: " . htmlspecialchars($e->getMessage());
                }
            }
        } else {
            $error_message = "Wachtwoord moet minimaal 8 tekens lang zijn, een hoofdletter, een kleine letter, een cijfer en een speciaal teken bevatten.";
        }
    } else {
        $error_message = "Alle velden zijn verplicht!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        p {
            line-height: 1.6;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Registreren</h1>
    <?php
    if (isset($error_message)) {
        echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>
    <form method="post">
        <label>Voornaam: <input type="text" name="first_name" required></label><br>
        <label>Achternaam: <input type="text" name="last_name" required></label><br>
        <label>Adres: <input type="text" name="address" required></label><br>
        <label>Gebruikersnaam: <input type="text" name="username" required></label><br>
        <label>Wachtwoord: <input type="password" name="password" required></label><br>
        <button type="submit">Registreren</button>
    </form>
    <p>Heb je al een account? <a href="inloggen.php">Inloggen</a></p>
    <p>lees <a href="privacy.php">hier</a> onze privacyverklaring </p>
</body>
</html>