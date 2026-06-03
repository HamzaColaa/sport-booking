<?php
session_start();
require_once 'config/db.php';

// admin secure
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    die("Nemate ovlaštenje za pristup ovoj stranici.");
}

// rez iz baze
$rezervacije_result = $conn->query("SELECT * FROM rezervacije ORDER BY id DESC");

// por iz baze
$poruke_result = $conn->query("SELECT * FROM poruke ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">

    <?php include 'config/navbar.php'; ?>

    <div class="p-4 md:p-8 max-w-7xl mx-auto flex-grow w-full mt-6 space-y-12">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Admin Kontrolna Tabla</h1>
                <p class="text-slate-500 text-sm">Pregled i upravljanje sistemom u realnom vremenu</p>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="dodaj_teren.php" class="w-full md:w-auto text-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-5 py-3 rounded-xl text-sm transition shadow-lg shadow-emerald-600/10">
                    ➕ Dodaj Novi Teren
                </a>
                <a href="index.php" class="w-full md:w-auto text-center bg-slate-800 hover:bg-slate-700 text-white font-bold px-5 py-3 rounded-xl text-sm transition">
                    Vrati se na sajt
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-900">Sve aktivne rezervacije terena</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">ID</th>
                            <th class="p-4">Korisnik (Email)</th>
                            <th class="p-4">Naziv Terena</th>
                            <th class="p-4">Sport</th>
                            <th class="p-4">Datum</th>
                            <th class="p-4">Termin</th>
                            <th class="p-4 pr-6 text-right">Uplaćeno</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <?php if ($rezervacije_result && $rezervacije_result->num_rows > 0): ?>
                            <?php while($row = $rezervacije_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 pl-6 font-mono text-xs text-slate-400">#<?= $row['id'] ?></td>
                                    <td class="p-4 font-medium text-slate-900"><?= htmlspecialchars($row['korisnik_email']) ?></td>
                                    <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($row['teren_naziv']) ?></td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded-full text-xs">
                                            <?= htmlspecialchars($row['sport']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-600 font-medium"><?= date('d.m.Y', strtotime($row['datum'])) ?></td>
                                    <td class="p-4 text-slate-600">
                                        <span class="bg-slate-100 text-slate-700 font-semibold px-2 py-1 rounded-lg text-xs">
                                            🕒 <?= htmlspecialchars($row['vrijeme']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-right font-black text-emerald-600">
                                        <?= htmlspecialchars($row['ukupno_platiti']) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 font-medium">Trenutno nema napravljenih rezervacija.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100 bg-slate-900 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black">Pristigle poruke sa kontakt forme</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Poruke i upiti posjetitelja sajta</p>
                </div>
                <span class="bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                    Ukupno: <?= $poruke_result ? $poruke_result->num_rows : 0 ?>
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">ID</th>
                            <th class="p-4">Pošiljatelj</th>
                            <th class="p-4">Email</th>
                            <th class="p-4 w-1/2">Sadržaj Poruke</th>
                            <th class="p-4 pr-6 text-right">Datum slanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <?php if ($poruke_result && $poruke_result->num_rows > 0): ?>
                            <?php while($poruka = $poruke_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 pl-6 font-mono text-xs text-slate-400">#<?= $poruka['id'] ?></td>
                                    <td class="p-4 font-bold text-slate-900"><?= htmlspecialchars($poruka['ime']) ?></td>
                                    <td class="p-4 font-medium text-emerald-600 hover:underline"><a href="mailto:<?= $poruka['email'] ?>"><?= htmlspecialchars($poruka['email']) ?></a></td>
                                    <td class="p-4 text-slate-600 leading-relaxed bg-slate-50/40 font-serif italic">"<?= htmlspecialchars($poruka['poruka']) ?>"</td>
                                    <td class="p-4 pr-6 text-right text-xs text-slate-400 font-mono">
                                        <?= date('d.m.Y H:i', strtotime($poruka['datum_slanja'])) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 font-medium">Nema pristiglih poruka.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php include 'config/footer.php'; ?>

</body>
</html>