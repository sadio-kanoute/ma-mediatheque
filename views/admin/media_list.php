<div class="admin-container">
    <div class="admin-header">
        <h1>Liste des médias</h1>
    </div>
    <div class="admin-actions"><a class="btn-link" href="<?= url('admin/media_add'); ?>">Ajouter un média</a></div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Genre</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medias as $media): ?>
                <tr>
                    <td><?php echo e((string)($media['title'] ?? '')); ?></td>
                    <td>
                        <?php
                        $mt = $media['media_type'] ?? '';
                        switch ($mt) {
                            case 'book':
                                echo 'Livre';
                                break;
                            case 'movie':
                                echo 'Film';
                                break;
                            case 'video_game':
                                echo 'Jeu vidéo';
                                break;
                            default:
                                echo e((string)$mt);
                        }
                        ?>
                    </td>
                    <td><?php echo e((string)($media['genre'] ?? '')); ?></td>
                    <td><?php echo (int)($media['stock'] ?? 0) > 0 ? (int)$media['stock'] : 'Indisponible'; ?></td>
                    <td>
                        <?php if (!empty($media['image_url'])): ?>
                            <img src="<?= url('uploads/covers/' . $media['image_url']); ?>"
                                alt="Couverture"
                                class="cover-thumb">
                        <?php else: ?>
                            <img src="<?= url('assets/images/default-cover.jpg'); ?>"
                                alt="Couverture par défaut"
                                class="cover-thumb">
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <div class="action-group">
                            <a href="<?= url('admin/media_edit/' . ($media['media_type'] ?? '') . '_' . ($media['id'] ?? '')); ?>" class="icon-btn" title="Modifier">✎</a>
                            <form method="post" action="<?= url('admin/media_delete/' . ($media['id'] ?? '') . '/' . ($media['media_type'] ?? '')); ?>" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                                <button type="submit" title="Supprimer" class="icon-btn danger">✖</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($total) && $total > $per_page): ?>
        <div class="admin-pagination">
            <?php $total_pages = ceil($total / $per_page); ?>
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <a href="<?= url('admin/media') . '?page=' . $p; ?>"><?= $p; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>