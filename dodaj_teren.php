<?php
session_start();
require_once 'config/db.php';


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    die("Nemate ovlaštenje za pristup.");
}

$notifikacija = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naziv = trim($_POST['naziv']);
    $sport = $_POST['sport'];
    $cijena = intval($_POST['cijena']);
    $lokacija = trim($_POST['lokacija']);
    $slika = trim($_POST['slika']); 

    if (!empty($naziv) && !empty($sport) && !empty($cijena) && !empty($lokacija)) {
        // prazna slika
        if (empty($slika)) {
            $slika = "images/default_teren.jpg";
        }

        $stmt = $conn->prepare("INSERT INTO tereni (naziv, sport, cijena, lokacija, slika) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $naziv, $sport, $cijena, $lokacija, $slika);
        
        if ($stmt->execute()) {
            $notifikacija = "✅ Teren uspješno dodat i objavljen na početnoj stranici!";
        } else {
            $notifikacija = "⚠️ Greška prilikom upisa u bazu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Dodaj novi teren | Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 text-white p-6 flex items-center justify-center min-h-screen">

    <div class="bg-slate-950 p-8 rounded-3xl border border-slate-800 w-full max-w-lg shadow-2xl">
        <div class="mb-6">
            <h2 class="text-2xl font-black text-emerald-500">Kreiraj Novi Sportski Teren</h2>
            <p class="text-slate-400 text-sm">Teren će odmah biti vidljiv svim posjetiocima za rezervaciju</p>
        </div>

        <?php if (!empty($notifikacija)): ?>
            <div class="bg-emerald-600 text-white p-3 rounded-xl text-sm font-bold mb-4 text-center">
                <?= $notifikacija ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Naziv terena</label>
                <input type="text" name="naziv" required placeholder="npr. Arena Otoka Centar" class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Sport</label>
                    <select name="sport" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 outline-none text-slate-300">
                        <option value="Fudbal">Fudbal</option>
                        <option value="Košarka">Košarka</option>
                        <option value="Tenis">Tenis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Cijena po satu (KM)</label>
                    <input type="number" name="cijena" required placeholder="npr. 40" class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm text-slate-400 mb-1">Lokacija / Adresa</label>
                <input type="text" name="lokacija" required placeholder="npr. Sarajevo - Otoka" class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-400 mb-1">Putanja do slike</label>
                <input type="text" name="slika" placeholder="images/ime_slike.jpg" class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 outline-none">
                <span class="text-xs text-slate-500 mt-1 block">Sliku ubaci u svoj folder "images", pa ovdje upiši naziv.</span>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="admin_dashboard.php" class="w-1/3 text-center bg-slate-800 border border-slate-700 py-3 rounded-xl font-bold hover:bg-slate-700 transition">Nazad</a>
                <button type="submit" class="w-2/3 bg-emerald-600 hover:bg-emerald-500 py-3 rounded-xl font-bold transition shadow-lg">Objavi Teren</button>
            </div>
        </form>
    </div>

</body>
</html>