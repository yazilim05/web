<?php
session_start();
session_destroy(); // Tüm oturumu sil
header("Location: login.php");
exit;
?>