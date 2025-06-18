<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'korisnik') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];

// Dohvati JMBG korisnika
$stmt = $conn->prepare("SELECT * FROM osoba WHERE username = ?");
$stmt->execute([$username]);
$osoba = $stmt->fetch();

if (!$osoba) {
    echo "Greška: Nema podataka za korisnika.";
    exit();
}

$jmbg = $osoba['jmbg'];
$ime = $osoba['ime'];
$prezime = $osoba['prezime'];

// Dokumenti
$licna = $conn->prepare("SELECT * FROM licna_k WHERE jmbg = ?");
$licna->execute([$jmbg]);
$licna_data = $licna->fetch();

$pasos = $conn->prepare("SELECT * FROM pasos WHERE jmbg = ?");
$pasos->execute([$jmbg]);
$pasos_data = $pasos->fetch();

$vozacka = $conn->prepare("SELECT * FROM vozacka_doz WHERE jmbg = ?");
$vozacka->execute([$jmbg]);
$vozacka_data = $vozacka->fetch();

// Zahtjevi u toku
$stmt = $conn->prepare("SELECT tip_dokumenta FROM status WHERE jmbg = ?");
$stmt->execute([$jmbg]);
$zahtjevi = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moji dokumenti</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
    <style>
        .info-button {
            padding: 4px 8px;
            font-size: 14px;
            margin-left: 10px;
            cursor: pointer;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
        }
        .info-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleInfo(id) {
            const el = document.getElementById(id);
            el.style.display = (el.style.display === 'none') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h2>📁 Moji dokumenti</h2>

    <div class="document-box">
        <h3>📌 Podaci o korisniku <button class="info-button" onclick="toggleInfo('info-korisnik')">ℹ️ Info</button></h3>
        <p><strong>Ime:</strong> <?= htmlspecialchars($osoba['ime']) ?></p>
        <p><strong>Prezime:</strong> <?= htmlspecialchars($osoba['prezime']) ?></p>
        <div id="info-korisnik" style="display:none">
            <p><strong>JMBG:</strong> <?= $osoba['jmbg'] ?></p>
            <p><strong>Datum rođenja:</strong> <?= $osoba['datum_rodjenja'] ?></p>
            <p><strong>Državljanstvo:</strong> <?= $osoba['id_drzavljanstva'] ?></p>
            <p><strong>Općina rođenja:</strong> <?= $osoba['opcina_rodjenja'] ?></p>
            <p><strong>Općina prebivališta:</strong> <?= $osoba['opcina_prebivalista'] ?></p>
            <p><strong>Potpis:</strong> <?= $osoba['potpis'] ?></p>
        </div>
    </div>

    <div class="document-box">
        <h3>🪪 Lična karta <button class="info-button" onclick="toggleInfo('info-licna')">ℹ️ Info</button></h3>
        <?php if ($licna_data): ?>
            <p><strong>Broj:</strong> <?= $licna_data['broj_licnek'] ?></p>
            <div id="info-licna" style="display:none">
                <p><strong>Datum izdavanja:</strong> <?= $licna_data['datum_izdavanja'] ?></p>
                <p><strong>Datum isteka:</strong> <?= $licna_data['datum_isteka'] ?></p>
                <p><strong>Općina:</strong> <?= $licna_data['opcina_prebivalista'] ?></p>
                <p><strong>Potpis:</strong> <?= $licna_data['potpis'] ?></p>
            </div>
        <?php elseif (in_array('licna_karta', $zahtjevi)): ?>
            <p>📨 Zahtjev za ličnu kartu je predat.</p>
        <?php else: ?>
            <p>❌ Nemate ličnu kartu.</p>
        <?php endif; ?>
    </div>

    <div class="document-box">
        <h3>🛂 Pasoš <button class="info-button" onclick="toggleInfo('info-pasos')">ℹ️ Info</button></h3>
        <?php if ($pasos_data): ?>
            <p><strong>Broj:</strong> <?= $pasos_data['broj_pasosa'] ?></p>
            <div id="info-pasos" style="display:none">
                <p><strong>Datum izdavanja:</strong> <?= $pasos_data['datum_izdavanja'] ?></p>
                <p><strong>Datum isteka:</strong> <?= $pasos_data['datum_isteka'] ?></p>
                <p><strong>Općina:</strong> <?= $pasos_data['opcina_prebivalista'] ?></p>
                <p><strong>Potpis:</strong> <?= $pasos_data['potpis'] ?></p>
            </div>
        <?php elseif (in_array('pasos', $zahtjevi)): ?>
            <p>📨 Zahtjev za pasoš je predat.</p>
        <?php else: ?>
            <p>❌ Nemate pasoš.</p>
        <?php endif; ?>
    </div>

    <div class="document-box">
        <h3>🚗 Vozačka dozvola <button class="info-button" onclick="toggleInfo('info-vozacka')">ℹ️ Info</button></h3>
        <?php if ($vozacka_data): ?>
            <p><strong>Broj:</strong> <?= $vozacka_data['broj_vozacke'] ?></p>
            <div id="info-vozacka" style="display:none">
                <p><strong>Datum izdavanja:</strong> <?= $vozacka_data['datum_izdavanja'] ?></p>
                <p><strong>Datum isteka:</strong> <?= $vozacka_data['datum_isteka'] ?></p>
                <p><strong>Općina:</strong> <?= $vozacka_data['opcina_prebivalista'] ?></p>
                <p><strong>Kategorija:</strong> <?= $vozacka_data['kategorija'] ?></p>
                <p><strong>Potpis:</strong> <?= $vozacka_data['potpis'] ?></p>
            </div>
        <?php elseif (in_array('vozacka', $zahtjevi)): ?>
            <p>📨 Zahtjev za vozačku dozvolu je predat.</p>
        <?php else: ?>
            <p>❌ Nemate vozačku dozvolu.</p>
        <?php endif; ?>
    </div>

    <br>
    <a href="zatrazidokument.php" class="back-link">➕ Zatraži novi dokument</a><br>
    <a href="dashboard.php" class="back-link">⬅️ Nazad</a>
</div>
</body>
</html>
