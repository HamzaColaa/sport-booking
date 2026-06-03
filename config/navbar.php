<?php
$trenutna_stranica = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white border-b border-slate-100 p-4 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
       
        <a href="index.php" class="font-black text-2xl text-emerald-600 tracking-tight flex items-center gap-2">
            🏆 SportBooking
        </a>
        
        
        <div class="flex items-center gap-6">
            
            <div class="hidden md:flex items-center gap-6 text-sm font-bold text-slate-600">
                <a href="index.php" class="hover:text-emerald-600 transition <?= $trenutna_stranica == 'index.php' ? 'text-emerald-600' : '' ?>">Početna</a>
                <a href="o_nama.php" class="hover:text-emerald-600 transition <?= $trenutna_stranica == 'o_nama.php' ? 'text-emerald-600' : '' ?>">O nama</a>
                <a href="kontakt.php" class="hover:text-emerald-600 transition <?= $trenutna_stranica == 'kontakt.php' ? 'text-emerald-600' : '' ?>">Kontakt</a>
                
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Guest'): ?>
                    <a href="profil.php" class="hover:text-emerald-600 transition <?= $trenutna_stranica == 'profil.php' ? 'text-emerald-600' : '' ?>">Moj Panel</a>
                <?php endif; ?>
            </div>

            
            <div class="hidden md:block w-px h-5 bg-slate-200"></div>

            
            <div class="flex items-center gap-4">
                <?php if (isset($_SESSION['user_email'])): ?>
                    
                    
                    <?php if ($_SESSION['user_role'] !== 'Admin'): ?>
                        <span class="text-sm bg-slate-100 px-3 py-1.5 rounded-full text-slate-700 font-medium">
                            👋 <?= htmlspecialchars($_SESSION['user_ime']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                        <a href="admin_dashboard.php" class="text-sm font-bold text-emerald-600 border border-emerald-200 bg-emerald-50 px-3 py-1.5 rounded-xl hover:bg-emerald-100 transition">Admin Dashboard</a>
                    <?php endif; ?>
                    
                    <a href="odjava.php" class="text-sm font-bold text-red-500 hover:underline">Odjavi se</a>
                <?php else: ?>
                    <a href="prijava.php" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition">Prijava</a>
                    <a href="registracija.php" class="bg-emerald-600 text-white font-bold px-5 py-2.5 rounded-xl text-sm hover:bg-emerald-500 transition shadow-sm">
                        Registracija
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>