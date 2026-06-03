<?php
session_start();
require_once 'config/db.php';

// guest bez profila
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Guest') {
    header("Location: prijava.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$notifikacija = "";

// otkazivanje termina
if (isset($_GET['otkaži'])) {
    $rez_id = intval($_GET['otkaži']);
    $stmt = $conn->prepare("DELETE FROM rezervacije WHERE id = ? AND korisnik_email = ?");
    $stmt->bind_param("is", $rez_id, $user_email);
    if ($stmt->execute()) {
        $notifikacija = "✅ Rezervacija je uspješno otkazana.";
    }
}

// izmjena termina
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['akcija_izmijeni'])) {
    $rez_id = intval($_POST['rez_id']);
    $novi_datum = $_POST['novi_datum'];
    $novo_vrijeme = $_POST['novo_vrijeme'];

// provjera termina
    $t_stmt = $conn->prepare("SELECT teren_naziv FROM rezervacije WHERE id = ?");
    $t_stmt->bind_param("i", $rez_id);
    $t_stmt->execute();
    $teren_naziv = $t_stmt->get_result()->fetch_assoc()['teren_naziv'];

    $chk = $conn->prepare("SELECT id FROM rezervacije WHERE teren_naziv = ? AND datum = ? AND vrijeme = ? AND id != ?");
    $chk->bind_param("sssi", $teren_naziv, $novi_datum, $novo_vrijeme, $rez_id);
    $chk->execute();

    if ($chk->get_result()->num_rows > 0) {
        $notifikacija = "⚠️ Greška: Odabrani novi termin je već zauzet za taj teren!";
    } else {
        $upd = $conn->prepare("UPDATE rezervacije SET datum = ?, vrijeme = ? WHERE id = ? AND korisnik_email = ?");
        $upd->bind_param("ssis", $novi_datum, $novo_vrijeme, $rez_id, $user_email);
        if ($upd->execute()) {
            $notifikacija = "✅ Termin rezervacije uspješno promijenjen.";
        }
    }
}

// tren rez 
$rez_query = $conn->prepare("SELECT * FROM rezervacije WHERE korisnik_email = ? ORDER BY datum ASC");
$rez_query->bind_param("s", $user_email);
$rez_query->execute();
$rezervacije = $rez_query->get_result();
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Moj Profil | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">

    <?php include 'config/navbar.php'; ?>

    <div class="p-4 md:p-8 max-w-6xl mx-auto flex-grow w-full mt-6">
        <div class="mb-8">
            <h1 class="text-3xl font-black">Korisnički Panel</h1>
            <p class="text-slate-500 text-sm">Upravljajte svojim sportskim terminima i rezervacijama</p>
        </div>

        <?php if (!empty($notifikacija)): ?>
            <div class="bg-emerald-600 text-white p-4 rounded-xl font-bold mb-6 shadow-md">
                <?= $notifikacija ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm h-fit">
                <h3 class="text-lg font-black mb-4">Informacije o profilu</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-400 block">Ime i prezime</span>
                        <span class="font-bold text-slate-800"><?= htmlspecialchars($_SESSION['user_ime']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Email adresa</span>
                        <span class="font-bold text-slate-800"><?= htmlspecialchars($_SESSION['user_email']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Uloga računa</span>
                        <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-xs"><?= htmlspecialchars($_SESSION['user_role']) ?></span>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xl font-black">Vaši rezervisani termini</h3>

                <?php if ($rezervacije->num_rows > 0): ?>
                    <?php while ($row = $rezervacije->fetch_assoc()): ?>
                        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="bg-emerald-500 text-white font-bold text-xs px-2.5 py-0.5 rounded-full"><?= $row['sport'] ?></span>
                                    <h4 class="text-xl font-black text-slate-900 mt-2"><?= $row['teren_naziv'] ?></h4>
                                </div>
                                <span class="text-xl font-black text-emerald-600"><?= $row['ukupno_platiti'] ?></span>
                            </div>

                            
                            <form method="POST" class="bg-slate-50 p-4 rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <input type="hidden" name="rez_id" value="<?= $row['id'] ?>">
                                
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Datum</label>
                                    <input type="date" name="novi_datum" value="<?= $row['datum'] ?>" min="<?= date('Y-m-d') ?>" required class="w-full bg-white border border-slate-200 p-2 rounded-xl text-sm outline-none focus:border-emerald-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Vrijeme</label>
                                    <select name="novo_vrijeme" required class="w-full bg-white border border-slate-200 p-2 rounded-xl text-sm outline-none focus:border-emerald-500">
                                        <option value="09:00 - 10:00" <?= $row['vrijeme'] == '09:00 - 10:00' ? 'selected' : '' ?>>09:00 - 10:00</option>
                                        <option value="12:00 - 13:00" <?= $row['vrijeme'] == '12:00 - 13:00' ? 'selected' : '' ?>>12:00 - 13:00</option>
                                        <option value="17:00 - 18:00" <?= $row['vrijeme'] == '17:00 - 18:00' ? 'selected' : '' ?>>17:00 - 18:00</option>
                                        <option value="20:00 - 21:00" <?= $row['vrijeme'] == '20:00 - 21:00' ? 'selected' : '' ?>>20:00 - 21:00</option>
                                    </select>
                                </div>

                                <button type="submit" name="akcija_izmijeni" class="w-full bg-slate-800 text-white hover:bg-slate-700 py-2 rounded-xl text-sm font-bold transition">
                                    Ažuriraj termin
                                </button>
                            </form>

                            <div class="flex justify-end">
                                <a href="profil.php?otkaži=<?= $row['id'] ?>" onclick="return confirm('Da li ste sigurni da želite otkazati ovaj termin?')" class="text-sm font-bold text-red-500 hover:text-red-700 hover:underline transition">
                                    ❌ Otkaži rezervaciju
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bg-white border border-slate-100 p-8 rounded-3xl text-center text-slate-400">
                        Trenutno nemate aktivnih rezervacija. <a href="index.php" class="text-emerald-500 font-bold hover:underline">Rezerviši ovdje</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'config/footer.php'; ?>
</body>
</html>