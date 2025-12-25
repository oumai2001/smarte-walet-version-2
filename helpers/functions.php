<?php

/**
 * Vérifie si l'utilisateur est connecté
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Récupère le nom de l'utilisateur connecté
 */
function get_user_name()
{
    return $_SESSION['user_name'] ?? 'Invité';
}

/**
 * Récupère l'ID de l'utilisateur connecté
 */
function get_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Redirige vers la page de connexion si non authentifié
 */
function require_auth()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Formate un montant en DH
 */
function format_amount($amount)
{
    return number_format($amount, 2, ',', ' ') . ' DH';
}

/**
 * Formate une date
 */
function format_date($date)
{
    return date('d/m/Y', strtotime($date));
}