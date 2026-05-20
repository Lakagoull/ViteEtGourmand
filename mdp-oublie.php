<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié | Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/inscription.css"> </head>
<body>

    <main class="login-container" style="margin-top: 50px;">
        <div class="login-card" style="max-width: 450px; margin: 0 auto;">
            <div class="login-header">
                <h1>Mot de passe oublié</h1>
                <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'envoye'): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                    📩 Si cet e-mail existe, un lien de réinitialisation vous a été envoyé !
                </div>
            <?php endif; ?>

            <form action="traitement-mdp-oublie.php" method="POST">
                <div class="form-group">
                    <label for="email">Votre Adresse Email</label>
                    <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                </div>

                <button type="submit" class="btn-orange w-100" style="margin-top: 10px;">Envoyer le lien</button>
            </form>

            <div class="login-footer" style="text-align: center; margin-top: 20px;">
                <p><a href="connexion.php" style="color: #800020; text-decoration: none; font-weight: bold;">Retour à la connexion</a></p>
            </div>
        </div>
    </main>

</body>
</html>