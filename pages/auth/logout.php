<?php
session_start();

session_unset();
session_destroy();

header("Location: /sagewood/pages/index.php");
exit();
