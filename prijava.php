<?php
session_start();
require_once 'config/db.php';

$errorPoruka = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, ime, email, lozinka, uloga FROM korisnici WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['lozinka']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_ime'] = $user['ime'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['uloga'];

                if ($user['uloga'] === 'Admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $errorPoruka = "Pogrešna lozinka!";
            }
        } else {
            $errorPoruka = "Korisnik sa ovim emailom ne postoji!";
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
    <title>Prijava | SportBooking</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center h-screen text-white p-4">

    <div class="bg-slate-950 p-8 rounded-3xl border border-slate-800 w-full max-w-md shadow-2xl">
        <h2 class="text-3xl font-black mb-2 text-center text-emerald-500">Prijavi se</h2>
        <p class="text-slate-400 text-sm text-center mb-6">Pristupite sistemu za rezervaciju terena</p>

        <?php if (!empty($errorPoruka)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-xl text-sm mb-4 text-center">
                <?= $errorPoruka ?>
            </div>
        <?php endif; ?>

        <form action="prijava.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Email adresa</label>
                <input type="email" name="email" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 text-white outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-400 mb-1">Lozinka</label>
                <input type="password" name="password" required class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl focus:border-emerald-500 text-white outline-none">
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 py-3 rounded-xl font-bold transition mt-6">
                Prijavi se
            </button>
        </form>

        
        <div class="mt-6 pt-6 border-t border-slate-800/60 text-center">
            <p class="text-sm text-slate-400">Nemate profil?</p>
            <a href="registracija.php" class="inline-block mt-2 text-emerald-400 font-bold hover:text-emerald-300 hover:underline transition text-sm">
                Registruj svoj profil ako ga još uvijek nemaš →
            </a>
        </div>
    </div>

</body>
</html>