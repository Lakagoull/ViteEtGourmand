<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('config.php');

$token = $_GET['token'] ?? $_SESSION['reset_token'] ?? '';
$token_valide = false;
$message_success = false;

if (!empty($token)) {
    $token_safe = mysqli_real_escape_string($conn, $token);
    $query = mysqli_query($conn, "SELECT id FROM utilisateurs WHERE token_recup = '$token_safe' AND token_expire > NOW()");
    $user = mysqli_fetch_assoc($query);
    
    if ($user) {
        $token_valide = true;
        $_SESSION['reset_token'] = $token;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && $token_valide) {
    $new_mdp_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $token_safe = mysqli_real_escape_string($conn, $token);
    
    $update = mysqli_query($conn, "UPDATE utilisateurs SET mot_de_passe = '$new_mdp_hash', token_recup = NULL, token_expire = NULL WHERE token_recup = '$token_safe'");
    
    if ($update) {
        $message_success = true;
        unset($_SESSION['reset_token']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
    <link rel="stylesheet" href="css/inscription.css">
</head>
<body>
    <main class="login-container" style="margin-top: 50px;">
        <div class="login-card" style="max-width: 450px; margin: 0 auto;">
            
            <?php if ($message_success): ?>
                <h1>Succès !</h1>
                <p>Votre mot de passe a été mis à jour.</p>
                <a href="connexion.php">Se connecter</a>
            
            <?php elseif ($token_valide): ?>
                <h1>Nouveau mot de passe</h1>
                <form action="" method="POST">
                    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
                    <button type="submit">Enregistrer</button>
                </form>

            <?php else: ?>
                <h1>Lien invalide ou expiré</h1>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>