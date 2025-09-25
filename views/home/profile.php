<div class="profil-container">
    <div class="profil-card">
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                Profil mis à jour avec succès !
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><i class="fas fa-exclamation-triangle"></i> <?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!$edit_mode): ?>
            <!-- Mode affichage -->
            <div class="profil-header">
                <img src="<?= esc($user['avatar']) ?>" alt="Avatar de <?= esc($user['prenom']) ?>" class="profil-avatar">
                <h2><?= esc($user['prenom'] . ' ' . $user['nom']) ?></h2>
            </div>

            <div class="profil-info">
                <div class="profil-field">
                    <label>Email</label>
                    <div class="profil-field-value"><?= esc($user['email']) ?></div>
                </div>

                <div class="profil-field">
                    <label>Rôle</label>
                    <div class="profil-field-value">
                        <?php if (isset($_SESSION['user']['role'])): ?>
                            <?= $_SESSION['user']['role'] === 'admin' ? 'Administrateur' : 'Utilisateur' ?>
                        <?php else: ?>
                            Utilisateur
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="profil-buttons">
                <a href="<?= url('home/profile?edit=1') ?>" class="btn-profil btn-profil-primary">
                    <i class="fas fa-edit"></i>
                    Modifier le profil
                </a>
                <a href="<?= url('catalog/index') ?>" class="btn-profil btn-profil-secondary">
                    <i class="fas fa-book"></i>
                    Parcourir le catalogue
                </a>
            </div>

        <?php else: ?>
            <!-- Mode édition -->
            <div class="profil-header">
                <h2><i class="fas fa-user-edit"></i> Modifier le profil</h2>
            </div>

            <form method="post" action="<?= url('home/profile?edit=1') ?>" class="profil-edit-form">
                <input type="hidden" name="csrf_token" value="<?= esc($csrf_token) ?>">

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= esc($user['prenom']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= esc($user['nom']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= esc($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="avatar">URL Avatar</label>
                    <input type="url" id="avatar" name="avatar" value="<?= esc($user['avatar']) ?>" placeholder="https://...">
                    <small style="color: var(--text-muted); font-size: 0.8rem;">Optionnel : URL vers votre photo de profil</small>
                </div>

                <div class="profil-buttons">
                    <button type="submit" class="btn-profil btn-profil-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <a href="<?= url('home/profile') ?>" class="btn-profil btn-profil-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>