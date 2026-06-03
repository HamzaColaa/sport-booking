<?php
session_start();
require_once 'config/db.php';

$errorPoruka = "";
$successPoruka = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ime = trim($_POST['ime']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($ime) && !empty($email) && !empty($password)) {
        // email provjera
        $chk = $conn->prepare("SELECT id FROM korisnici WHERE email = ?");
        $chk->bind_param("s", $email);
        $chk->execute();
        
        if ($chk->get_result()->num_rows > 0) {
            $errorPoruka = "Korisnik sa ovom email adresom već postoji!";
        } else {
            // upis u baz
            $uloga = 'Guest';
            $ins = $conn->prepare("INSERT INTO korisnici (ime, email, lozinka, uloga) VALUES (?, ?, ?, ?)");
            $ins->bind_param("ssss", $ime, $email, $password, $uloga);
            
            if ($ins->execute()) {
                // auto login
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['user_ime'] = $ime;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $uloga;

                header("Location: index.php");
                exit();
            } else {
                $errorPoruka = "Greška pri registraciji. Pokušajte ponovo.";
            }
        }
    } else {
        $errorPoruka = "Molimo popunite sva polja.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Registracija | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen text-white p-4">

    <div class="bg-slate-950 p-8 rounded-3xl border border-slate-800 w-full max-w-md shadow-2xl">
        <h2 class="text-3xl font-black mb-2 text-center text-emerald-500">Registruj se</h2>
        <p class="text-slate-400 text-sm text-center mb-6">Kreirajte besplatan račun i rezervišite terene</p>

        <?php if (!empty($errorPoruka)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-xl text-sm mb-4 text-center">
                <?= $errorPoruka ?>
            </div>
        <?php endif; ?>

        <form action="registracija.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Ime i prezime</label>
                <input type="text" name="ime" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 text-white outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-400 mb-1">Email adresa</label>
                <input type="email" name="email" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 text-white outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-400 mb-1">Lozinka</label>
                <input type="password" name="password" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 text-white outline-none">
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 py-3 rounded-xl font-bold transition mt-6 shadow-lg shadow-emerald-600/10">
                Kreiraj račun
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Već imate račun? <a href="prijava.php" class="text-emerald-500 font-bold hover:underline">Prijavi se</a>
        </p>
    </div>

</body>
</html>