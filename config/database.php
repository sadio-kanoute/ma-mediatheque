<?php
// Configuration de la base de données
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'php_mvc_app');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8');

// Configuration générale de l'application

if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/mediatheque_paris_grp1/public');

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
