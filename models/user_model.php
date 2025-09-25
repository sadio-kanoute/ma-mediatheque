<?php
// Modèle utilisateur complet

/**
 * Vérifie si l'utilisateur est connecté et admin
 */
function require_admin()
{
    if (!is_logged_in() || $_SESSION['user']['role'] !== 'admin') {
        set_flash('error', 'Accès non autorisé');
        redirect('/');
    }
}

/**
 * Récupère un utilisateur par son email
 */
function get_user_by_email($email)
{
    $query = "SELECT id, name, last_name, email, password, role, created_at, updated_at FROM users WHERE email = ? LIMIT 1";
    return db_select_one($query, [$email]);
}

/**
 * Récupère un utilisateur par son ID
 */
function get_user_by_id($id)
{
    $query = "SELECT id, name, last_name, email, password, role, created_at, updated_at FROM users WHERE id = ? LIMIT 1";
    return db_select_one($query, [$id]);
}

/**
 * Crée un nouvel utilisateur
 */
function create_user($name, $last_name, $email, $password, $role = 'user')
{
    $hashed_password = hash_password($password);
    $query = "INSERT INTO users (name, last_name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())";

    if (db_execute($query, [$name, $last_name, $email, $hashed_password, $role])) {
        return db_last_insert_id();
    }
    return false;
}

/**
 * Met à jour un utilisateur
 */
function update_user($id, $name, $last_name, $email, $role = 'user')
{
    $query = "UPDATE users SET name = ?, last_name = ?, email = ?, role = ?, updated_at = NOW() WHERE id = ?";
    return db_execute($query, [$name, $last_name, $email, $role, $id]);
}

/**
 * Met à jour le mot de passe d'un utilisateur
 */
function update_user_password($id, $password)
{
    $hashed_password = hash_password($password);
    $query = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
    return db_execute($query, [$hashed_password, $id]);
}

/**
 * Supprime un utilisateur
 */
function delete_user($id)
{
    $query = "DELETE FROM users WHERE id = ?";
    return db_execute($query, [$id]);
}

/**
 * Récupère tous les utilisateurs
 * @param int|null $limit
 * @param int $offset
 */
function get_all_users($limit = null, $offset = 0)
{
    $query = "SELECT id, name, last_name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC";

    if ($limit !== null) {
        $query .= " LIMIT $offset, $limit";
    }

    return db_select($query);
}

/**
 * Compte le nombre total d'utilisateurs
 */
function count_users()
{
    $query = "SELECT COUNT(*) as total FROM users";
    $result = db_select_one($query);
    return $result['total'] ?? 0;
}

/**
 * Vérifie si un email existe déjà
 */
function email_exists($email, $exclude_id = null)
{
    $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $params = [$email];

    if ($exclude_id) {
        $query .= " AND id != ?";
        $params[] = $exclude_id;
    }

    $result = db_select_one($query, $params);
    return $result['count'] > 0;
}

/**
 * Alias pour le nombre total d'utilisateurs
 */
function get_users_count()
{
    return count_users();
}
