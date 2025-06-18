<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Dohvati sve osobe + njihovu ulogu
$stmt = $conn->query("
    SELECT o.*, k.uloga
    FROM osoba o
    LEFT JOIN korisnici k ON o.username = k.username
");
$osobe = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upravljanje osobama</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h2>👥 Upravljanje korisnicima</h2>

    <?php if (count($osobe) === 0): ?>
        <p class="message info">Nema registrovanih korisnika.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>JMBG</th>
                    <th>Ime</th>
                    <th>Prezime</th>
                    <th>Općina prebivališta</th>
                    <th>Potpis</th>
                    <th>Uloga</th>
                    <th colspan="2">Akcije</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($osobe as $o): ?>
                <tr>
                    <form action="azuriraj_osobu.php" method="post">
                        <td>
                            <input type="text" value="<?= htmlspecialchars($o['jmbg']) ?>" disabled class="readonly-input">
                            <input type="hidden" name="jmbg" value="<?= $o['jmbg'] ?>">
                        </td>
                        <td><input type="text" name="ime" value="<?= htmlspecialchars($o['ime']) ?>" required></td>
                        <td><input type="text" name="prezime" value="<?= htmlspecialchars($o['prezime']) ?>" required></td>
                        <td><input type="text" name="opcina_prebivalista" value="<?= htmlspecialchars($o['opcina_prebivalista']) ?>" required></td>
                        <td><input type="text" name="potpis" value="<?= htmlspecialchars($o['potpis']) ?>" required></td>
                        <td><input type="text" value="<?= $o['uloga'] ?? 'N/A' ?>" disabled class="readonly-input"></td>
                        <td><button type="submit" class="table-button save">💾 Ažuriraj</button></td>
                        </form>
                    <td>
                        <form action="obrisi_osobu.php" method="post" onsubmit="return confirm('Jeste li sigurni da želite obrisati ovog korisnika?')">
                            <input type="hidden" name="jmbg" value="<?= $o['jmbg'] ?>">
<button type="submit" class="table-button delete">🗑️ Obriši</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a class="back-link" href="dashboard.php">⬅️ Nazad</a>
</div>
</body>
</html>
