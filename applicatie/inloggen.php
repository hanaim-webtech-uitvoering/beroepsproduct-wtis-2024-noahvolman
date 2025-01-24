<?php
session_start();
require_once 'db_connectie.php';

// Genereer een CSRF-token als deze nog niet bestaat
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Ongeldige CSRF-token');
    }

    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $db = maakVerbinding();

        // Zoek gebruiker op basis van username
        $stmt = $db->prepare('SELECT username, password, first_name, last_name, address, role FROM "User" WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Wachtwoord klopt
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['client_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['address'] = $user['address'];

            if ($user['role'] == 'Client') {
                header('Location: menu.php');
                exit();
            } else {
                header('Location: bestellingOverzicht.php');
                exit();
            }
        } else {
            $error_message = "Ongeldige gebruikersnaam of wachtwoord.";
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
    <title>Inloggen</title>
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
    <h1>Inloggen</h1>
    <?php
    if (isset($error_message)) {
        echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label>Gebruikersnaam: <input type="text" name="username" required></label><br>
        <label>Wachtwoord: <input type="password" name="password" required></label><br>
        <button type="submit">Inloggen</button>
    </form>
    <p>Nog geen account? <a href="registreren.php">Registreren</a> of <a href="menu.php">ga verder als gast</a></p>
    <p>lees <a href="privacy.php">hier</a> onze privacyverklaring </p>
</body>
</html>