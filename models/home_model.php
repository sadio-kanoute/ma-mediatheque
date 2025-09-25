<?php


/**
 * -----------------------------
 * FONCTIONS POUR LE TABLEAU DE BORD
 * -----------------------------
 */



/**
 * Récupère le nombre total de médias par type
 */
function get_total_media_count()
{
    $books = db_select_one("SELECT COUNT(*) AS total FROM books")['total'] ?? 0;
    $movies = db_select_one("SELECT COUNT(*) AS total FROM movies")['total'] ?? 0;
    $video_games = db_select_one("SELECT COUNT(*) AS total FROM video_games")['total'] ?? 0;

    return $books + $movies + $video_games;
}



/**
 * Récupère toutes les statistiques du tableau de bord
 */
function get_dashboard_stats()
{
    $books = db_select_one("SELECT COUNT(*) AS total FROM books");
    $movies = db_select_one("SELECT COUNT(*) AS total FROM movies");
    $video_games = db_select_one("SELECT COUNT(*) AS total FROM video_games");

    return [
        'users_count' => count_users(),
        'media_count' => get_total_media_count(),
        'loans_count' => get_rentals_count(),
        'media_stats' => [
            'books' => isset($books['total']) ? $books['total'] : 0,
            'movies' => isset($movies['total']) ? $movies['total'] : 0,
            'video_games' => isset($video_games['total']) ? $video_games['total'] : 0,
        ],
    ];
}



/**
 * Récupère tous les médias
 */
function get_all_media($limit = null, $offset = 0)
{
    $query = "
        SELECT id, title, 'book' AS media_type, gender AS genre, stock FROM books
        UNION ALL
        SELECT id, title, 'movie' AS media_type, gender AS genre, stock FROM movies
        UNION ALL
        SELECT id, title, 'video_game' AS media_type, gender AS genre, stock FROM video_games
        ORDER BY title ASC
    ";
    if ($limit !== null) {
        $query .= " LIMIT $offset, $limit";
    }
    return db_select($query);
}

/**
 * Récupère un média par son ID et son type
 */
function get_media_by_id($id, $type)
{
    $db = db_connect();
    switch ($type) {
        case 'book':
        case 'livre':
            $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
            break;
        case 'movie':
        case 'film':
            $stmt = $db->prepare("SELECT * FROM movies WHERE id = ?");
            break;
        case 'video_game':
        case 'jeu':
            $stmt = $db->prepare("SELECT * FROM video_games WHERE id = ?");
            break;
        default:
            return false;
    }
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $result['media_type'] = $type === 'livre' ? 'book' : ($type === 'film' ? 'movie' : ($type === 'jeu' ? 'video_game' : $type));
        $result['genre'] = $result['gender'] ?? $result['genre']; // Fix: compatibilité gender/genre
    }
    return $result;
}

/**
 * Compte le nombre total de médias
 */
function get_media_count()
{
    $db = db_connect();
    return $db->query("
        SELECT 
            (SELECT COUNT(*) FROM books) +
            (SELECT COUNT(*) FROM movies) +
            (SELECT COUNT(*) FROM video_games) AS total
    ")->fetchColumn();
}

/**
 * Crée un nouveau média
 */
function create_media($type, $data)
{
    $db = db_connect();
    switch ($type) {
        case 'book':
            $stmt = $db->prepare("INSERT INTO books (title, writer, ISBN13, gender, page_number, synopsis, year, stock, available, image_url, upload_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $data['title'],
                $data['writer'] ?? '',
                $data['ISBN13'] ?? '',
                $data['genre'] ?? '',
                $data['page_number'] ?? 0,
                $data['synopsis'] ?? '',
                $data['year'] ?? 0,
                $data['stock'] ?? 1,
                1,
                $data['image_url'] ?? '',
                date('Y-m-d')
            ]);
        case 'movie':
            $stmt = $db->prepare("INSERT INTO movies (title, producer, year, gender, duration, synopsis, classification, stock, available, image_url, upload_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $data['title'],
                $data['producer'] ?? '',
                $data['year'] ?? 0,
                $data['genre'] ?? '',
                $data['duration'] ?? 0,
                $data['synopsis'] ?? '',
                $data['classification'] ?? '',
                $data['stock'] ?? 1,
                $data['stock'] ?? 1, // available = stock initialement
                $data['image_url'] ?? '',
                date('Y-m-d')
            ]);
        case 'video_game':
            $stmt = $db->prepare("INSERT INTO video_games (title, editor, plateform, gender, min_age, description, year, stock, available, image_url, upload_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $data['title'],
                $data['editor'] ?? '',
                $data['plateform'] ?? '', // Note: 'plateform' avec un 'e' selon votre DB
                $data['genre'] ?? '',
                $data['min_age'] ?? 0,
                $data['description'] ?? '', // Note: 'description' selon votre DB
                $data['year'] ?? 0,
                $data['stock'] ?? 1,
                $data['stock'] ?? 1, // available = stock initialement
                $data['image_url'] ?? '',
                date('Y-m-d')
            ]);
        default:
            return false;
    }
}

/**
 * Met à jour un média
 */
function update_media($id, $type, $data)
{
    $db = db_connect();
    switch ($type) {
        case 'book':
        case 'livre':
            $stmt = $db->prepare("UPDATE books SET title = ?, writer = ?, ISBN13 = ?, gender = ?, page_number = ?, synopsis = ?, year = ?, stock = ?, image_url = ? WHERE id = ?");
            return $stmt->execute([
                $data['title'],
                $data['writer'] ?? '',
                $data['ISBN13'] ?? '',
                $data['genre'] ?? '',
                $data['page_number'] ?? 0,
                $data['synopsis'] ?? '',
                $data['year'] ?? 0,
                $data['stock'] ?? 1,
                $data['image_url'] ?? '',
                $id
            ]);
        case 'movie':
        case 'film':
            // DB column is `duration`
            $stmt = $db->prepare("UPDATE movies SET title = ?, producer = ?, year = ?, gender = ?, duration = ?, synopsis = ?, classification = ?, stock = ?, image_url = ? WHERE id = ?");
            return $stmt->execute([
                $data['title'],
                $data['producer'] ?? '',
                $data['year'] ?? 0,
                $data['genre'] ?? '',


                $data['duration_m'] ?? $data['duration'] ?? 0,

                $data['synopsis'] ?? '',
                $data['classification'] ?? '',
                $data['stock'] ?? 1,
                $data['image_url'] ?? '',
                $id
            ]);
        case 'video_game':
        case 'jeu':
            // DB columns: plateform, description
            $stmt = $db->prepare("UPDATE video_games SET title = ?, editor = ?, plateform = ?, gender = ?, min_age = ?, description = ?, year = ?, stock = ?, image_url = ? WHERE id = ?");
            return $stmt->execute([
                $data['title'],
                $data['editor'] ?? '',


                $data['platform'] ?? $data['plateform'] ?? '',
                $data['genre'] ?? '',
                $data['min_age'] ?? 0,
                $data['synopsis'] ?? $data['description'] ?? '',


                $data['year'] ?? 0,
                $data['stock'] ?? 1,
                $data['image_url'] ?? '',
                $id
            ]);
        default:
            return false;
    }
}

/**
 * Supprime un média
 */
function delete_media($id, $type)
{
    $db = db_connect();
    switch ($type) {
        case 'book':
        case 'livre':
            $stmt = $db->prepare("DELETE FROM books WHERE id = ?");
            break;
        case 'movie':
        case 'film':
            $stmt = $db->prepare("DELETE FROM movies WHERE id = ?");
            break;
        case 'video_game':
        case 'jeu':
            $stmt = $db->prepare("DELETE FROM video_games WHERE id = ?");
            break;
        default:
            return false;
    }
    return $stmt->execute([$id]);
}

/**
 * Vérifie si l'image choisie est valide, la redimensionne selon les specs et l'ajoute au dossier /uploads/covers/
 * Selon cahier des charges : JPG/PNG/GIF, max 2Mo, redimensionnement automatique 300x400px max, stockage dans /uploads/covers/
 */
function media_upload_image($type, $data, $image)
{
    $errors = [];
    $success = "";

    // Pas d'image uploadée
    if (!$image || $image['error'] === UPLOAD_ERR_NO_FILE) {
        return ["errors" => $errors, "success" => $success];
    }

    // Vérification des erreurs d'upload
    if ($image['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors de l'upload de l'image.";
        return ["errors" => $errors, "success" => $success];
    }

    // Vérification de la taille (2 Mo maximum selon cahier des charges)
    if ($image['size'] > 2097152) {
        $errors[] = "Le fichier est trop volumineux (maximum 2 Mo).";
        return ["errors" => $errors, "success" => $success];
    }

    // Vérification de l'extension de fichier
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExt)) {
        $errors[] = "Format non supporté. Formats acceptés : JPG, PNG, GIF.";
        return ["errors" => $errors, "success" => $success];
    }

    // Vérification que c'est bien une image et récupération des dimensions
    $imageInfo = getimagesize($image['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = "Le fichier n'est pas une image valide.";
        return ["errors" => $errors, "success" => $success];
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mimeType = $imageInfo['mime'];

    // Vérification du type MIME
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedMimes)) {
        $errors[] = "Type MIME non supporté. Types acceptés : JPEG, PNG, GIF.";
        return ["errors" => $errors, "success" => $success];
    }

    // Vérification des dimensions minimales (100x100px selon cahier des charges)
    if ($width < 100 || $height < 100) {
        $errors[] = "L'image doit avoir au minimum 100x100 pixels.";
        return ["errors" => $errors, "success" => $success];
    }

    // Si pas d'erreurs, traitement de l'image
    if (empty($errors)) {
        // Création du nom de fichier unique
        $newName = uniqid("media_", true) . "." . $fileExt;
        
        // Dossier de destination selon cahier des charges
        $uploadDir = __DIR__ . "/../uploads/covers/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destinationPath = $uploadDir . $newName;

        // Redimensionnement selon cahier des charges (300px largeur max, 400px hauteur max)
        $maxWidth = 300;
        $maxHeight = 400;

        // Calculer les nouvelles dimensions en conservant le ratio
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        
        // Si l'image est plus petite que les dimensions max, on ne la redimensionne pas
        if ($ratio < 1) {
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Créer l'image source selon le type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($image['tmp_name']);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($image['tmp_name']);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($image['tmp_name']);
                break;
            default:
                $errors[] = "Type d'image non supporté pour le redimensionnement.";
                return ["errors" => $errors, "success" => $success];
        }

        if (!$sourceImage) {
            $errors[] = "Impossible de lire l'image pour le redimensionnement.";
            return ["errors" => $errors, "success" => $success];
        }

        // Créer l'image de destination
        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Redimensionner l'image
        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Sauvegarder l'image redimensionnée
        $saveSuccess = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $saveSuccess = imagejpeg($destImage, $destinationPath, 90); // Qualité 90%
                break;
            case 'image/png':
                $saveSuccess = imagepng($destImage, $destinationPath);
                break;
            case 'image/gif':
                $saveSuccess = imagegif($destImage, $destinationPath);
                break;
        }

        // Libérer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($destImage);

        if ($saveSuccess) {
            $success = $newName;
        } else {
            $errors[] = "Impossible d'enregistrer l'image redimensionnée.";
        }
    }

    return ["errors" => $errors, "success" => $success];
}

/**
 * Vérifie si un ISBN existe déjà dans la table books
 * @param string $isbn
 * @param int|null $exclude_id
 * @return bool
 */
function isbn_exists($isbn, $exclude_id = null)
{
    if (empty($isbn)) return false;
    $db = db_connect();
    $query = "SELECT COUNT(*) as count FROM books WHERE ISBN13 = ?";
    $params = [$isbn];
    if ($exclude_id) {
        $query .= " AND id != ?";
        $params[] = $exclude_id;
    }
    $res = db_select_one($query, $params);
    return ($res['count'] ?? 0) > 0;
}
