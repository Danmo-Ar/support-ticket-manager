<?php
session_start();
require('../auth/protectedRoute.php');

require('../database/database.php');
require('../utils.php');




mustAccess();
if ($role !== 'client') {
    header('Location:./my-ticket.php');
}
$typedemandErr = $priorityErr = $subjectErr = $messageErr = $fileErr = "";



if (!empty($_POST)) {
    $subject           = check($_POST['subject']);
    if (!empty($subject)) {

        $message        = check($_POST['message']) ?? '';
        $typedemand            = $_POST['typedemand'] ?? check($_POST['typedemand']);
        $priority     = check($_POST['priority']) ?? '';

        $file  = !empty($_FILES) ? check($_FILES['file']['name']) : "";
        $filepath       = "../files/" . basename($file); //definir le chemin où le fichier sera stocker
        $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION); //pour recuperer l'extension du fichier
        $IsSuccess       = true;
        if (empty($typedemand)) {
            $typedemandErr = "Le type de la demmande est requis";
            $IsSuccess = false;
        }
        if (empty($priority)) {
            $priority = "Normal";
        }
        if (empty($subject)) {
            $subjectErr = "Entrez le sujet de votre ticket";
            $IsSuccess = false;
        }
        if (empty($message)) {
            $message = "Veuillez Décire votre problème";
            $IsSuccess = false;
        }
        if (empty($file)) {
            $file = "";
        } else {
            echo $fileExtension;
            if ($fileExtension != 'jpg' && $fileExtension != 'png' && $fileExtension != 'jpeg' && $fileExtension != 'pdf') //verifier l'extension du fichier uploader
            {
                $fileErr = "les extensions autorisées sont : .jpg , .jpeg , .png, .pdf";
                $IsSuccess = false;
            }
            if ($_FILES['file']['size'] > 1000000) {
                $fileErr = "la taille du fichier ne dois pas depasser 1mb";
                $IsSuccess = false;
            }
            if ($IsSuccess) {
                $temp = explode(".", $file);
                $newfilename =  date('YmdHis') . '.' . end($temp);
                $file = $file . $newfilename;
                if (!move_uploaded_file($_FILES['file']['tmp_name'], '../files/' . $file)) {
                    $fileErr = "la taille du fichier ne dois pas depasser 1mb";

                    $IsSuccess = false;
                }
            }
        }



        if ($IsSuccess) {
            $userId = $_SESSION["id"];
            $db = database::connection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $statement = $db->prepare("INSERT INTO tickets (subject, createdAt, updatedAt,typeDemandeId , state , priorityId , filename , userId ) VALUES (?, ?, ?, ?, ? , ? , ? , ? )");
            $statement->execute(array($subject, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $typedemand, $stateEnum[0], $priority, $file, $userId));
            $ticketId = $db->lastInsertId("tickets");
            $sendMessage = $db->prepare("INSERT INTO message (`message`, userId , ticketId , firstmessage) VALUES (?, ? , ? , ?)");
            $currentMessage = $sendMessage->execute(array($message, $userId, $ticketId, 'true'));
            database::dconnection();
            header("Location:../tickets/my-ticket.php");
        }
    }
}




?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/index.css">
    <link rel="stylesheet" href="../public/new-ticket.css">
    <title>Inscription</title>
</head>

<body>
    <?php include_once('../menu.php') ?>
    <div class='container'>
        <h3>Nouveau Ticket</h3>
        <form action="new-ticket.php" method="post" enctype="multipart/form-data">
            <table class="table-style">

                <tbody>

                    <tr>

                        <td class="table-label">Type de demande <sup>*</sup> :</td>

                        <td><select name="typedemand">
                                <option value='' selected disabled>Choisissez le type de la demande</option>
                                <?php
                                $db = database::connection();
                                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $getTypeDemand = $db->query("SELECT * FROM typedemande");

                                $typeDemmands = $getTypeDemand->fetchAll();

                                foreach ($typeDemmands as $type) {
                                    echo "<option value='{$type["id"]}'>{$type["libelle"]}</option>";
                                }

                                ?>
                            </select>
                            <span class="input-error"><?= $typedemandErr ?></span>
                        </td>


                    </tr>

                    <tr>

                        <td class="table-label">Priorité <sup>*</sup> : </td>

                        <td><select name="priority">
                                <option value="" selected disabled>Choisissez la priorité</option>
                                <?php

                                $getPriority = $db->query("SELECT * FROM priorite");

                                $priorites = $getPriority->fetchAll();

                                foreach ($priorites as $priorite) {
                                    echo " <option value='{$priorite["id"]}'>{$priorite["libelle"]}</option>";
                                }
                                $db = database::dconnection();

                                ?>
                            </select>
                            <span class="input-error"><?= $priorityErr ?></span>
                        </td>

                    </tr>

                    <tr>

                        <td class="table-label">Sujet <sup>*</sup> :</td>

                        <td><input name="subject" type="text" id="name" placeholder="Entrez le sujet"> <span class="input-error"><?= $subjectErr ?></span></td>


                    </tr>

                    <tr>

                        <td class="table-label">Message <sup>*</sup> : </td>

                        <div>
                            <td><textarea id="story" name="message" rows="5" cols="33" class='text' placeholder="Entrez le message"></textarea> <span class="input-error"><?= $messageErr ?></span></td>
                        </div>

                    </tr>

                    <tr>

                        <td class="table-label">Piéces jointe <sup>*</sup> :</td>
                        <td><input type="file" name="file" accept="image/png,image/jpeg,application/pdf"></td>
                        <span class="input-error"><?= $fileErr ?></span>

                    </tr>
                    <tr>

                        <td>
                            <button class='btn--primary'>
                                Creer un ticket
                            </button>
                        </td>



                    </tr>



                </tbody>




            </table>
        </form>
    </div>






</body>