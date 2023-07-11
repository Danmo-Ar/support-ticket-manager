<?php
session_start();
require('../database/database.php');
require('../utils.php');



$nameErr = $firstnameErr = $emailErr = $passwordErr = $confirmPasswordErr = $typeDemmandErr  = "";

$lastname = $firstname = $email = $password = $confirmPassword = "";

if (!empty($_POST)) {
    $lastname            = check($_POST['lastname']);
    $firstname     = check($_POST['firstname']);
    $email           = check($_POST['email']);
    $password        = check($_POST['password']);
    $confirmPassword  = check($_POST['confirmPassword']);
    $IsSuccess       = true;
    $typeDemmand  = $isPower ?  check($_POST['typedemand']) : '';


    if (empty($lastname)) {
        $nameErr = "Le nom est est requis";
        $IsSuccess = false;
    }
    if (empty($firstname)) {
        $firstnameErr = "le prenom est requis";
        $IsSuccess = false;
    }
    if (empty($email)) {
        $emailErr = "l'email est requis";
        $IsSuccess = false;
    }
    if (empty($password)) {
        $passwordErr = "le mot de passe est obligatoire";
        $IsSuccess = false;
    }

    if (empty($confirmPassword)) {
        $confirmPasswordErr = "veuillez confirmer votre mot de passe";
        $IsSuccess = false;
    }
    if ($password !== $confirmPassword) {
        $confirmPasswordErr = "les mots de passes ne sont pas identique";
        $IsSuccess = false;
    }


    if ($IsSuccess) {
        $db = database::connection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        if ($isPower) {
            $statement = $db->prepare("INSERT INTO admin (nom, prenom, email, password ,roleId, serviceId) VALUES ( ?, ?, ?, ? , ? , ?)");
            $statement->execute(array($lastname, $firstname, $email, password_hash($password, PASSWORD_DEFAULT), !empty($typeDemmand)  ? 1 : 3, $typeDemmand));
        } else {
            $statement = $db->prepare("INSERT INTO clients (nom, prenom, email , roleId, password) VALUES ( ?, ?, ?,?, ?)");
            $statement->execute(array($lastname, $firstname, $email, 2,  password_hash($password, PASSWORD_DEFAULT)));
        }

        database::dconnection();
        $isPower ? header("Location:../tickets/my-ticket.php") :  header("Location:login.php");
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/index.css">
    <link rel="stylesheet" href="../public/login.css">
    <title>Inscription</title>
</head>

<body>
    <?php require('../menu.php'); ?>
    <div class="auth">
        <ul>

        </ul>
        <div class="auth-wrapper">
            <div class="auth-header">

                <?php echo !$isPower ? "<h3>Inscription</h3>" : " <h3>Ajouter un Admin</h3>" ?>
                <?php echo !$isPower ? " <p>J'ai déja un compte? <a href='./login.php'>Connectez-vous</a></p>"   : "" ?>
            </div>

            <form role="form" class="auth-form-wrapper" method="post" action="register.php">
                <div class='form-wrapper'>
                    <div class="form-group">
                        <label for="">Nom</label>
                        <input name="lastname" type="text" class="marge" placeholder="Nom" value=<?= $lastname ?>>
                        <span class="input-error"><?= $nameErr ?></span>

                    </div>
                    <div class="form-group">
                        <label for="">Prenom</label>
                        <input name='firstname' type="text" placeholder="Prénom" value=<?= $firstname ?>>
                        <span class="input-error"><?= $firstnameErr ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">Email</label>
                    <input name="email" type="text" placeholder="Entrez votre email" value=<?= $email ?>>
                    <span class="input-error"><?= $emailErr ?></span>

                </div>

                <?php
                if ($isPower) {
                    echo " <div class='form-group'>
                    <label for=''>Service</label>
                    <select name='typedemand'>";

                    $db = database::connection();
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $getTypeDemand = $db->query("SELECT * FROM typedemande");

                    $typeDemmands = $getTypeDemand->fetchAll();
                    echo "<option value='' selected >Choisissez le type de la demande</option>";
                    foreach ($typeDemmands as $type) {
                        echo " 
                                 <option value='{$type["id"]}'>{$type["libelle"]}</option>
                                ";
                    }
                }
                ?>
                <?php
                if ($isPower) {
                    echo "  </select>
                             <span class='input-error'>{$typeDemmandErr}</span>
                        </div>";
                }
                ?>


                <div class="form-wrapper">
                    <div class="form-group">
                        <label for="">Mot de Passe</label>

                        <input name=' password' type="password" placeholder=" Entrez le mot de passe">


                    </div>
                    <div class="form-group">
                        <label for="">Confirmation </label>
                        <input name="confirmPassword" type="password" placeholder="Confirmez le mot de passe">
                        <span class="input-error"><?= $confirmPasswordErr ?></span>
                    </div>
                </div>




                <button class="btn--secondary" type="submit">S'inscrire</button>
            </form>


        </div>

    </div>
</body>

</html>