<div class="admin-container">
    <div class="admin-header">
        <h1>Éditer l'emprunt</h1>
    </div>

    <?php if (has_flash_messages()): ?>
        <?php foreach (get_flash_messages() as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="flash <?php echo $type; ?>"><?php echo e($message); ?></div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($loan)): ?>
        <div class="form-card">
            <p>Emprunt introuvable.</p>
        </div>
    <?php else: ?>
        <div class="form-card">
            <form method="POST" action="<?= url('admin/loan_update/' . $loan['id']); ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                <p><strong>Média :</strong> <?= e($loan['media_title'] ?? '') ?> (<?= e($loan['media_type'] ?? '') ?>)</p>
                <p><strong>Utilisateur :</strong> <?= e($loan['user_name'] ?? '') ?> &lt;<?= e($loan['user_email'] ?? '') ?>&gt;</p>
                <p><strong>Date d'emprunt :</strong> <?= e(date('d/m/Y', strtotime($loan['loan_date']))); ?></p>
                <div class="form-row">
                    <label>Date de retour prévue (YYYY-MM-DD ou JJ/MM/AAAA):</label>
                    <input type="text" name="return_date" value="<?= e($loan['return_date'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label><input type="checkbox" name="mark_returned"> Marquer comme retourné</label>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="<?= url('admin/loans'); ?>" class="btn btn-ghost">Annuler</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>