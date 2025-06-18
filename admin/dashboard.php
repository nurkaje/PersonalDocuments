<?php
session_start();

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h2>🛠️ Dobrodošli, admin <?php echo htmlspecialchars($username); ?>!</h2>
    <p class="subtitle">Pregled i upravljanje korisnicima i dokumentima.</p>

    <div class="dashboard-actions">
        <a href="upravljanje_osobama.php" class="button">👥 Upravljanje osobama</a>
        <a href="upravljanje_statusima.php" class="button">📨 Zahtjevi korisnika</a>
        <a href="upravljanje_dokumentima.php" class="button">📄 Upravljanje dokumentima korisnika</a>
        <a href="grupno_brisanje.php" class="button">👥 Grupno brisanje dokumenata</a>
        <a href="../logout.php" class="button logout">🚪 Odjava</a>
    </div>
</div>
</body>
</html>
