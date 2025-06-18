<!korisnicki panel gdje ispise Dobrodosao, i gdje pise koja je uloga korisnika, i gdje su linkovi na podaci.php i zatrazidokument.php-->

<?php
session_start();

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'korisnik') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Korisnički Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h2>👤 Dobrodošli, <?php echo htmlspecialchars($username); ?>!</h2>
    <p class="subtitle">Ovdje možete upravljati svojim ličnim dokumentima.</p>

    <div class="dashboard-actions">
        <a href="podaci.php" class="button">📄 Moji dokumenti</a>
        <a href="zatrazidokument.php" class="button">➕ Zatraži dokument</a>
        <a href="../logout.php" class="button logout">🚪 Odjava</a>
    </div>
</div>
</body>
</html>

