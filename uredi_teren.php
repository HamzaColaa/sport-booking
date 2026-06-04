<?php
session_start();
require_once 'config/db.php';

// Zaštita
if (!isset($_SESSION['user_email']) || $_SESSION['uloga'] !== 'Admin') {
    header("Location: prijava.php");
    exit();
}

$poruka = "";
$greska = "";

// 1. Dobavljanje trenutnih podataka terena za formu
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM tereni WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teren = $result->fetch_assoc();
    $stmt->close();
    
    if (!$teren) {
        die("Teren nije pronađen.");
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}

// 2. Obrada forme nakon klika na "Spasi izmjene"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naziv = $_POST['naziv'];
    $sport = $_POST['sport'];
    $cijena = floatval($_POST['cijena']);
    $lokacija = $_POST['lokacija'];

    if (!empty($naziv) && !empty($sport) && !empty($lokacija) && $cijena > 0) {
        $stmt = $conn->prepare("UPDATE tereni SET naziv = ?, sport = ?, cijena = ?, lokacija = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $naziv, $sport, $cijena, $lokacija, $id);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?poruka=Teren uspješno ažuriran");
            exit();
        } else {
            $greska = "Došlo je do greške pri ažuriranju baze.";
        }
        $stmt->close();
    } else {
        $greska = "Molimo ispunite sva polja ispravno.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Uredi Teren</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900">

    <?php include 'config/navbar.php'; ?>

    <div class="max-w-lg mx-auto mt-12 bg-white p-8 rounded-3xl border border-slate-100 shadow-xl">
        <h2 class="text-3xl font-black mb-6">Uredi Teren</h2>

        <?php if(!empty($greska)): ?>
            <div class="bg-rose-100 text-rose-700 p-3 rounded-xl mb-4 font-semibold"><?= $greska ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-1">Naziv terena</label>
                <input type="text" name="naziv" value="<?= htmlspecialchars($teren['naziv']) ?>" class="w-full p-3 border border-slate-200 rounded-xl focus:outline-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-1">Sport</label>
                <select name="sport" class="w-full p-3 border border-slate-200 rounded-xl focus:outline-emerald-500">
                    <option value="Fudbal" <?= $teren['sport'] == 'Fudbal' ? 'selected' : '' ?>>Fudbal</option>
                    <option value="Košarka" <?= $teren['sport'] == 'Košarka' ? 'selected' : '' ?>>Košarka</option>
                    <option value="Tenis" <?= $teren['sport'] == 'Tenis' ? 'selected' : '' ?>>Tenis</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-1">Cijena po satu (KM)</label>
                <input type="number" name="cijena" value="<?= htmlspecialchars($teren['cijena']) ?>" class="w-full p-3 border border-slate-200 rounded-xl focus:outline-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-1">Lokacija</label>
                <input type="text" name="lokacija" value="<?= htmlspecialchars($teren['lokacija']) ?>" class="w-full p-3 border border-slate-200 rounded-xl focus:outline-emerald-500">
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition">
                    Spasi izmjene
                </button>
                <a href="admin_dashboard.php" class="text-center bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold py-3 px-6 rounded-xl transition">
                    Otkaži
                </a>
            </div>
        </form>
    </div>

</body>
</html>