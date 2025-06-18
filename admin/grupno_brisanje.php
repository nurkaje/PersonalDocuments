<?php
// admin/grupno_brisanje_filter.php
session_start();
require_once "../config/db.php";

// Provjera admin sesije
if (!isset($_SESSION['uloga']) || $_SESSION['uloga']!=='admin') {
    header("Location: ../login.php");
    exit();
}

$poruka = "";

if ($_SERVER['REQUEST_METHOD']==='POST') {
    // 1) JMBG list
    $jmbgInput = trim($_POST['jmbg_list'] ?? '');
    $jmbgList  = $jmbgInput!=='' 
        ? array_unique(array_filter(array_map('trim', explode(',', $jmbgInput))))
        : [];

    // 2) Ime list → prvo dohvatimo JMBG za ta imena
    $imeInput = trim($_POST['ime_list'] ?? '');
    if ($imeInput!=='') {
        $imeList = array_unique(array_filter(array_map('trim', explode(',', $imeInput))));
        // dohvati jmbg-ove iz osobe
        $place = implode(',', array_fill(0, count($imeList), '?'));
        $stmt = $conn->prepare("SELECT jmbg FROM osoba WHERE ime IN($place)");
        $stmt->execute($imeList);
        $found = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $jmbgList = array_unique(array_merge($jmbgList, $found));
    }

    // 3) Godina list
    $yearInput = trim($_POST['year_list'] ?? '');
    $yearList  = $yearInput!==''
        ? array_unique(array_filter(array_map('trim', explode(',', $yearInput))))
        : [];

    if (empty($jmbgList) && empty($yearList)) {
        $poruka = "⚠️ Unesite bar JMBG ili ime ili godinu izdavanja.";
    } else {
        // Sastavljamo WHERE uvjete
        $where = []; 
        $params = [];

        if (!empty($jmbgList)) {
            $ph = implode(',', array_fill(0, count($jmbgList), '?'));
            $where[] = "jmbg IN($ph)";
            $params = array_merge($params, $jmbgList);
        }
        if (!empty($yearList)) {
            $ph = implode(',', array_fill(0, count($yearList), '?'));
            $where[] = "YEAR(datum_izdavanja) IN($ph)";
            $params = array_merge($params, $yearList);
        }

        $filter = implode(' OR ', $where);

        try {
            $conn->beginTransaction();

            // Lična karta
            $del1 = $conn->prepare("DELETE FROM licna_k WHERE $filter");
            $del1->execute($params);
            $c1 = $del1->rowCount();

            // Pasoši
            $del2 = $conn->prepare("DELETE FROM pasos WHERE $filter");
            $del2->execute($params);
            $c2 = $del2->rowCount();

            // Vozačka
            $del3 = $conn->prepare("DELETE FROM vozacka_doz WHERE $filter");
            $del3->execute($params);
            $c3 = $del3->rowCount();

            $conn->commit();
            $poruka = "✅ Obrisano: Lične karte ($c1), Pasoši ($c2), Vozačke dozvole ($c3).";
        } catch (Exception $e) {
            $conn->rollBack();
            $poruka = "❌ Greška pri brisanju: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Napredno grupno brisanje</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h2>🗑️ Grupno brisanje dokumenata</h2>

    <?php if ($poruka): ?>
        <p class="<?= str_starts_with($poruka,'✅')?'success':'error' ?>">
            <?= htmlspecialchars($poruka) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <fieldset>
            <legend>Unesite jedan ili više kriterija za brisanje:</legend>

            <label for="jmbg_list">JMBG (može više, odvojiti zarezom):</label>
            <input type="text" id="jmbg_list" name="jmbg_list"
                   placeholder="1234567890123,2345678901234">

            <label for="ime_list">Ime (može više, odvojiti zarezom):</label>
            <input type="text" id="ime_list" name="ime_list"
                   placeholder="Alma,Nina">

            <label for="year_list">Godina izdavanja (može više, YYYY):</label>
            <input type="text" id="year_list" name="year_list"
                   placeholder="2024,2025">
        </fieldset>

        <button type="submit" class="table-button delete">🗑️ Obriši dokumente</button>
    </form>

    <p><a href="dashboard.php" class="back-link">⬅️ Nazad na dashboard</a></p>
</div>
</body>
</html>
