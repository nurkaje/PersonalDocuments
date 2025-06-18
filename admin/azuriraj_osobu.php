<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jmbg = $_POST['jmbg'];
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $opcina = $_POST['opcina_prebivalista'];
    $potpis = $_POST['potpis'];

    try {
        $stmt = $conn->prepare("UPDATE osoba 
            SET ime = ?, prezime = ?, opcina_prebivalista = ?, potpis = ?
            WHERE jmbg = ?");
        $stmt->execute([$ime, $prezime, $opcina, $potpis, $jmbg]);

        // ✅ Vraćanje nazad s porukom (opcionalno možeš dodati ?msg=...)
        header("Location: upravljanje_osobama.php");
        exit();
    } catch (PDOException $e) {
        echo "❌ Greška pri ažuriranju: " . $e->getMessage();
    }
} else {
    header("Location: upravljanje_osobama.php");
    exit();
}
?>
