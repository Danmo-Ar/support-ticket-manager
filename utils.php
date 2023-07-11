<?php

function check($verify)
{
    $verify = trim($verify);
    $verify = stripslashes($verify);
    $verify = htmlspecialchars($verify);
    return $verify;
}


$stateEnum =  array("En cours", "Attente action client", "Cloturé");


$roles =  array('power', "admin", "client");

$userId = $_SESSION['id'] ?? '';
$role = $_SESSION['role'] ?? '';
$service = $_SESSION['service'] ?? '';

$isPower = $role === $roles[0];
