<?php 
session_start(); 
require_once 'config/db.php';

$porukaPoslana = false;
$errorPoruka = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ime = trim($_POST['ime']);
    $email = trim($_POST['email']);
    $poruka = trim($_POST['poruka']);

    if (!empty($ime) && !empty($email) && !empty($poruka)) {
        // unos por u bazu
        $stmt = $conn->prepare("INSERT INTO poruke (ime, email, poruka) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $ime, $email, $poruka);
        
        if ($stmt->execute()) {
            $porukaPoslana = true;
        } else {
            $errorPoruka = "⚠️ Greška prilikom slanja poruke. Pokušajte ponovo.";
        }
    } else {
        $errorPoruka = "⚠️ Molimo popunite sva polja.";
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Kontakt | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">

    <?php include 'config/navbar.php'; ?>

    <div class="p-4 md:p-8 max-w-5xl mx-auto flex-grow w-full mt-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            
            <div>
                <span class="text-xs bg-emerald-100 text-emerald-700 font-bold uppercase tracking-wider px-3 py-1 rounded-full">Kontakt</span>
                <h1 class="text-4xl font-black mt-3 mb-6">U kontaktu sa Vama</h1>
                <p class="text-slate-600 mb-8">Imate pitanja vezana za neki teren, partnerstvo ili probleme prilikom rezervacije? Pišite nam, naš tim odgovara u najkraćem roku.</p>
                
                <div class="space-y-4 text-sm text-slate-600">
                    <p class="flex items-center gap-3"><span class="text-xl">📍</span> Sarajevo, Bosna i Hercegovina</p>
                    <p class="flex items-center gap-3"><span class="text-xl">✉️</span> info@sportbooking.ba</p>
                    <p class="flex items-center gap-3"><span class="text-xl">📞</span> +387 33 000 000</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <?php if (!empty($errorPoruka)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl text-sm mb-4"><?= $errorPoruka ?></div>
                <?php endif; ?>

                <?php if ($porukaPoslana): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-6 rounded-xl text-center font-semibold">
                        ✅ Vaša poruka je uspješno poslana! Odgovorit ćemo Vam uskoro na email.
                    </div>
                <?php else: ?>
                    <form action="kontakt.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Vaše Ime</label>
                            <input type="text" name="ime" required class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Email adresa</label>
                            <input type="email" name="email" required class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Poruka</label>
                            <textarea name="poruka" rows="4" required class="w-full p-3 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl transition text-sm shadow-md">
                            Pošalji poruku
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php include 'config/footer.php'; ?>

</body>
</html>