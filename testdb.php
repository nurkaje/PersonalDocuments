<?php
// testdb.php
try {
    // Pokušaj spajanja na bazu
    $dsn  = 'mysql:host=127.0.0.1;port=3306;dbname=projekat;charset=utf8mb4';
    $user = 'root';
    $pass = '1234';
    $pdo  = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p>✔️ Spoj uspješan!</p>";
} catch (PDOException $e) {
    echo "<p>❌ Greška: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<p><a href="login.php">⬅️ Nazad na prijavu</a></p>
