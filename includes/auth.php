<?php
include_once __DIR__ . '/functions.php';

if (!validateSession()) {
    header("Location: login.php");
    exit();
}
?>