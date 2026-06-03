<?php session_start(); ?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>O nama | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">

    <?php include 'config/navbar.php'; ?>

    <div class="p-4 md:p-8 max-w-4xl mx-auto flex-grow mt-8">
        <span class="text-xs bg-emerald-100 text-emerald-700 font-bold uppercase tracking-wider px-3 py-1 rounded-full">Ko smo mi</span>
        <h1 class="text-4xl md:text-5xl font-black mt-3 mb-6">O platformi SportBooking</h1>
        
        <p class="text-lg text-slate-600 leading-relaxed mb-6">
            SportBooking je nastao kao odgovor na vječiti studentski i rekreativni problem – sate provedene na telefonu pokušavajući da se pronađe i rezerviše slobodan termin za fudbal, košarku ili tenis sa ekipom.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-12">
            <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
                <p class="text-3xl mb-2">⚡</p>
                <h3 class="font-black text-xl mb-2">Naša misija</h3>
                <p class="text-slate-500 text-sm">Omogućiti sportistima i rekreativcima pristup najboljim terenima u gradu uz transparentne cijene i instant potvrdu rezervacije.</p>
            </div>
            <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
                <p class="text-3xl mb-2">🛡️</p>
                <h3 class="font-black text-xl mb-2">Pouzdan sistem</h3>
                <p class="text-slate-500 text-sm">Svi termini se ažuriraju u realnom vremenu, što znači da nema duplih rezervacija ili nesporazuma na terenu.</p>
            </div>
        </div>

        <p class="text-slate-600 leading-relaxed">
            Ponosni smo što nudimo premium podršku i vrhunsko korisničko iskustvo. Bez obzira da li planirate lagani vikend meč ili ozbiljan turnir, mi smo tu da osiguramo vaše mjesto na terenu. Vidimo se u igri!
        </p>
    </div>

    <?php include 'config/footer.php'; ?>

</body>
</html>