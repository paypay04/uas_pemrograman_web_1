<?php
// modules/auth/logout.php
session_start();
session_destroy();

// Redirect ke home
header('Location: /project_uas_web/');
exit();
?>