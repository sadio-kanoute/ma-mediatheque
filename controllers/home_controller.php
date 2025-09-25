<?php
// Contrôleur pour la page d'accueil

/**
 * Page d'accueil
 */
function home_index()
{
    $data = [
        'title' => 'Accueil',
        'message' => 'Bienvenue sur notre médiathèque en ligne !',
        'features' => [
            'Un catalogue complet avec un système de filtrage fonctionnel',
            'La possibilité de créer, gérer et modifier votre profil',
            'Un design moderne, simple et épuré',
            'Un système d\'emprunt et de retour pour les documents du catalogue',
            'Une sécurité au point, et une protection efficace de vos données'
        ]
    ];

    load_view_with_layout('home/index', $data);
}

/**
 * Page à propos
 */
function home_about()
{
    $data = [
        'title' => 'À propos',
        'content' => 'Cette application est un starter kit PHP MVC développé avec une approche procédurale.'
    ];

    load_view_with_layout('home/about', $data);
}

/**
 * Page contact
 */
function home_contact()
{
    $data = [
        'title' => 'Contact'
    ];

    if (is_post()) {
        $name = clean_input(post('name'));
        $email = clean_input(post('email'));
        $message = clean_input(post('message'));

        // Validation simple
        if (empty($name) || empty($email) || empty($message)) {
            set_flash('error', 'Tous les champs sont obligatoires.');
        } elseif (!validate_email($email)) {
            set_flash('error', 'Adresse email invalide.');
        } else {
            // Ici vous pourriez envoyer l'email ou sauvegarder en base
            set_flash('success', 'Votre message a été envoyé avec succès !');
            redirect('home/contact');
        }
    }

    load_view_with_layout('home/contact', $data);
}


/**
 * Page profile
 */
function home_profile()
{
    // Vérifier que l'utilisateur est connecté
    if (!is_logged_in()) {
        set_flash('error', 'Vous devez être connecté pour accéder à votre profil.');
        redirect('auth/login');
        return;
    }

    // Récupérer les données utilisateur depuis la session
    $session_user = $_SESSION['user'] ?? null;
    
    // Mapper les données de session vers le format attendu par la vue
    if ($session_user) {
        // Si on a des données de profil personnalisées stockées, les utiliser
        if (isset($_SESSION['user_profile'])) {
            $user = $_SESSION['user_profile'];
        } else {
            // Sinon, créer le profil à partir des vraies données de session
            $user = [
                'prenom' => $session_user['name'] ?? 'Utilisateur',
                'nom' => $session_user['last_name'] ?? 'Test',
                'email' => $session_user['email'] ?? 'test@example.com',
                'avatar' => 'https://www.shutterstock.com/image-illustration/blank-profile-photo-flat-face-260nw-2271909553.jpg'
            ];
            // Sauvegarder en session pour la prochaine fois
            $_SESSION['user_profile'] = $user;
        }
    } else {
        // Données par défaut si pas de session
        $user = [
            'prenom' => 'Utilisateur',
            'nom' => 'Test',
            'email' => 'test@example.com',
            'avatar' => 'https://www.shutterstock.com/image-illustration/blank-profile-photo-flat-face-260nw-2271909553.jpg'
        ];
        $_SESSION['user_profile'] = $user;
    }

    // Gérer le token CSRF
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $errors = [];
    $success = isset($_GET['saved']); // Après redirection avec ?saved=1

    // Traitement du formulaire de mise à jour
    if (is_post()) {
        $submitted_csrf = post('csrf_token', '');

        // Vérification CSRF
        if (!verify_csrf_token($submitted_csrf)) {
            $errors[] = "Jeton CSRF invalide - veuillez réessayer.";
        }

        if (empty($errors)) {
            $prenom = clean_input(post('prenom', ''));
            $nom = clean_input(post('nom', ''));
            $email = clean_input(post('email', ''));
            $avatar = clean_input(post('avatar', ''));

            // Validation selon les mêmes règles que l'inscription
            if (empty($prenom) || empty($nom)) {
                $errors[] = "Nom et prénom sont requis.";
            }
            if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\- ]{2,50}$/u', $prenom)) {
                $errors[] = 'Le prénom doit contenir uniquement des lettres (2-50 caractères).';
            }
            if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\- ]{2,50}$/u', $nom)) {
                $errors[] = 'Le nom doit contenir uniquement des lettres (2-50 caractères).';
            }
            if (!validate_email($email) || strlen($email) > 255) {
                $errors[] = 'Adresse email invalide ou trop longue (max 255 caractères).';
            }
            if (!empty($avatar) && !filter_var($avatar, FILTER_VALIDATE_URL)) {
                $errors[] = "URL de l'avatar invalide.";
            }

            if (empty($errors)) {
                // Mettre à jour les données utilisateur
                $_SESSION['user_profile'] = [
                    'prenom' => mb_convert_case($prenom, MB_CASE_TITLE, 'UTF-8'),
                    'nom' => mb_convert_case($nom, MB_CASE_TITLE, 'UTF-8'),
                    'email' => $email,
                    'avatar' => $avatar ?: 'https://www.shutterstock.com/image-illustration/blank-profile-photo-flat-face-260nw-2271909553.jpg'
                ];

                // Mettre à jour aussi les données principales de session
                if (isset($_SESSION['user'])) {
                    $_SESSION['user']['name'] = mb_convert_case($prenom, MB_CASE_TITLE, 'UTF-8');
                    $_SESSION['user']['last_name'] = mb_convert_case($nom, MB_CASE_TITLE, 'UTF-8');
                    $_SESSION['user']['email'] = $email;
                }

                // Nouveau token CSRF
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                set_flash('success', 'Profil mis à jour avec succès !');
                redirect('home/profile?saved=1');
                return;
            } else {
                // Conserver les valeurs saisies en cas d'erreur
                $user = [
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email ?: $user['email'],
                    'avatar' => $avatar ?: $user['avatar']
                ];
            }
        }
    }

    $data = [
        'title' => 'Mon Profil',
        'user' => $user,
        'errors' => $errors,
        'success' => $success,
        'csrf_token' => $_SESSION['csrf_token'],
        'edit_mode' => isset($_GET['edit']) && $_GET['edit'] === '1'
    ];

    load_view_with_layout('home/profile', $data);
}

/**
 * Page test
 */
function home_test()
{
    $data = [
        'title' => 'Page test',
        'message' => 'Bienvenue sur votre page test',
    ];

    load_view_with_layout('home/test', $data);
}

/** 
 * Page Upload - DÉSACTIVÉE : Seuls les admins peuvent ajouter des médias
 * Redirection vers l'interface admin si utilisateur admin, sinon page d'accueil
 */
function home_upload()
{
    // Vérifier si l'utilisateur est admin
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
        // Rediriger vers l'interface admin pour ajouter un média
        redirect('admin/media_add');
        return;
    }
    
    // Pour les utilisateurs normaux, rediriger vers l'accueil avec un message
    set_flash('info', 'Seuls les administrateurs peuvent ajouter des médias. Consultez le catalogue existant !');
    redirect('catalog/index');
}
