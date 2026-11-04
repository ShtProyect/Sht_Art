<?php
session_start();
header('Location: ' . (isset($_SESSION['user_id']) ? 'galeria.php' : 'login.php'));
exit;