<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'korisnik') {
    header("Location: ../login.php");
    exit();
}

$poruka = "";
$tip_poruke = ""; // success, error, info
$username = $_SESSION['username'];

// Dohvati jmbg iz osobe
$stmt = $conn->prepare("SELECT jmbg FROM osoba WHERE username = ?");
$stmt->execute([$username]);
$osoba = $stmt->fetch();

if (!$osoba) {
    $poruka = "Nema korisnika povezanog s ovim nalogom.";
    $tip_poruke = "error";
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tip = $_POST['tip_dokumenta'] ?? '';
    $jmbg = $osoba['jmbg'];

    // Provjera da li korisnik već ima taj dokument
    $postoji = false;

    switch ($tip) {
        case 'licna_karta':
            $stmt = $conn->prepare("SELECT * FROM licna_k WHERE jmbg = ?");
            break;
        case 'pasos':
            $stmt = $conn->prepare("SELECT * FROM pasos WHERE jmbg = ?");
            break;
        case 'vozacka':
            $stmt = $conn->prepare("SELECT * FROM vozacka_doz WHERE jmbg = ?");
            break;
        default:
            $poruka = "Nepoznat tip dokumenta.";
            $tip_poruke = "error";
            $stmt = null;
    }

    if ($stmt) {
        $stmt->execute([$jmbg]);
        if ($stmt->rowCount() > 0) {
            $postoji = true;
        }
    }

    if ($postoji) {
        $poruka = "❌ DOKUMENT POSTOJI. Ne možete tražiti isti dokument ponovo.";
        $tip_poruke = "error";
    } else {
        // Provjeri da li je već poslan zahtjev za taj tip
        $stmt = $conn->prepare("SELECT * FROM status WHERE jmbg = ? AND tip_dokumenta = ?");
        $stmt->execute([$jmbg, $tip]);
        if ($stmt->fetch()) {
            $poruka = "ℹ️ Već ste poslali zahtjev za ovaj dokument.";
            $tip_poruke = "info";
        } else {
            // Dodaj zahtjev u status tabelu
            $datum = date("Y-m-d");
            $stmt = $conn->prepare("INSERT INTO status (jmbg, tip_dokumenta, datum_podnosenja) VALUES (?, ?, ?)");
            $stmt->execute([$jmbg, $tip, $datum]);
            $poruka = "✅ Zahtjev uspješno poslan.";
            $tip_poruke = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zatraži dokument</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>📝 Zatraži novi dokument</h2>

        <?php if (!empty($poruka)): ?>
            <p class="message <?= $tip_poruke ?>"><?= $poruka ?></p>
        <?php endif; ?>

        <form method="post" class="document-form">
            <label>Odaberi dokument koji želiš:</label><br>
            <select name="tip_dokumenta" required>
                <option value="">-- Odaberi --</option>
                <option value="licna_karta">Lična karta</option>
                <option value="pasos">Pasoš</option>
                <option value="vozacka">Vozačka dozvola</option>
            </select><br><br>

            <input type="submit" value="Pošalji zahtjev">
        </form>

        <br>
        <a class="back-link" href="dashboard.php">⬅️ Nazad</a>
    </div>
</body>
</html>
