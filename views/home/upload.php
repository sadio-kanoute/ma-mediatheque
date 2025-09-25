<!-- Formulaire -->

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <label>Type de média :</label>
  <select name="type">
    <option value="">-- Choisir --</option>
    <option value="book" <?= (($data['type'] ?? '') === 'book' ? 'selected' : '') ?>>Livre</option>
    <option value="movie" <?= (($data['type'] ?? '') === 'movie' ? 'selected' : '')  ?>>Film</option>
    <option value="video_game" <?= (($data['type'] ?? '') === 'video_game' ? 'selected' : '')   ?>>Jeu vidéo</option>
  </select>
  <input type="submit" name="submit" value="Selectionner">
  <br><br>

  <?php if (($data['type'] ?? '') === 'book'): ?>
    Titre : <input name="title" value="<?= e($data['title'] ?? '') ?>" required><br>
    Auteur : <input name="writer" value="<?= e($data['writer'] ?? '') ?>" required><br>
    ISBN : <input name="ISBN13" value="<?= e($data['ISBN13'] ?? '') ?>" required><br>
    Pages : <input type="number" name="page_number" value="<?= e($data['page_number'] ?? '') ?>" required><br>
    Synopsis : <input type="text" name="synopsis" value="<?= e($data['synopsis'] ?? '') ?>" required><br>
    Année : <input type="number" name="year" value="<?= e($data['year'] ?? '') ?>" required><br>
    Stock : <input type="number" name="stock" value="<?= e($data['stock'] ?? '') ?>" required><br>
    Genre :
    <select name="genre">
      <?php
      $gender = ["", "Classique", "Science-fiction", "Drame", "Romance", "Fantaisie", "Crime", "Action"];
      $sel = $data['genre'] ?? '';
      foreach ($gender as $g) {
        $lab = $g ?: '-- Choisir --';
        echo "<option value=\"" . e($g) . "\" " . ($sel === $g ? 'selected' : '') . ">$lab</option>";
      }
      ?>
    </select><br>

  <?php elseif (($data['type'] ?? '') === 'movie'): ?>
    Titre : <input name="title" value="<?= e($data['title'] ?? '') ?>" required><br>
    Réalisateur : <input name="producer" value="<?= e($data['producer'] ?? '') ?>" required><br>
    Année : <input type="number" name="year" value="<?= e($data['year'] ?? '') ?>" required><br>
    Durée (min) : <input type="number" name="duration" value="<?= e($data['duration'] ?? '') ?>" required><br>
    Synopsis : <input type="text" name="synopsis" value="<?= e($data['synopsis'] ?? '') ?>" required><br>
    Stock : <input type="number" name="stock" value="<?= e($data['stock'] ?? '') ?>" required><br>
    Classification :
    <select name="classification">
      <?php
      $classes = ["", "Tous publics", "-10", "-12", "-16", "-18"];
      $sel = $data['classification'] ?? '';
      foreach ($classes as $c) {
        $lab = $c ?: '-- Choisir --';
        echo "<option value=\"" . e($c) . "\" " . ($sel === $c ? 'selected' : '') . ">$lab</option>";
      }
      ?>
    </select><br>
    Genre :
    <select name="genre">
      <?php
      $gender = ["", "Classique", "Science-fiction", "Drame", "Romance", "Fantaisie", "Crime", "Action"];
      $sel = $data['genre'] ?? '';
      foreach ($gender as $g) {
        $lab = $g ?: '-- Choisir --';
        echo "<option value=\"" . e($g) . "\" " . ($sel === $g ? 'selected' : '') . ">$lab</option>";
      }
      ?>
    </select><br>

  <?php elseif (($data['type'] ?? '') === 'video_game'): ?>
    Titre : <input name="title" value="<?= e($data['title'] ?? '') ?>" required><br>
    Éditeur : <input name="editor" value="<?= e($data['editor'] ?? '') ?>" required><br>
    Synopsis : <input type="text" name="synopsis" value="<?= e($data['synopsis'] ?? '') ?>" required><br>
    Année : <input type="number" name="year" value="<?= e($data['year'] ?? '') ?>" required><br>
    Stock : <input type="number" name="stock" value="<?= e($data['stock'] ?? '') ?>" required><br>
    Plateforme :
    <select name="platform">
      <?php
      $plats = ["PC", "PlayStation", "Xbox", "Nintendo", "Mobile"];
      $sel = $data['platform'] ?? '';
      foreach ($plats as $p) {
        echo "<option value=\"" . e($p) . "\" " . ($sel === $p ? 'selected' : '') . ">$p</option>";
      }
      ?>
    </select><br>
    Âge minimum :
    <select name="min_age">
      <?php
      $ages = ["3", "7", "12", "16", "18"];
      $sel = $data['min_age'] ?? '';
      foreach ($ages as $a) {
        echo "<option value=\"" . e($a) . "\" " . ($sel === $a ? 'selected' : '') . ">$a</option>";
      }
      ?>
    </select><br>
    Genre :
    <select name="genre">
      <?php
      $gender = ["", "Action", "RPG", "Plateforme", "Aventure", "Simulation"];
      $sel = $data['genre'] ?? '';
      foreach ($gender as $g) {
        $lab = $g ?: '-- Choisir --';
        echo "<option value=\"" . e($g) . "\" " . ($sel === $g ? 'selected' : '') . ">$lab</option>";
      }
      ?>
    </select><br>
  <?php endif; ?>

  <?php if ($data['type']): ?>
    <br>Image : <input type="file" name="image"><br><br>
    <button type="submit" name="submit_button">Valider</button>
  <?php endif; ?>
</form>

<?php
// Affichage des erreurs
if (!empty($data['errors'])) {
  echo "<ul style='color:red;'>";
  foreach ($data['errors'] as $e) echo "<li>" . e($e) . "</li>";
  echo "</ul>";
}

// Affichage du succès
if (!empty($data['success'])) {
  echo "<p style='color:green;'>Image uploadée : " . e($data['success']) . "</p>";
}
?>