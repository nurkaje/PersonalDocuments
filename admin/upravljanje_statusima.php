<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$poruka = "";

// Rukovanje akcijama
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $akcija = $_POST['akcija'] ?? '';
    $id = $_POST['rb_statusa'] ?? 0;

    if ($akcija === 'potvrdi' || $akcija === 'odbij') {
        // Dohvati zahtjev
        $stmt = $conn->prepare("SELECT * FROM status WHERE rb_statusa = ?");
        $stmt->execute([$id]);
        $zahtjev = $stmt->fetch();

        if (!$zahtjev) {
            $poruka = "⚠️ Zahtjev nije pronađen.";
        } else {
            $jmbg = $zahtjev['jmbg'];
            $tip = $zahtjev['tip_dokumenta'];
            $datum_izdavanja = date("Y-m-d");
            $datum_isteka = date("Y-m-d", strtotime("+10 years"));

            // Dohvati općinu i potpis iz osobe
            $stmt = $conn->prepare("SELECT * FROM osoba WHERE jmbg = ?");
            $stmt->execute([$jmbg]);
            $osoba = $stmt->fetch();
            $opcina = $osoba['opcina_prebivalista'] ?? 'Nepoznato';
            $potpis = $osoba['potpis'] ?? 'N/A';

            if ($akcija === 'potvrdi') {
                switch ($tip) {
                    case 'licna_karta':
                        $stmt = $conn->prepare("INSERT INTO licna_k (jmbg, datum_izdavanja, datum_isteka, opcina_prebivalista, potpis)
                                                VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$jmbg, $datum_izdavanja, $datum_isteka, $opcina, $potpis]);
                        break;

                    case 'pasos':
                        $stmt = $conn->prepare("INSERT INTO pasos (jmbg, datum_izdavanja, datum_isteka, opcina_prebivalista, potpis)
                                                VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$jmbg, $datum_izdavanja, $datum_isteka, $opcina, $potpis]);
                        break;

                    case 'vozacka':
                        $stmt = $conn->prepare("INSERT INTO vozacka_doz (jmbg, datum_izdavanja, datum_isteka, opcina_prebivalista, kategorija, potpis)
                                                VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$jmbg, $datum_izdavanja, $datum_isteka, $opcina, "B", $potpis]);
                        break;
                }
                $poruka = "✅ Dokument kreiran. Zahtjev potvrđen.";
            } else {
                $poruka = "❌ Zahtjev odbijen.";
            }

            // Briši iz status tabele
            $stmt = $conn->prepare("DELETE FROM status WHERE rb_statusa = ?");
            $stmt->execute([$id]);
        }
    }
}

// Dohvati sve zahtjeve
$stmt = $conn->query("
    SELECT s.rb_statusa, s.jmbg, s.tip_dokumenta, s.datum_podnosenja,
           o.ime, o.prezime
    FROM status s
    JOIN osoba o ON s.jmbg = o.jmbg
    ORDER BY s.datum_podnosenja DESC
");
$zahtjevi = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upravljanje zahtjevima</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">

</head>
<body>
    <div class="container">
        <h2>📋 Zahtjevi korisnika</h2>

    <?php if (!empty($poruka)) echo "<p style='color:green;'>$poruka</p>"; ?>

    <?php if (count($zahtjevi) === 0): ?>
        <p>Nema zahtjeva za prikaz.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>Ime i prezime</th>
                <th>JMBG</th>
                <th>Dokument</th>
                <th>Datum podnošenja</th>
                <th>Akcija</th>
            </tr>
            <?php foreach ($zahtjevi as $z): ?>
                <tr>
                    <td><?php echo htmlspecialchars($z['ime'] . ' ' . $z['prezime']); ?></td>
                    <td><?php echo $z['jmbg']; ?></td>
                    <td><?php echo strtoupper($z['tip_dokumenta']); ?></td>
                    <td><?php echo $z['datum_podnosenja']; ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="rb_statusa" value="<?php echo $z['rb_statusa']; ?>">
                            <button type="submit" name="akcija" value="potvrdi">✅ Potvrdi</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="rb_statusa" value="<?php echo $z['rb_statusa']; ?>">
                            <button type="submit" name="akcija" value="odbij">❌ Odbij</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">⬅️ Nazad</a>
</div>
</body>

</html>
<?php
// Zatvori konekciju