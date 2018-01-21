<?php
session_start();
header('Content-type: application/json');

unset($_SESSION['user']);

echo json_encode(array('logout_done' => 1));

?>