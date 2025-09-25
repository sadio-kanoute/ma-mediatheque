<?php
/**
 * Fichier d'amorçage pour les tests et l'initialisation de l'application
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Charger la configuration avant tout (pour les constantes DB)
require_once __DIR__ . '/config/database.php';

// Définir le chemin racine du projet (seulement si pas déjà défini)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Définir les chemins des dossiers (protégé contre redefinition)
if (!defined('CORE_PATH')) define('CORE_PATH', ROOT_PATH . '/core');
if (!defined('CONTROLLER_PATH')) define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
if (!defined('MODEL_PATH')) define('MODEL_PATH', ROOT_PATH . '/models');
if (!defined('VIEW_PATH')) define('VIEW_PATH', ROOT_PATH . '/views');
if (!defined('INCLUDE_PATH')) define('INCLUDE_PATH', ROOT_PATH . '/includes');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('DATABASE_PATH')) define('DATABASE_PATH', ROOT_PATH . '/database');

// Charger les fichiers core (après définition des chemins)
require_once CORE_PATH . '/database.php';
require_once CORE_PATH . '/router.php';
require_once CORE_PATH . '/view.php';

// Charger les fichiers utilitaires
require_once INCLUDE_PATH . '/helpers.php';

// Charger tous les modèles (avec check pour éviter erreurs)
foreach (glob(MODEL_PATH . '/*.php') as $model_file) {
    if (file_exists($model_file)) {
        require_once $model_file;
    }
}

// Charger tous les contrôleurs (avec check)
foreach (glob(CONTROLLER_PATH . '/*.php') as $controller_file) {
    if (file_exists($controller_file)) {
        require_once $controller_file;
    }
}

// Configuration pour les tests
if (defined('TESTING')) {
    // Désactiver l'affichage des erreurs pour les tests
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Configuration de test de base de données
    if (!defined('DB_NAME')) {
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'php_mvc_app_test');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_CHARSET', 'utf8');
    }
} else {
    // Configuration pour le développement
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/**
 * Fonction pour créer les tables de test
 */
function setup_test_database() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
            DB_USER,
            DB_PASS
        );
        
        // Créer la base de données de test
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        
        // Créer les tables
        $schema = file_get_contents(DATABASE_PATH . '/schema.sql');
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !str_starts_with($statement, '--')) {
                $pdo->exec($statement);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur création base de test : " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour nettoyer la base de données de test
 */
function cleanup_test_database() {
    if (defined('TESTING') && DB_NAME === 'php_mvc_app_test') {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS
            );
            
            $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur suppression base de test : " . $e->getMessage());
            return false;
        }
    }
    return false;
}

/**
 * Fonction d'aide pour les tests
 */
function create_test_user($name = 'Test User', $email = 'test@example.com', $password = 'password123') {
    return create_user($name, $email, $password);
}

/**
 * Fonction pour vider les messages flash
 */
function clear_flash_messages() {
    unset($_SESSION['flash_messages']);
}

/**
 * Fonction pour simuler une connexion utilisateur
 */
function login_test_user($user_id, $name = 'Test User', $email = 'test@example.com') {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
}
?>