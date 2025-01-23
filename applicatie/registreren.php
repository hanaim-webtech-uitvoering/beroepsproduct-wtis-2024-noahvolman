<?php
require_once 'db_connectie.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);

    $client_name = $first_name . ' ' . $last_name;
    $_SESSION['client_name'] = $client_name;
    $_SESSION['address'] = $address;

    if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name)) {
        // Hash het wachtwoord
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Verbinding met database
        $db = maakVerbinding();

        // Insert gebruiker
        $stmt = $db->prepare('INSERT INTO "User" (username, password, first_name, last_name, address, role) VALUES (?, ?, ?, ?, ?, ?)');
        $role = 'Client'; // Standaardrol
        try {
            $stmt->execute([$username, $hashed_password, $first_name, $last_name, $address, $role]);
            header('Location: inloggen.php');
            exit(); 
        } catch (Exception $e) {
            echo "Fout bij registratie: " . $e->getMessage();
        }
    } else {
        echo "Alle velden zijn verplicht!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
</head>
<body>
    <h1>Registreren</h1>
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
