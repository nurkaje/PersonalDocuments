<?php
session_start();
require_once "config/db.php";

$poruka = "";
$tip_poruke = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $username = strtolower($ime . $prezime); // automatski username
    $password = md5($_POST['password']);
    $uloga = 'korisnik';

    $jmbg = $_POST['jmbg'];
    $datum_rodjenja = $_POST['datum_rodjenja'];
    $drzavljanstvo = $_POST['id_drzavljanstva'];
    $opcina_rodjenja = $_POST['opcina_rodjenja'];
    $opcina_prebivalista = $_POST['opcina_prebivalista'];
    $potpis = $_POST['potpis'];

    // Validacija JMBG: mora imati točno 13 cifara
    if (!preg_match('/^\d{13}$/', $jmbg)) {
        $poruka = "❌ JMBG mora imati tačno 13 cifara (npr. 1234567890123).";
        $tip_poruke = "error";
    } else {
        // Provjera username
        $stmt = $conn->prepare("SELECT * FROM korisnici WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $poruka = "❌ Korisničko ime već postoji (generisano: $username).";
            $tip_poruke = "error";
        } else {
            // Provjera JMBG
            $stmt = $conn->prepare("SELECT * FROM osoba WHERE jmbg = ?");
            $stmt->execute([$jmbg]);
            if ($stmt->rowCount() > 0) {
                $poruka = "❌ Osoba s tim JMBG već postoji.";
                $tip_poruke = "error";
            } else {
                try {
                    // Dodaj korisnika
                    $stmt = $conn->prepare("INSERT INTO korisnici (username, password, uloga) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $password, $uloga]);

                    // Dodaj osobu
                    $stmt = $conn->prepare(
                        "INSERT INTO osoba (
                            jmbg, ime, prezime, datum_rodjenja, id_drzavljanstva,
                            opcina_rodjenja, opcina_prebivalista, potpis, username
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->execute([
                        $jmbg,
                        $ime,
                        $prezime,
                        $datum_rodjenja,
                        $drzavljanstvo,
                        $opcina_rodjenja,
                        $opcina_prebivalista,
                        $potpis,
                        $username
                    ]);

                    $poruka = "✅ Registracija uspješna! Vaš username je: <strong>$username</strong>";
                    $tip_poruke = "success";
                } catch (PDOException $e) {
                    $poruka = "⚠️ Greška prilikom unosa: " . htmlspecialchars($e->getMessage());
                    $tip_poruke = "error";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registracija korisnika</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>📝 Registracija korisnika</h2>

    <?php if (!empty($poruka)): ?>
        <p class="message <?= $tip_poruke ?? 'info' ?>">
            <?= $poruka ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <fieldset>
            <legend>🧍 Podaci o osobi + prijava</legend>

            <label for="ime">Ime:</label>
            <input type="text" name="ime" id="ime" required>

            <label for="prezime">Prezime:</label>
            <input type="text" name="prezime" id="prezime" required>

            <label for="password">Lozinka:</label>
            <input type="password" name="password" id="password" required>

            <label for="jmbg">JMBG:</label>
            <input type="text" name="jmbg" id="jmbg" required
                   pattern="\d{13}"
                   title="JMBG mora imati tačno 13 cifara, npr. 1234567890123"
                   placeholder="npr. 1234567890123">

            <label for="datum_rodjenja">Datum rođenja:</label>
            <input type="date" name="datum_rodjenja" id="datum_rodjenja" required>

            <label for="id_drzavljanstva">Državljanstvo:</label>
            <input type="text" name="id_drzavljanstva" id="id_drzavljanstva" required>

            <label for="opcina_rodjenja">Općina rođenja:</label>
            <input type="text" name="opcina_rodjenja" id="opcina_rodjenja" required>

            <label for="opcina_prebivalista">Općina prebivališta:</label>
            <input type="text" name="opcina_prebivalista" id="opcina_prebivalista" required>

            <label for="potpis">Potpis:</label>
            <input type="text" name="potpis" id="potpis" required>
        </fieldset>

        <input type="submit" value="Registruj se">
    </form>

    <br>
    <a class="back-link" href="login.php">⬅️ Nazad na login</a><br>
    <a class="back-link" href="index.php">← Nazad na početnu</a>
</div>
</body>
</html>

<?php $conn = null; ?>
