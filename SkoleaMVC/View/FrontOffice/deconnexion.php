<?php
require_once __DIR__ . '/../../bootstrap.php';

unset($_SESSION['utilisateur']);
flash_set('info', 'Vous avez ete deconnecte.');

header('Location: connexion.php');
exit;
