<div class="admin-container">
    <div class="admin-header">
        <h1>Liste des utilisateurs</h1>
    </div>

    <?php if (has_flash_messages()): ?>
        <?php foreach (get_flash_messages() as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="admin-flash <?php echo $type; ?>">
                    <?php echo e($message); ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['id']); ?></td>
                    <td><?= e($user['name']); ?></td>
                    <td><?= e($user['last_name']); ?></td>
                    <td><?= e($user['email']); ?></td>
                    <td><?= e($user['role'] ?? 'user'); ?></td>
                    <td><?= e($user['created_at']); ?></td>
                    <td>
                        <a class="voir" href="<?= url('admin/user_detail/' . $user['id']); ?>">details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>