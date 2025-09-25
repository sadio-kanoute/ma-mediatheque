<div class="admin-container">
    <div class="form-card">
        <h2><?php echo $media ? 'Modifier le média' : 'Ajouter un nouveau média'; ?></h2>

        <div class="alert alert-info">
            <strong>Instructions :</strong>
            <ol>
                <li>Sélectionnez le type de média</li>
                <li>Remplissez tous les champs généraux obligatoires</li>
                <li>Remplissez uniquement les champs spécifiques au type choisi</li>
                <li>Ajoutez une image (optionnel)</li>
            </ol>
        </div>

        <?php
        $type = $resolved_type ?? ($media['media_type'] ?? ($_POST['type'] ?? ($_GET['type'] ?? '')));
        ?>

        <form action="<?php echo $media ? url('admin/media_save/' . $media['media_type'] . '_' . $media['id']) : url('admin/media_save'); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <!-- ===== CHAMPS GÉNÉRAUX ===== -->
            <fieldset>
                <legend>📋 Informations générales (obligatoires pour tous)</legend>

                <div class="form-group">
                    <label for="type">Type de média <span class="required">*</span>:</label>
                    <select name="type" id="type" required>
                        <option value="">-- Sélectionner un type --</option>
                        <option value="livre" <?php echo $type === 'book' || $type === 'livre' ? 'selected' : ''; ?>>Livre</option>
                        <option value="film" <?php echo $type === 'movie' || $type === 'film' ? 'selected' : ''; ?>>Film</option>
                        <option value="jeu" <?php echo $type === 'video_game' || $type === 'jeu' ? 'selected' : ''; ?>>Jeu vidéo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Titre <span class="required">*</span>:</label>
                    <input type="text" name="title" id="title" value="<?php echo e($media['title'] ?? ''); ?>" maxlength="200" required>
                    <small>Entre 1 et 200 caractères</small>
                </div>

                <div class="form-group">
                    <label for="genre">Genre <span class="required">*</span>:</label>
                    <input type="text" name="genre" id="genre" value="<?php echo e($media['genre'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Nombre d'exemplaires <span class="required">*</span>:</label>
                    <input type="number" name="stock" id="stock" value="<?php echo $media['stock'] ?? 1; ?>" min="1" required>
                    <small>Minimum 1 exemplaire</small>
                </div>
            </fieldset>

            <!-- ===== CHAMPS SPÉCIFIQUES AUX LIVRES ===== -->
            <fieldset>
                <legend>📚 Informations pour les LIVRES seulement</legend>
                <div class="alert alert-warning">
                    <strong>⚠️ Ne remplir que si vous ajoutez un LIVRE</strong>
                </div>

                <div class="form-group">
                    <label for="writer">Auteur:</label>
                    <input type="text" name="writer" id="writer" value="<?php echo e($media['writer'] ?? ''); ?>" maxlength="100">
                    <small>Entre 2 et 100 caractères (obligatoire pour les livres)</small>
                </div>

                <div class="form-group">
                    <label for="ISBN13">ISBN:</label>
                    <input type="text" name="ISBN13" id="ISBN13" value="<?php echo e($media['ISBN13'] ?? ''); ?>" pattern="[0-9]{10}|[0-9]{13}">
                    <small>10 ou 13 chiffres, doit être unique (obligatoire pour les livres)</small>
                </div>

                <div class="form-group">
                    <label for="page_number">Nombre de pages:</label>
                    <input type="number" name="page_number" id="page_number" value="<?php echo $media['page_number'] ?? ''; ?>" min="1" max="9999">
                    <small>Entre 1 et 9999 pages (obligatoire pour les livres)</small>
                </div>

                <div class="form-group">
                    <label for="synopsis_book">Synopsis (livre):</label>
                    <textarea name="synopsis_book" id="synopsis_book" rows="4"><?php echo e($media['synopsis'] ?? ''); ?></textarea>
                    <small>Obligatoire pour les livres</small>
                </div>

                <div class="form-group">
                    <label for="year_book">Année de publication (livre):</label>
                    <input type="number" name="year_book" id="year_book" value="<?php echo $media['year'] ?? ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                    <small>Entre 1900 et <?php echo date('Y'); ?> (obligatoire pour les livres)</small>
                </div>
            </fieldset>

            <!-- ===== CHAMPS SPÉCIFIQUES AUX FILMS ===== -->
            <fieldset>
                <legend>🎬 Informations pour les FILMS seulement</legend>
                <div class="alert alert-warning">
                    <strong>⚠️ Ne remplir que si vous ajoutez un FILM</strong>
                </div>

                <div class="form-group">
                    <label for="producer">Réalisateur:</label>
                    <input type="text" name="producer" id="producer" value="<?php echo e($media['producer'] ?? ''); ?>" maxlength="100">
                    <small>Entre 2 et 100 caractères (obligatoire pour les films)</small>
                </div>

                <div class="form-group">
                    <label for="duration">Durée (minutes):</label>
                    <input type="number" name="duration" id="duration" value="<?php echo $media['duration'] ?? ($media['duration_m'] ?? ''); ?>" min="1" max="999">
                    <small>Entre 1 et 999 minutes (obligatoire pour les films)</small>
                </div>

                <div class="form-group">
                    <label for="synopsis_movie">Synopsis (film):</label>
                    <textarea name="synopsis_movie" id="synopsis_movie" rows="4"><?php echo e($media['synopsis'] ?? ''); ?></textarea>
                    <small>Obligatoire pour les films</small>
                </div>

                <div class="form-group">
                    <label for="classification">Classification:</label>
                    <?php
                    $current_class = $media['classification'] ?? '';
                    $allowed_class = ['Tous publics', '-12', '-16', '-18'];
                    ?>
                    <select name="classification" id="classification">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($allowed_class as $c): ?>
                            <option value="<?php echo $c; ?>" <?php echo ($current_class === $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                        <?php if ($current_class !== '' && !in_array($current_class, $allowed_class)): ?>
                            <option value="<?php echo e($current_class); ?>" selected><?php echo e($current_class); ?> (actuel)</option>
                        <?php endif; ?>
                    </select>
                    <small>Obligatoire pour les films</small>
                </div>

                <div class="form-group">
                    <label for="year_movie">Année de sortie (film):</label>
                    <input type="number" name="year_movie" id="year_movie" value="<?php echo $media['year'] ?? ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                    <small>Entre 1900 et <?php echo date('Y'); ?> (obligatoire pour les films)</small>
                </div>
            </fieldset>

            <!-- ===== CHAMPS SPÉCIFIQUES AUX JEUX VIDÉO ===== -->
            <fieldset>
                <legend>🎮 Informations pour les JEUX VIDÉO seulement</legend>
                <div class="alert alert-warning">
                    <strong>⚠️ Ne remplir que si vous ajoutez un JEU VIDÉO</strong>
                </div>

                <div class="form-group">
                    <label for="editor">Éditeur:</label>
                    <input type="text" name="editor" id="editor" value="<?php echo e($media['editor'] ?? ''); ?>" maxlength="100">
                    <small>Entre 2 et 100 caractères (obligatoire pour les jeux)</small>
                </div>

                <div class="form-group">
                    <label for="platform">Plateforme:</label>
                    <?php
                    $current_platform = $media['platform'] ?? '';
                    $allowed_platforms = ['PC', 'PlayStation', 'Xbox', 'Nintendo', 'Mobile'];
                    ?>
                    <select name="platform" id="platform">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($allowed_platforms as $p): ?>
                            <option value="<?php echo $p; ?>" <?php echo ($current_platform === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                        <?php endforeach; ?>
                        <?php if ($current_platform !== '' && !in_array($current_platform, $allowed_platforms)): ?>
                            <option value="<?php echo e($current_platform); ?>" selected><?php echo e($current_platform); ?> (actuel)</option>
                        <?php endif; ?>
                    </select>
                    <small>Obligatoire pour les jeux</small>
                </div>

                <div class="form-group">
                    <label for="min_age">Âge minimum:</label>
                    <?php
                    $current_age = isset($media['min_age']) ? intval($media['min_age']) : null;
                    $allowed_ages = [3, 7, 12, 16, 18];
                    ?>
                    <select name="min_age" id="min_age">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($allowed_ages as $a): ?>
                            <option value="<?php echo $a; ?>" <?php echo ($current_age === $a) ? 'selected' : ''; ?>><?php echo $a; ?> ans</option>
                        <?php endforeach; ?>
                        <?php if ($current_age !== null && !in_array($current_age, $allowed_ages)): ?>
                            <option value="<?php echo $current_age; ?>" selected><?php echo $current_age; ?> ans (actuel)</option>
                        <?php endif; ?>
                    </select>
                    <small>Obligatoire pour les jeux</small>
                </div>

                <div class="form-group">
                    <label for="synopsis_game">Synopsis/Description (jeu):</label>
                    <textarea name="synopsis_game" id="synopsis_game" rows="4"><?php echo e($media['synopsis'] ?? ''); ?></textarea>
                    <small>Obligatoire pour les jeux</small>
                </div>

                <div class="form-group">
                    <label for="year_game">Année de sortie (jeu):</label>
                    <input type="number" name="year_game" id="year_game" value="<?php echo $media['year'] ?? ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                    <small>Entre 1900 et <?php echo date('Y'); ?> (obligatoire pour les jeux)</small>
                </div>
            </fieldset>

            <!-- ===== IMAGE DE COUVERTURE ===== -->
            <fieldset>
                <legend>🖼️ Image de couverture</legend>

                <div class="form-group">
                    <label for="image">Image de couverture (optionnel):</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif">
                    <small>Formats acceptés : JPG, PNG, GIF. Taille max : 2 Mo. L'image sera automatiquement redimensionnée.</small>

                    <?php if (!empty($media['image_url'])): ?>
                        <div class="current-image">
                            <p>Image actuelle :</p>
                            <img src="<?= url('uploads/covers/' . $media['image_url']); ?>" alt="Couverture actuelle" style="max-width: 150px; max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $media ? 'Mettre à jour le média' : 'Ajouter le média'; ?>
                </button>
                <a href="<?= url('admin/media'); ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>

        <style>
            .alert {
                padding: 10px;
                margin: 10px 0;
                border: 1px solid;
                border-radius: 4px;
            }

            .alert-info {
                background-color: #d1ecf1;
                border-color: #bee5eb;
                color: #0c5460;
            }

            .alert-warning {
                background-color: #fff3cd;
                border-color: #ffeaa7;
                color: #856404;
            }

            fieldset {
                border: 1px solid #ddd;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
            }

            legend {
                font-weight: bold;
                padding: 0 10px;
            }

            .required {
                color: red;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-actions {
                margin-top: 30px;
                text-align: center;
            }

            .btn {
                padding: 10px 20px;
                margin: 0 10px;
                border: none;
                border-radius: 4px;
                text-decoration: none;
                display: inline-block;
            }

            .btn-primary {
                background-color: #007bff;
                color: white;
            }

            .btn-secondary {
                background-color: #6c757d;
                color: white;
            }

            .current-image {
                margin-top: 10px;
                padding: 10px;
                background-color: #f8f9fa;
                border-radius: 4px;
            }
        </style>

    </div>
</div>