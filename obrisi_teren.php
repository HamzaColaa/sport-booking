<?php
session_start();
require_once 'config/db.php';

// admin panel kontrola
if (!isset($_SESSION['user_email']) || $_SESSION['uloga'] !== 'Admin') {
    header("Location: prijava.php");
    exit();
}

// id terena u URL-u
if (isset($_GET['id'])) {
    $teren_id = intval($_GET['id']);

    // brisanje upita 
    $stmt = $conn->prepare("DELETE FROM tereni WHERE id = ?");
    $stmt->bind_param("i", $teren_id);

    if ($stmt->execute()) {
        
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