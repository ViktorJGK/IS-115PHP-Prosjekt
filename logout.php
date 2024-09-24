<?php
session_start();
$_SESSION['logout_message'] = 'You have successfully logged out.';
session_destroy();
header("Location: public/login.php?logout_message=" . urlencode($_SESSION['logout_message']));
exit();
?>
