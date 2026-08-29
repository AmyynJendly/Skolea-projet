<?php
// Fonctions utilitaires partagees par les vues et les controleurs.

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function old($old, $key, $default = '')
{
    return e($old[$key] ?? $default);
}

function flash_set($type, $message)
{
    $_SESSION['_flash'][$type] = $message;
}

function flash_get()
{
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $flash;
}

function format_date($datetime, $format = 'd/m/Y')
{
    if (!$datetime) {
        return '';
    }

    try {
        return (new DateTime($datetime))->format($format);
    } catch (Exception $e) {
        return '';
    }
}

function role_label($role)
{
    if ($role === 'administrateur') return 'Administrateur';
    if ($role === 'formateur') return 'Formateur';
    if ($role === 'etudiant') return 'Etudiant';

    return ucfirst($role);
}

function niveau_label($niveau)
{
    if ($niveau === 'debutant') return 'Debutant';
    if ($niveau === 'intermediaire') return 'Intermediaire';
    if ($niveau === 'avance') return 'Avance';

    return ucfirst($niveau);
}

function type_ressource_label($type)
{
    if ($type === 'document') return 'Document';
    if ($type === 'video') return 'Video';
    if ($type === 'quiz') return 'Quiz';

    return ucfirst($type);
}

// --- Session utilisateur ---

function utilisateur_connecte()
{
    return $_SESSION['utilisateur'] ?? null;
}

function est_connecte()
{
    return isset($_SESSION['utilisateur']);
}

function a_le_role(...$roles)
{
    return est_connecte() && in_array($_SESSION['utilisateur']['role'], $roles, true);
}
