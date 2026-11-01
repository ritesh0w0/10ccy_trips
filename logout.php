<?php
session_start();
include('includes/db.php');

session_start();
session_destroy();
header('Location: login-choice.php');
exit();
