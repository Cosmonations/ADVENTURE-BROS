<?php
session_start();

session_unset();

session_destroy();

if (isset($_COOKIE['cctoken'])) {
    setcookie('cctoken', '', time() - 3600, '/');
}

header("Location: index.php");
exit;
