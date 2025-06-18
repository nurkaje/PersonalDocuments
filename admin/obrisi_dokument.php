<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Brisanje dokumenta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tip = $_POST['tip'];
    $jmbg = $_POST['jmbg'];

    switch ($tip) {
        case 'licna_karta':
            $conn->prepare("DELETE FROM licna_k WHERE jmbg = ?")->execute([$jmbg]);
            break;
        case 'pasos':
            $conn->prepare("DELETE FROM pasos WHERE jmbg = ?")->execute([$jmbg]);
            break;
        case 'vozacka':
            $conn->prepare("DELETE FROM vozacka_doz WHERE jmbg = ?")->execute([$jmbg]);
            break;
    }

    $poruka = "🗑️ Dokument obrisan.";
}

// Dohvati korisnike
$osobe = $conn->query("SELECT jmbg, ime, prezime FROM osoba")->fetchAll(PDO::FETCH_ASSOC);

// Dohvati dokumente
$licne = $conn->query("SELECT jmbg FROM licna_k")->fetchAll(PDO::FETCH_COLUMN);
$pasosi = $conn->query("SELECT jmbg FROM pasos")->fetchAll(PDO::FETCH_COLUMN);
$vozacke = $conn->query("SELECT jmbg FROM vozacka_doz")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Brisanje dokumenata</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">

</head>
<body>
    <h2>🗂️ Brisanje dokumenata korisnika</h2>

    <?php if (!empty($poruka)) echo "<p style='color:green;'>$poruka</p>"; ?>

    <table border="1" cellpadding="6">
        <tr>
            <th>JMBG</th>
            <th>Ime i prezime</th>
            <th>Lična karta</th>
            <th>Pasoš</th>
            <th>Vozačka</th>
        </tr>
        <?php foreach ($osobe as $o): ?>
            <tr>
                <td><?php echo $o['jmbg']; ?></td>
                <td><?php echo htmlspecialchars($o['ime'] . ' ' . $o['prezime']); ?></td>

                <td>
                    <?php if (in_array($o['jmbg'], $licne)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <input type="hidden" name="tip" value="licna_karta">
                            <button type="submit" onclick="return confirm('Obrisati ličnu kartu?')">🗑️</button>
                        </form>
                    <?php else: ?>❌<?php endif; ?>
                </td>

                <td>
                    <?php if (in_array($o['jmbg'], $pasosi)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <input type="hidden" name="tip" value="pasos">
                            <button type="submit" onclick="return confirm('Obrisati pasoš?')">🗑️</button>
                        </form>
                    <?php else: ?>❌<?php endif; ?>
                </td>

                <td>
                    <?php if (in_array($o['jmbg'], $vozacke)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <input type="hidden" name="tip" value="vozacka">
                            <button type="submit" onclick="return confirm('Obrisati vozačku?')">🗑️</button>
                        </form>
                    <?php else: ?>❌<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="dashboard.php">⬅️ Nazad</a>
</body>
</html>
