<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../class/User.php';


if (isset($_POST['register'])) {
    $user = new User();
    $user->register($_POST['username'], $_POST['password']);
    header("Location: ../../index.php");
}
