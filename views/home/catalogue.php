<?php
//navbar
//titre
//phrase d'intro
//barre de recherche + filtres
//Livres
//grille affichage livres
//Films
//grille affichage films
//Jeux vidéos
//grille affichage jeux vidéos

/*
    *Fonctions routeur.php
    _utiliser la fonction function_dispatch(){}
*/

/*
    *Fonctions model.php
    _créer une page catalogue_model.php
*/

/*
    *Fonctions view.php
    _créer une fonction load_view_with_layout(){}
*/

/*
    *Fonctions home_controller.php    
    _créer une fonction home_catalogue(){}
    _si fonctionnel et trop volumineux, créer un dossier catalogue/, un fichier 
    catalogue_controleur.php et une fonction home_catalogue
*/



//[ [Accueil] [A propos] [Catalogue] [Profil] [Contact] [Deconnexion] ]
//titre
//phrase d'intro
//[barre de recherche] [Filtres] [Rechercher]
//Livres
//[] [] []
//[] [] []
//Films
//[] [] []
//[] [] []
//Jeux vidéos
//[] [] []
//[] [] []

?>

<div class="page-header">
    <div class="container">
        <h1><?php e($title); ?></h1>
    </div>
</div>

<section class="content">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">

                <p><?php e($content); ?></p>

            </div>
        </div>
</section>

<section>
    <form method="GET" action="">
        <legend>Filtres</legend>
        <input type="text" placeholder="Entrez votre recherche">
        <div class="à remplir">
            <select name="type-filter" id="type-filter">
                <option value="type-display">
                    --type--
                </option>
                <option value="books">
                    Livre
                </option>
                <option value="movies">
                    Film
                </option>
                <option value="video-games">
                    Jeu vidéo
                </option>
            </select>
            <select name="gender-filter" id="gender-filter">
                <option value="gender-display">
                    --genre--
                </option>
                <option value="science-fiction">
                    --science-fiction--
                </option>
                <option value="bac-a-sable">
                    --bac à sable--
                </option>
            </select>
            <select name="stock-filter" id="stock-filter">
                <option value="stock-display">
                    --disponibilité--
                </option>
                <option value="free">
                    disponible
                </option>
                <option value="loaned">
                    emprunté
                </option>
                <option value="all">
                    tous
                </option>
            </select>
        </div>
        <input type="submit" name="submit" value="Rechercher">
        <!--
            <select name="style" id="style-select">
                <option value="">--Filtres--</option>
                <option value="à remplir">
                    Par type
                </option>
                <option value="à remplir">
                    Par genre
                </option>
                <option value="à remplir">
                    Par disponibilité
                </option>                
            </select>
        -->
    </form>
</section>
<!--tests var_dumps-->
<?php var_dump("Valeur de type :", $type); ?>
<?php var_dump($_GET) ?>
<!--Grille d'affichage des médias du catalogue-->
<section>
    <h2>📚 Livres</h2>
    <div class="grid">
        <?php foreach ($data['books'] as $book): ?>
            <div class="doc">
                <?= e($book['title']) ?>
                <?= e($book['synopsis']) ?>
                <?= htmlspecialchars($book['gender']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>🎬 Films</h2>
    <div class="grid">
        <?php foreach ($data['movies'] as $movies): ?>
            <div class="doc">
                <?= htmlspecialchars($movies['title']) ?>
                <?= htmlspecialchars($movies['producer']) ?>
                <?= htmlspecialchars($movies['synopsis']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>🎮 Jeux vidéo</h2>
    <div class="grid">
        <?php foreach ($data['video-games'] as $video_games): ?>
            <div class="doc">
                <?= htmlspecialchars($video_games['title']) ?>
                <?= htmlspecialchars($video_games['editor']) ?>
                <?= htmlspecialchars($video_games['description']) ?>
            </div>
        <?php endforeach; ?>
</section>
<h1>======================</h1>
<section class="catalogue-grid">
    <!--Filtre par type de média-->
    <?php if (!empty($data['type'])): ?>
        <h3><?php echo $data['type-title'] ?></h3>
        <?php foreach ($data['type'] as $doc): ?>
            <p>titre</p>
            <?= htmlspecialchars($doc['title']) ?>
            <p>synopsis</p>
            <?= htmlspecialchars($doc['synopsis'] ?? '') ?>
            <p>genre</p>
            <?= htmlspecialchars($doc['gender'] ?? '') ?>
        <?php endforeach; ?>
    <?php endif ?>
    <!--Filtre par genre de média-->
    <?php if (!empty($data['gender-filter'])): ?>
    <?php endif ?>
    <!--Filtre par disponibilité des médias-->
    <?php if (!empty($data['stock-filter'])): ?>
        <?php foreach ($data['stock'] as $doc): ?>
            <p>titre</p>
            <?= htmlspecialchars($doc['title']) ?>
            <p>synopsis</p>
            <?= htmlspecialchars($doc['synopsis'] ?? '') ?>
            <p>genre</p>
            <?= htmlspecialchars($doc['gender'] ?? '') ?>
        <?php endforeach; ?>
    <?php endif ?>

    <?php //endif 
    ?>
</section>
<h1>======================</h1>
<section>
    <h2>📚 Catalogue filtré</h2>
    <div class="grid">
        <?php if (!empty($data['type'])): ?>
            <h3><?php echo $data['type-message'] ?></h3>
            <?php foreach ($data['type'] as $doc): ?>
                <?= htmlspecialchars($doc['title']) ?>
                <?= htmlspecialchars($doc['synopsis'] ?? '') ?>
                <?= htmlspecialchars($doc['gender'] ?? '') ?>
            <?php endforeach ?>
        <?php endif ?>
        <?php if (!empty($data['gender-filter'])): ?>
            <?php foreach ($data['gender-filter'] as $doc): ?>
                <div class="doc">
                    <?= htmlspecialchars($doc['title']) ?>
                    <?= htmlspecialchars($doc['synopsis'] ?? '') ?>
                    <?= htmlspecialchars($doc['gender'] ?? '') ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- Si pas de filtre, afficher tout -->
            <h3>📚 Livres</h3>
            <div class="grid">
                <?php foreach ($data['books'] as $book): ?>
                    <div class="doc">
                        <?= htmlspecialchars($book['title']) ?>
                        <?= htmlspecialchars($book['synopsis']) ?>
                        <?= htmlspecialchars($book['gender']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>🎬 Films</h3>
            <div class="grid">
                <?php foreach ($data['movies'] as $movie): ?>
                    <div class="doc">
                        <?= htmlspecialchars($movie['title']) ?>
                        <?= htmlspecialchars($movie['producer']) ?>
                        <?= htmlspecialchars($movie['synopsis']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>🎮 Jeux vidéo</h3>
            <div class="grid">
                <?php foreach ($data['video-games'] as $game): ?>
                    <div class="doc">
                        <?= htmlspecialchars($game['title']) ?>
                        <?= htmlspecialchars($game['editor']) ?>
                        <?= htmlspecialchars($game['description']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>