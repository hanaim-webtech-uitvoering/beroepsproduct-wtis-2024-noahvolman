<?php
session_start();
require_once 'db_connectie.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $db = maakVerbinding();

        // Zoek gebruiker op basis van username
        $stmt = $db->prepare('SELECT username, password, role FROM "User" WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Wachtwoord klopt
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo "Welkom, " . htmlspecialchars($user['username']) . "! Je bent ingelogd als een " . htmlspecialchars($user['role']) . ".";
        if($user['role'] == 'Client'){
            header('Location: menu.php');
            exit();
        } else {
            header('Location: bestellingOverzicht.php');
            exit();
        }
        } else {
            echo "Ongeldige gebruikersnaam of wachtwoord.";
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
    <title>Inloggen</title>
</head>
<body>
    <h1>Inloggen</h1>
    <form method="post">
        <label>Gebruikersnaam: <input type="text" name="username" required></label><br>
        <label>Wachtwoord: <input type="password" name="password" required></label><br>
        <button type="submit">Inloggen</button>
    </form>
    <p>Nog geen account? <a href="registreren.php">Registreren</a></p>
</body>
</html>
