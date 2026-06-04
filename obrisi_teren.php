<?php
session_start();
require_once 'config/db.php';

// Provjera da li je korisnik ulogovan i da li je Admin (zaštita panela)
if (!isset($_SESSION['user_email']) || $_SESSION['uloga'] !== 'Admin') {
    header("Location: prijava.php");
    exit();
}

// Provjera da li je proslijeđen ID terena u URL-u
if (isset($_GET['id'])) {
    $teren_id = intval($_GET['id']);

    // Priprema i izvršavanje SQL upita za brisanje
    $stmt = $conn->prepare("DELETE FROM tereni WHERE id = ?");
    $stmt->bind_param("i", $teren_id);

    if ($stmt->execute()) {
        // Ako je uspješno obrisano, vraća na panel sa porukom
        header("Location: admin_dashboard.php?poruka=Teren uspješno obrisan");
    } else {
        header("Location: admin_dashboard.php?greska=Greška pri brisanju terena");
    }
    $stmt->close();
} else {
    header("Location: admin_dashboard.php");
}
$conn->close();
?>