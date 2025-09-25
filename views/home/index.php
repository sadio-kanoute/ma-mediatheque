<div class="hero">
    <div class="hero-content">
        <h1><?php e($message); ?></h1>
        <p class="hero-subtitle">Cultivons notre culture !</p>
        <?php if (!is_logged_in()): ?>
            <div class="hero-buttons">
                <a href="<?php echo url('auth/register'); ?>" class="btn btn-primary">Commencer</a>
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-secondary">Se connecter</a>
            </div>
        <?php else: ?>
            <p class="welcome-message">
                <i class="fas fa-user"></i>
                Bienvenue, <?php e($_SESSION['user']['name'] ?? ''); ?> !
            </p>
        <?php endif; ?>
    </div>
</div>

<section class="features">
    <div class="container">
        <h2>Fonctionnalités incluses</h2>
        <div class="features-grid">
            <?php foreach ($features as $feature): ?>
                <div class="feature-card">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php e($feature); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="getting-started">
    <div class="container">
        <h2>Commencer rapidement grâce au CEPRR</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Créez</h3>
                <p>Créez votre profil sur la page inscription</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Explorez</h3>
                <p>
                    Parcourez le catalogue à la recherche de vos médias préférés ou de nouveaux médias
                </p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Profitez</h3>
                <p>
                    Une fois que vous avez trouvé ce que vous cherchiez, empruntez jusqu'à 3
                    articles et profitez de vos nouvelles aquisitions temporaires pendant 2 semaines
                </p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Rendez</h3>
                <p>
                    Retournez vos emprunts très simplement via votre profil
                </p>
            </div>
            <div class="step">
                <div class="step-number">5</div>
                <h3>Recommencez</h3>
                <p>
                    Feuilletez à nouveau le catalogue, empruntez de nouveau articles, et profitez-en.
                    Recommencez autant que vous le souhaiterez !
                </p>
            </div>
        </div>
    </div>
</section>