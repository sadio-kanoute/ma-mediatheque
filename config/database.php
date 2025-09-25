<?php
// Détection de l'environnement
$is_plesk = isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'sadio-kanoute') !== false ||
    strpos($_SERVER['SERVER_NAME'], 'plesk') !== false ||
    file_exists('/usr/local/psa') || // Répertoire typique Plesk Linux
    file_exists('C:\\Program Files (x86)\\Parallels\\Plesk') // Répertoire typique Plesk Windows
);

// Configuration de la base de données
if ($is_plesk) {
    // Configuration pour Plesk
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'sadio-kanoute_mediatheque'); // Format typique Plesk: username_dbname
    if (!defined('DB_USER')) define('DB_USER', 'sadio-kanoute');
    if (!defined('DB_PASS')) define('DB_PASS', 'Adama@1974');
    if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
} else {
    // Configuration pour développement local
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'php_mvc_app');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8');
}

// Configuration générale de l'application
if ($is_plesk) {
    // Configuration pour Plesk - ajustez le domaine selon votre configuration
    if (!defined('BASE_URL')) define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/public');
} else {
    // Configuration pour développement local
    if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/ma-mediatheque/public');
}

if (!defined('APP_NAME')) define('APP_NAME', 'Paris en culture');
if (!defined('APP_VERSION')) define('APP_VERSION', '1.0.0');

// Configuration des chemins
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('CONTROLLER_PATH')) define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
if (!defined('MODEL_PATH')) define('MODEL_PATH', ROOT_PATH . '/models');
if (!defined('VIEW_PATH')) define('VIEW_PATH', ROOT_PATH . '/views');
if (!defined('INCLUDE_PATH')) define('INCLUDE_PATH', ROOT_PATH . '/includes');
if (!defined('CORE_PATH')) define('CORE_PATH', ROOT_PATH . '/core');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');


// Dossiers uploads
define('UPLOADS_PATH', __DIR__ . '/../uploads');
define('COVERS_PATH', UPLOADS_PATH . '/covers');
define('COVERS_THUMBS_PATH', COVERS_PATH . '/thumbs');
define('DEFAULT_COVER', 'default.jpg'); // à placer dans /uploads/covers/
