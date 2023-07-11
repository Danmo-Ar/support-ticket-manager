<?php

require('../utils.php');
require('../database/database.php');

session_start();
// // print_r($_SESSION);
if (!empty($_SESSION)) {
    session_unset();
    session_destroy();
}



$emailErr = $passwordErr  = "";
$email = "";
$IsSuccess       = true;


if (!empty($_POST)) {
    $email           = check($_POST['email']);
    $password        = check($_POST['password']);


    if (empty($email)) {
        $emailErr = "l'email est requis";
        $IsSuccess = false;
    }
    if (empty($password)) {
        $passwordErr = "le mot de passe est obligatoire";
        $IsSuccess = false;
    }



    if ($IsSuccess) {
        $db = database::connection();
        $db->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        $getUser = $db->query("SELECT * FROM clients WHERE email='{$email}'");
        $user = $getUser->fetch();
        if ($user) {
            $getRole = $db->query("SELECT * FROM role WHERE id='{$user["roleId"]}'");
            $role = $getRole->fetch();
            if (password_verify($password, $user["password"])) {
                $_SESSION["nom"] = $user["nom"];
                $_SESSION["prenom"] = $user["prenom"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["id"] = $user["id"];
                $_SESSION["service"] = null;
                $_SESSION["role"] = $role[1];

                header('Location:../tickets/my-ticket.php');
            } else {
                $passwordErr = "mot de passe incorrect";
            }
        } else {
            $getUser = $db->query("SELECT * FROM `admin` WHERE email='{$email}'");
            $user = $getUser->fetch();

            if ($user) {
                $getRole = $db->query("SELECT * FROM `role` WHERE id='{$user["roleId"]}'");
                $role = $getRole->fetch();
                $getService = $db->query("SELECT * FROM typedemande WHERE id='{$user["serviceId"]}'");
                $service = $getService->fetch();
                if (password_verify($password, $user["password"])) {
                    $_SESSION["nom"] = $user["nom"];
                    $_SESSION["prenom"] = $user["prenom"];
                    $_SESSION["email"] = $user["email"];
                    $_SESSION["id"] = $user["id"];
                    $_SESSION["role"] = $role[1];
                    $_SESSION["service"] = $service[0];

                    header('Location:../tickets/my-ticket.php');
                } else {
                    $passwordErr = "le mot de passe est incorrect";
                }
            }
        }


        database::dconnection();
    }
}

// function check($verify)
// {
//     $verify = trim($verify);
//     $verify = stripslashes($verify);
//     $verify = htmlspecialchars($verify);
//     return $verify;
// }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/index.css">
    <link rel="stylesheet" href="../public/login.css">
    <title>Connexion</title>
</head>

<body>
    <?php include_once('../menu.php') ?>
    <div class="auth">

        <div class="auth-wrapper">
            <div class="auth-header">
                <h3>Connexion</h3>
                <p>Vous n'avez de compte? <a href="./register.php">inscrivez-vous</a></p>
            </div>


            <form class="auth-form-wrapper" action="login.php" method="post">
                <div class=" form-group">
                    <label for="">Email</label>
                    <input name="email" type="text" placeholder="Email" value=<?= $email  ?>>
                </div>



                <div class="form-group">
                    <label for="">Mot de Passe</label>

                    <input name='password' type="password" placeholder="Mot de Passe">
                    <span class="input-error"><?= $passwordErr ?></span>
                </div>

                <button class="btn--secondary" type="submit">Se Connecter</button>
            </form>


        </div>

    </div>
</body>

</html>