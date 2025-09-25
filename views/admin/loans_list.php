<div class="admin-container">
    <div class="admin-header">
        <h1>Liste des emprunts</h1>
    </div>

    <?php if (has_flash_messages()): ?>
        <?php foreach (get_flash_messages() as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="flash <?php echo $type; ?>">
                    <?php echo e($message); ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($loans)): ?>
        <p>Aucun emprunt trouvé.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Média</th>
                    <th>Utilisateur</th>
                    <th>Date d'emprunt</th>
                    <th>Date d'échéance</th>
                    <th>Statut</th>
                    <th>Retour</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loans as $loan): ?>
                    <?php
                    $is_overdue = !$loan['returned_at'] && strtotime($loan['return_date']) < time();
                    $status_class = $is_overdue ? 'overdue' : ($loan['returned_at'] ? 'returned' : 'in-progress');
                    $return_date = $loan['returned_at'] ? date('d/m/Y', strtotime($loan['returned_at'])) : 'Non retourné';
                    ?>
                    <tr class="<?php echo $status_class; ?>">
                        <td><?php echo e($loan['media_title'] . ' (' . $loan['media_type'] . ') ' . ($loan['media_genre'] ?? '')); ?></td>
                        <td><?php echo e($loan['user_name'] . ' <' . ($loan['user_email'] ?? '') . '>'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($loan['loan_date'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($loan['return_date'])); ?></td>
                        <td><?php echo $is_overdue ? 'En retard (' . floor((time() - strtotime($loan['return_date'])) / (3600 * 24)) . ')' : ($loan['returned_at'] ? 'Retourné' : 'En cours'); ?></td>
                        <td><?php echo $return_date; ?></td>
                        <td class="actions">
                            <div class="action-group">
                                <form method="post" action="<?= url('admin/loan_return/' . $loan['id']); ?>" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                                    <button type="submit" title="Retour" class="icon-btn">↺</button>
                                </form>
                                <a href="<?= url('admin/loan_edit/' . $loan['id']); ?>" class="icon-btn" title="Éditer">✎</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>