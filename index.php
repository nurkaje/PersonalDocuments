<?php
session_start();
// Ako smo već prijavljeni, preusmjeri na odgovarajući dashboard
if (isset($_SESSION['uloga'])) {
    if ($_SESSION['uloga'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dobrodošli | Dokumenti App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

    <!-- Box 0: Naslov, podnaslov i Prijava/Registracija u jednom -->
<div class="document-box">
    <h2>📑 Dobrodošli u Dokumenti App</h2>
    <p class="subtitle">Upravljajte ličnim dokumentima brzo i jednostavno.</p>
    <div class="dashboard-actions">
        <a href="login.php" class="button">🔐 Prijavi se</a>
        <a href="register.php" class="button">📝 Registracija</a>
    </div>
</div>


    <!-- Box 2: Osnovne funkcionalnosti -->
    <div class="document-box">
        <h3>⚙️ Osnovne funkcionalnosti</h3>
        <ul>
            <li>Prijava i registracija korisnika (admin i korisnik)</li>
            <li>CRUD operacije za lične podatke (lična karta, pasoš, vozačka dozvola)</li>
            <li>Admin dashboard s upravljanjem korisnicima i zahtjevima</li>
            <li>Korisnički dashboard za pregled i zahtjev novih dokumenata</li>
            <li>Sigurnosna provjera uloga i sesije</li>
        </ul>
    </div>

    <!-- Box 3: Autori projekta -->
    <div class="document-box">
        <h3>👥 Radili</h3>
        <ul>
            <li>Student: </li>
            <li>Student: </li>
            <li>Student: </li>
            <!-- Dodajte po potrebi -->
        </ul>
    </div>

</div>
</body>
</html>
