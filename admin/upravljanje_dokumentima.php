<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$poruka = "";

// Obrada brisanja dokumenta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tip = $_POST['tip_dokumenta'];
    $jmbg = $_POST['jmbg'];

    switch ($tip) {
        case 'licna_karta':
            $stmt = $conn->prepare("DELETE FROM licna_k WHERE jmbg = ?");
            $stmt->execute([$jmbg]);
            $poruka = "🗑️ Lična karta obrisana.";
            break;
        case 'pasos':
            $stmt = $conn->prepare("DELETE FROM pasos WHERE jmbg = ?");
            $stmt->execute([$jmbg]);
            $poruka = "🗑️ Pasoš obrisan.";
            break;
        case 'vozacka':
            $stmt = $conn->prepare("DELETE FROM vozacka_doz WHERE jmbg = ?");
            $stmt->execute([$jmbg]);
            $poruka = "🗑️ Vozačka dozvola obrisana.";
            break;
    }
}

// Dohvati sve osobe
$stmt = $conn->query("SELECT * FROM osoba");
$osobe = $stmt->fetchAll();

// Dohvati sve dokumente
$licne = $conn->query("SELECT jmbg FROM licna_k")->fetchAll(PDO::FETCH_COLUMN);
$pasosi = $conn->query("SELECT jmbg FROM pasos")->fetchAll(PDO::FETCH_COLUMN);
$vozacke = $conn->query("SELECT jmbg FROM vozacka_doz")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upravljanje dokumentima</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">

</head>
<body>
<body>
<div class="container">
    <h2>📂 Upravljanje dokumenti korisnika</h2>


    <?php if ($poruka) echo "<p style='color:green;'>$poruka</p>"; ?>

    <table border="1" cellpadding="6">
        <tr>
            <th>Ime i prezime</th>
            <th>JMBG</th>
            <th>Lična karta</th>
            <th>Pasoš</th>
            <th>Vozačka dozvola</th>
        </tr>

        <?php foreach ($osobe as $o): ?>
            <tr>
                <td><?php echo htmlspecialchars($o['ime'] . ' ' . $o['prezime']); ?></td>
                <td><?php echo $o['jmbg']; ?></td>

                <!-- Lična karta -->
                <td>
                    <?php if (in_array($o['jmbg'], $licne)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="tip_dokumenta" value="licna_k">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <button type="submit" onclick="return confirm('Obrisati ličnu kartu?')">🗑️</button>
                        </form>
                    <?php else: ?>
                        ❌
                    <?php endif; ?>
                </td>

                <!-- Pasoš -->
                <td>
                    <?php if (in_array($o['jmbg'], $pasosi)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="tip_dokumenta" value="pasos">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <button type="submit" onclick="return confirm('Obrisati pasoš?')">🗑️</button>
                        </form>
                    <?php else: ?>
                        ❌
                    <?php endif; ?>
                </td>

                <!-- Vozačka dozvola -->
                <td>
                    <?php if (in_array($o['jmbg'], $vozacke)): ?>
                        ✅
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="tip_dokumenta" value="vozacka">
                            <input type="hidden" name="jmbg" value="<?php echo $o['jmbg']; ?>">
                            <button type="submit" onclick="return confirm('Obrisati vozačku dozvolu?')">🗑️</button>
                        </form>
                    <?php else: ?>
                        ❌
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a class="back-link" href="dashboard.php">⬅️ Nazad</a>
</div>
</body>

</body>
</html>
<?php
$conn = null; // Zatvori konekciju