<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo APP_NAME; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .nav a:hover { text-decoration: underline; }
    </style>
    <link rel="stylesheet" href="<?= url('assets/css/style.css'); ?>">
</head>
<body>
    <div class="nav">
        <?php if (is_logged_in()): ?>
            <a href="/admin/dashboard">Tableau de bord</a>
            <a href="/admin/media">Médias</a>
            <a href="/admin/users">Utilisateurs</a>
            <a href="/admin/loans">Emprunts</a>
            <a href="/logout">Déconnexion</a>
        <?php else: ?>
            <a href="/auth/login">Connexion</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <?php echo $content; ?>
    </div>
</body>
</html>