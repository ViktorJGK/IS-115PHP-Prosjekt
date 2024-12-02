<?php
session_start();
$_SESSION['logout_message'] = 'Du er nå utlogget.';
session_destroy();
header("Location: public/login.php?logout_message=" . urlencode($_SESSION['logout_message']));
exit();
?>
