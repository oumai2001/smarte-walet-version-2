<?php
require_once __DIR__ . '/../helpers/Validator.php';

class Auth
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register($full_name, $email, $password, $confirm_password)
    {
        // Validation des champs
        if (empty($full_name) || empty($email) || empty($password)) {
            return "Tous les champs sont obligatoires";
        }

        if (!Validator::email($email)) {
            return "Email invalide";
        }

        if (strlen($password) < 6) {
            return "Le mot de passe doit contenir au moins 6 caractères";
        }

        if ($password !== $confirm_password) {
            return "Les mots de passe ne correspondent pas";
        }

        // Vérifier si l'email existe déjà
        if ($this->user->findByEmail($email)) {
            return "Cet email est déjà utilisé";
        }

        // Créer l'utilisateur
        if ($this->user->create($full_name, $email, $password)) {
            // Récupérer l'utilisateur créé pour la session
            $userData = $this->user->findByEmail($email);
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['full_name'];
            $_SESSION['user_email'] = $userData['email'];
            return true;
        }

        return "Erreur lors de la création du compte";
    }

    /**
     * Connexion utilisateur
     */
    public function login($email, $password)
    {
        $userData = $this->user->findByEmail($email);

        if ($userData && password_verify($password, $userData['password'])) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['full_name'];
            $_SESSION['user_email'] = $userData['email'];
            return true;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session_destroy();
    }
}