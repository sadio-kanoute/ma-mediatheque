<div class="admin-container">
    <div class="admin-header">
        <h1>Tableau de bord</h1>
    </div>
    <!-- Affichage des statistiques dans une mise en page en grille -->
    <section class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <span class="icon">👤</span>
                <h3>Utilisateurs</h3>
                <p><?= e($stats['users_count'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <span class="icon">📚</span>
                <h3>Livres</h3>
                <p><?= e($stats['media_stats']['books'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <span class="icon">🎬</span>
                <h3>Films</h3>
                <p><?= e($stats['media_stats']['movies'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <span class="icon">🎮</span>
                <h3>Jeux vidéo</h3>
                <p><?= e($stats['media_stats']['video_games'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <span class="icon">📦</span>
                <h3>Médias totaux</h3>
                <p><?= e($stats['media_count'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <span class="icon">📅</span>
                <h3>Emprunts actifs</h3>
                <p><?= e($stats['loans_count'] ?? 0) ?></p>
            </div>
        </div>
    </section>
    <h2 class="categorie">Répartition des médias par catégorie</h2>
    <table class="categorie">
        <tr>
            <th>Catégorie</th>
            <th>Nombre</th>
            <th>Barre</th>
        </tr>
        <tr>
            <td>Livres</td>
            <td><?= e($stats['media_stats']['books'] ?? 0) ?></td>
            <td>
                <?php
                $books = intval($stats['media_stats']['books'] ?? 0);
                $movies = intval($stats['media_stats']['movies'] ?? 0);
                $games = intval($stats['media_stats']['video_games'] ?? 0);
                $total_media = max(1, $books + $movies + $games);
                $pct = intval(round(($books / $total_media) * 100));
                ?>
                <div class="bar-track">
                    <div class="bar book bar-pct-<?= $pct; ?>" aria-hidden="true"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Films</td>
            <td><?= e($stats['media_stats']['movies'] ?? 0) ?></td>
            <td>
                <?php $pct = intval(round(($movies / $total_media) * 100)); ?>
                <div class="bar-track">
                    <div class="bar movie bar-pct-<?= $pct; ?>" aria-hidden="true"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>Jeux vidéo</td>
            <td><?= e($stats['media_stats']['video_games'] ?? 0) ?></td>
            <td>
                <?php $pct = intval(round(($games / $total_media) * 100)); ?>
                <div class="bar-track">
                    <div class="bar game bar-pct-<?= $pct; ?>" aria-hidden="true"></div>
                </div>
            </td>
        </tr>
    </table>

</div>