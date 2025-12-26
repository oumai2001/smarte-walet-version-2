<?php
session_start();

require_once 'models/User.php';
require_once 'helpers/Auth.php';

$user = new User();
$auth = new Auth($user);

// Déconnexion
$auth->logout();

// Rediriger vers login
header('Location: login.php');
exit;
