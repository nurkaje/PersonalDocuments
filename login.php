<?php
session_start();
require_once "config/db.php";


$poruka = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM korisnici WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && md5($password) === $user['password']) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['uloga'] = $user['uloga'];

            if ($user['uloga'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $poruka = "❌ Pogrešno korisničko ime ili lozinka.";
        }
    } else {
        $poruka = "❗ Unesite sve podatke.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prijava</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>🔐 Prijava</h2>

    <?php if (!empty($poruka)): ?>
        <p class="message error"><?= $poruka ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="username">Korisničko ime:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Lozinka:</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" value="Prijavi se">
    </form>

    <a class="back-link" href="index.php">⬅️ Nazad na početnu</a><br>
    <a class="back-link" href="register.php">📋 Registruj se</a>
</div>
</body>
</html>
