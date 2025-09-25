<div class="admin-container">
    <div class="admin-header">
        <h1>Détails utilisateur: <?= e($user['name'] . ' ' . $user['last_name']); ?></h1>
    </div>

    <?php if ($user): ?>
        <div class="form-card">
            <p><strong>ID:</strong> <?= $user['id']; ?></p>
            <p><strong>Nom:</strong> <?= e($user['name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>Email:</strong> <?= e($user['email']); ?></p>
            <p><strong>Rôle:</strong> <?= e($user['role']); ?></p>
            <!-- Role change form (admin only) -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                <form method="post" action="<?= url('admin/user_update_role/' . $user['id']); ?>" class="form-actions">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                    <label for="role_select">Modifier le rôle :</label>
                    <select id="role_select" name="role">
                        <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                    </select>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $user['id']): ?>
                        <p class="flash error">Vous ne pouvez pas retirer vos propres droits administrateur.</p>
                    <?php endif; ?>
                    <div class="form-actions-row">
                        <button type="submit" class="btn btn-primary">Mettre à jour le rôle</button>
                        <a href="<?= url('admin/users'); ?>" class="btn btn-ghost">Retour</a>
                    </div>
                </form>
                <div class="detail">
            <?php endif; ?>
            <p><strong>Inscrit le:</strong> <?= $user['created_at']; ?></p>

            <h3>Statistiques d'utilisation</h3>
            <ul>
                <li>Emprunts totaux: <?= $user['total_loans']; ?></li>
                <li>Emprunts actifs: <?= $user['active_loans']; ?></li>
                <li>Emprunts en retard: <?= count($user['overdue_loans']); ?></li>
            </ul>
        </div>

        <h2>Emprunts actuels</h2>
        <?php if (!empty($user['loans'])): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Média</th>
                        <th>Date emprunt</th>
                        <th>Retour prévu</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_filter($user['loans'], fn($l) => !$l['returned_at']) as $loan): ?>
                        <tr>
                            <td><?= e($loan['media_title']); ?></td>
                            <td><?= $loan['loan_date']; ?></td>
                            <td><?= $loan['return_date']; ?></td>
                            <td><?= (strtotime($loan['return_date']) < time()) ? 'En retard' : 'OK'; ?></td>
                            <td>
                                <div class="action-group">
                                    <form method="post" action="<?= url('admin/loan_return/' . $loan['id']); ?>" onsubmit="return confirm('Forcer le retour ?')" class="inline-form">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                                        <button type="submit" class="icon-btn danger" title="Retour forcé">↺</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun emprunt en cours.</p>
        <?php endif; ?>

        <h2>Emprunts en retard</h2>
        <?php if (!empty($user['overdue_loans'])): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Média</th>
                        <th>Date emprunt</th>
                        <th>Retour prévu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user['overdue_loans'] as $loan): ?>
                        <tr>
                            <td><?= e($loan['media_title']); ?></td>
                            <td><?= $loan['loan_date']; ?></td>
                            <td><?= $loan['return_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun emprunt en retard.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="form-card">
            <p>Utilisateur introuvable.</p>
        </div>
    <?php endif; ?>
</div>
</div>