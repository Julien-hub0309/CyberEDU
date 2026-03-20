<?php
session_start();
require 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$message = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Vérification simple
    if ($password !== $password_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $error = true;
    } else {
        // Vérifier si l'utilisateur existe déjà
        $check = $pdo->prepare("SELECT id_membre FROM membres WHERE nom = ? OR email = ?");
        $check->execute([$nom, $email]);

        if ($check->fetch()) {
            $message = "Le nom d'utilisateur ou l'email est déjà utilisé.";
            $error = true;
        } else {
            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertion
            $stmt = $pdo->prepare("INSERT INTO membres (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hashedPassword])) {
                $message = "Inscription réussie ! <a href='login.php'>Connectez-vous ici</a>";
                $error = false;
            } else {
                $message = "Une erreur est survenue lors de l'inscription.";
                $error = true;
                            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberEDU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800 flex flex-col min-h-screen">

    <header class="bg-blue-700 text-white shadow-lg z-20">
        <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-8">
                <h1 class="text-2xl font-bold tracking-tight">CyberEDU</h1>
                <nav>
                    <ul class="flex gap-6 text-sm font-medium">
                        <li><a href="Acceuil.php" class="hover:text-blue-200 transition">Accueil</a></li>
                        <li><a href="Cantine.php" class="hover:text-blue-200 transition">Cantine</a></li>
                        <li><a href="Dashboard.php" class="hover:text-blue-200 transition">Dashboard</a></li>
                        <li><a href="Messagerie.php" class="hover:text-blue-200 transition">Messagerie</a></li>
                    </ul>
                </nav>
            </div>

            <div class="w-full md:w-64">
                <div class="relative">
                    <input type="search" id="search-input" placeholder="Rechercher..." 
                           class="w-full bg-blue-800/50 border border-blue-400 text-white placeholder-blue-200 text-sm rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white/50 transition">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-blue-200"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="main-gradient flex-grow py-10 px-6">
        <div class="container mx-auto">
            <section class="bg-blue-50/80 border border-blue-100 rounded-2xl shadow-sm p-8 min-h-[500px]">
            <h2>Créer un compte</h2>

                <?php if ($message): ?>
                    <div class="alert <?php echo $error ? 'error' : 'success'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="inscription.php" method="POST">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" name="username" id="username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirm" id="password_confirm" required>
                    </div>

                    <button type="submit">S'inscrire</button>
                </form>

                <div class="login-link">
                    <p>Déjà membre ? <a href="login.php">Connectez-vous</a></p>
                </div>
                </div>
                
                <div id="apps-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                    </div>
            </section>
        </div>
    </main>

    <footer class="bg-slate-700 text-slate-300 py-4 text-center">
        <p class="text-xs">&copy; <span id="currentYear"></span> Justradamus - Tous droits réservés.</p>
    </footer>

    <script src="./Module/Script.js"></script>
    <script>document.getElementById('currentYear').textContent = new Date().getFullYear();</script>
</body>
</html>