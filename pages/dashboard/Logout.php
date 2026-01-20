<?php
session_start();
session_destroy();
header('Location: LoginAdmin.php');
exit;
