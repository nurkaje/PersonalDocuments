<?php
$host = 'localhost'; // stavite adresu vašeg servera baze podataka ovdje
$dbname = 'projekat'; //stavite ime vaše baze podataka ovdje
$username = 'root'; //stavite korisničko ime vaše baze podataka ovdje
$password = 'password'; //stavite lozinku vaše baze podataka ovdje

// Kreiranje konekcije
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Greška u konekciji: " . $e->getMessage());
}
?>
