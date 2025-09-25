<?php
// Contrôleur d'authentification corrigé selon cahier des charges

/**
 * Page de connexion
 */
function auth_login() {
    // Rediriger si déjà connecté
    if (is_logged_in()) {
        redirect('home');
    }

    $data = ['title' => 'Connexion'];

    if (is_post()) {
        if (!verify_csrf_token(post('csrf_token', ''))) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('auth/login');
            return;
        }
        $email = clean_input(post('email'));
        $password = post('password');

        if (empty($email) || empty($password)) {
            set_flash('error', 'Email et mot de passe obligatoires.');
        } else {
            $user = get_user_by_email($email);
          


            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                session_regenerate_id(true); // ⚡ sécuriser contre fixation

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'last_activity' => time() // ⚡ suivi expiration
                ];
                session_write_close();

                set_flash('success', 'Connexion réussie !');
                redirect('home');
            } else {
                set_flash('error', 'Email ou mot de passe incorrect.');
            }
        }
    }

    load_view_with_layout('auth/login', $data);
}

/**
 * Page d'inscription
 */
function auth_register() {
    if (is_logged_in()) {
        redirect('home');
    }

    $data = ['title' => 'Inscription'];

    if (is_post()) {
        if (!verify_csrf_token(post('csrf_token', ''))) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('auth/register');
            return;
        }
        $name = mb_convert_case(clean_input(post('name')), MB_CASE_TITLE, 'UTF-8');
        $last_name = mb_convert_case(clean_input(post('last_name')), MB_CASE_TITLE, 'UTF-8');
        $email = clean_input(post('email'));
        $password = post('password');
        $confirm_password = post('confirm_password');

        // Validation stricte
        if (empty($name) || empty($last_name) || empty($email) || empty($password)) {
            set_flash('error', 'Tous les champs sont obligatoires.');
        } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\- ]{2,50}$/u', $name)) {
            set_flash('error', 'Le prénom doit contenir uniquement des lettres (2-50 caractères).');
        } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\- ]{2,50}$/u', $last_name)) {
            set_flash('error', 'Le nom doit contenir uniquement des lettres (2-50 caractères).');
        } elseif (!validate_email($email) || strlen($email) > 255) {
            set_flash('error', 'Adresse email invalide ou trop longue (max 255 caractères).');
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères avec 1 majuscule, 1 minuscule et 1 chiffre.');
        } elseif ($password !== $confirm_password) {
            set_flash('error', 'Les mots de passe ne correspondent pas.');
        } elseif (get_user_by_email($email)) {
            set_flash('error', 'Cette adresse email est déjà utilisée.');
        } else {
            // Création utilisateur (le hashing est effectué dans create_user)
            $user_id = create_user($name, $last_name, $email, $password);

            if ($user_id) {
                set_flash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                redirect('auth/login');
            } else {
                set_flash('error', 'Erreur lors de l\'inscription.');
            }
        }
    }

    load_view_with_layout('auth/register', $data);
}

/**
 * Déconnexion
 */
function auth_logout() {
    logout();
}

function check_session_timeout() {
    $timeout = 7200; // 2 heures
    if (isset($_SESSION['user']['last_activity']) && (time() - $_SESSION['user']['last_activity']) > $timeout) {
        logout();
    } else {
        $_SESSION['user']['last_activity'] = time();
    }
}
