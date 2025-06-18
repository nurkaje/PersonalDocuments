<?php
session_start();
require_once "../config/db.php";

// Provjera admin sesije
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jmbg = $_POST['jmbg'];

    // Dohvati povezani username
    $stmt = $conn->prepare("SELECT username FROM osoba WHERE jmbg = ?");
    $stmt->execute([$jmbg]);
    $korisnik = $stmt->fetchColumn();

    try {
        // Počni transakciju
        $conn->beginTransaction();

        // 1) Obriši statuse
        $conn->prepare("DELETE FROM status WHERE jmbg = ?")->execute([$jmbg]);

        // 2) Obriši dokumente
        $conn->prepare("DELETE FROM licna_k WHERE jmbg = ?")->execute([$jmbg]);
        $conn->prepare("DELETE FROM pasos WHERE jmbg = ?")->execute([$jmbg]);
        $conn->prepare("DELETE FROM vozacka_doz WHERE jmbg = ?")->execute([$jmbg]);

        // 3) Obriši zapis iz osoba
        $conn->prepare("DELETE FROM osoba WHERE jmbg = ?")->execute([$jmbg]);

        // 4) Obriši korisnički račun
        if ($korisnik) {
            $conn->prepare("DELETE FROM korisnici WHERE username = ?")->execute([$korisnik]);
        }

        // Commit transakcije
        $conn->commit();

        header("Location: upravljanje_osobama.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        // Rollback u slučaju greške
        $conn->rollBack();
        echo "<p class=\"error\">Greška prilikom brisanja: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
