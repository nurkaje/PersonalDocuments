<?php
// Provjera da li je korisnik prijavljen
function isLoggedIn() {
    return isset($_SESSION['username']) && isset($_SESSION['uloga']);
}

// Provjera da li je admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['uloga'] === 'admin';
}

// Provjera da li je korisnik (običan)
function isUser() {
    return isLoggedIn() && $_SESSION['uloga'] === 'korisnik';
}

// Ako nije admin, preusmjeri
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}

// Ako nije korisnik, preusmjeri
function requireUser() {
    if (!isUser()) {
        header("Location: ../login.php");
        exit();
    }
}
