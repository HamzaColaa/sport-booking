<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: prijava.php");
    exit();
}

$teren_id = $_GET['teren_id'] ?? null;
$errorPoruka = "";
$success = false;

// provjera pod o terenu
$stmt = $conn->prepare("SELECT * FROM tereni WHERE id = ?");
$stmt->bind_param("i", $teren_id);
$stmt->execute();
$teren = $stmt->get_result()->fetch_assoc();

if (!$teren) {
    die("Teren nije pronađen.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datum = $_POST['datum'];
    $vrijeme = $_POST['vrijeme'];

    if (!empty($datum) && !empty($vrijeme)) {
        // provjera termina
        $chk = $conn->prepare("SELECT id FROM rezervacije WHERE teren_naziv = ? AND datum = ? AND vrijeme = ?");
        $chk->bind_param("sss", $teren['naziv'], $datum, $vrijeme);
        $chk->execute();
        
        if ($chk->get_result()->num_rows > 0) {
            $errorPoruka = "⚠️ Žao nam je, ovaj teren je već rezervisan u tom terminu!";
        } else {
            // unos novog termina
            $ukupno = $teren['cijena'] . " KM";
            $ins = $conn->prepare("INSERT INTO rezervacije (korisnik_email, teren_naziv, sport, datum, vrijeme, ukupno_platiti) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->bind_param("ssssss", $_SESSION['user_email'], $teren['naziv'], $teren['sport'], $datum, $vrijeme, $ukupno);
            
            if ($ins->execute()) {
                $success = true;
            } else {
                $errorPoruka = "Greška prilikom upisa rezervacije.";
            }
        }
    } else {
        $errorPoruka = "Molimo odaberite datum i vrijeme.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Potvrda Rezervacije</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-slate-100">
        <h3 class="text-2xl font-black text-slate-900">Rezervacija Terena</h3>
        <p class="text-slate-500 mt-1 mb-6"><?= $teren['naziv'] ?> (<?= $teren['lokacija'] ?>)</p>

        <?php if (!empty($errorPoruka)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl text-sm mb-4"><?= $errorPoruka ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-center font-semibold mb-6">
                ✅ Termin uspješno rezervisan!
            </div>
            <a href="index.php" class="block text-center w-full bg-emerald-600 text-white py-3 rounded-xl font-bold">Vrati se na početnu</a>
        <?php else: ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Datum</label>
                    <input type="date" name="datum" required min="<?= date('Y-m-d') ?>" class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Vrijeme (Termin)</label>
                    <select name="vrijeme" required class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-emerald-500">
                        <option value="">Odaberi termin</option>
                        <option value="09:00 - 10:00">09:00 - 10:00</option>
                        <option value="12:00 - 13:00">12:00 - 13:00</option>
                        <option value="17:00 - 18:00">17:00 - 18:00</option>
                        <option value="20:00 - 21:00">20:00 - 21:00</option>
                    </select>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Korisnik:</span>
                        <span class="font-bold text-slate-800"><?= $_SESSION['user_email'] ?></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Ukupno za platiti:</span>
                        <span class="font-black text-emerald-600 text-lg"><?= $teren['cijena'] ?> KM</span>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="index.php" class="w-1/3 text-center bg-slate-100 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-200 transition">Nazad</a>
                    <button type="submit" class="w-2/3 bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-bold transition">Potvrdi</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>