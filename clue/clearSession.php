<?php
session_start();
session_destroy();
header ( 'Location:/clue/clue.php');
?>