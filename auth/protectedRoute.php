<?php

function mustAccess()
{

    if (empty($_SESSION)) {
        header('Location:../auth/login.php');
    }
}
