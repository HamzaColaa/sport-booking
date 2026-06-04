<?php
session_start();
require_once 'config/db.php';

$odabraniSport = isset($_GET['sport']) ? $_GET['sport'] : 'Svi';

if ($odabraniSport !== 'Svi') {
    $stmt = $conn->prepare("SELECT * FROM tereni WHERE sport = ?");
    $stmt->bind_param("s", $odabraniSport);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM tereni");
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rezervacija Sportskih Terena</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">

    <?php include 'config/navbar.php'; ?>

    <div class="p-4 md:p-8 max-w-7xl mx-auto flex-grow w-full">

        <div class="relative overflow-hidden rounded-3xl bg-slate-950 p-10 md:p-14 mb-12 text-white">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/20 blur-[140px]"></div>
            <h1 class="text-4xl md:text-6xl font-black mb-4 relative z-10">Rezerviši teren za svoju ekipu</h1>
            <p class="text-slate-300 text-lg relative z-10">Fudbal • Košarka • Tenis</p>
            
            <div class="grid grid-cols-3 gap-6 mt-10 relative z-10 max-w-md">
                <div>
                    <p class="text-3xl font-black">50+</p>
                    <p class="text-slate-400 text-sm">Rezervacija</p>
                </div>
                <div>
                    <p class="text-3xl font-black">5</p>
                    <p class="text-slate-400 text-sm">Terena</p>
                </div>
                <div>
                    <p class="text-3xl font-black">24/7</p>
                    <p class="text-slate-400 text-sm">Dostupnost</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mb-10 justify-center">
            <?php foreach (['Svi', 'Fudbal', 'Košarka', 'Tenis'] as $sport): ?>
                <a href="index.php?sport=<?= $sport ?>" 
                   class="px-5 py-3 rounded-xl font-bold transition <?= $odabraniSport === $sport ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                    <?= $sport ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($teren = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition duration-300">
                        <div class="relative h-60 overflow-hidden">
                            <img src="<?= ltrim($teren['slika'], '/') ?>" alt="<?= htmlspecialchars($teren['naziv']) ?>" class="w-full h-full object-cover">
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full"><?= htmlspecialchars($teren['sport']) ?></span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-2xl font-black text-slate-900"><?= htmlspecialchars($teren['naziv']) ?></h3>
                            <p class="text-slate-500 mt-2">📍 <?= htmlspecialchars($teren['lokacija']) ?></p>

                            <div class="bg-slate-50 rounded-2xl p-4 mt-5">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400 font-semibold text-sm">Cijena po satu</span>
                                    <span class="text-2xl font-black text-emerald-600"><?= htmlspecialchars($teren['cijena']) ?> KM</span>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['user_email'])): ?>
                                <a href="rezervacija.php?teren_id=<?= $teren['id'] ?>" class="block text-center w-full mt-5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold py-3 rounded-xl transition">
                                    Rezerviši Termin
                                </a>
                            <?php else: ?>
                                <button onclick="alert('Morate biti prijavljeni da biste rezervisali teren!'); window.location.href='prijava.php';" class="w-full mt-5 bg-slate-200 text-slate-600 font-bold py-3 rounded-xl cursor-pointer hover:bg-slate-300 transition">
                                    Prijavi se za rezervaciju
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-10">
                    <p class="text-slate-500">Nema dostupnih terena u bazi podataka.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'config/footer.php'; ?>

</body>
</html>