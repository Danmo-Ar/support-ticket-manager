<?php

session_start();

require('../database/database.php');
require('../utils.php');
$actions =  array('consulter', "modifier", "discussion");

$ticketId =  $_GET["ticketId"];
$action =  $_GET["action"];
$cloture =  $_GET["etat"] ?? '';


$messageErr = $fileErr = $descriptionErr = $subjectErr = "";

// get ticket
$db = database::connection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$getTicket = $db->query("SELECT tickets.id,tickets.userId, tickets.adminId, tickets.subject, tickets.createdAt, tickets.updatedAt, tickets.state, typedemande.libelle , priorite.libelle as priorite , message.message , message.id as messageId
FROM tickets,typedemande,priorite,message WHERE  tickets.typeDemandeId = typedemande.id AND tickets.priorityId = priorite.id AND message.ticketId = {$ticketId} AND  tickets.id = {$ticketId}");
$ticket =
    $getTicket->fetch();
// cloturer 

if ($cloture === 'cloture' && $role === $roles[2]) {
    $db = database::connection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $updateTicket = $db->prepare("UPDATE tickets SET state = ? , updatedAt = ? WHERE id= ?");
    $updateTicket->execute(array($stateEnum[2], date('Y-m-d H:i:s'),  $ticketId));
    header('Location:./my-ticket.php');
} else if ($cloture === 'rouvrir' && $role === $roles[2]) {
    $db = database::connection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $updateTicket = $db->prepare("UPDATE tickets SET state = ? , updatedAt = ? WHERE id= ?");
    $updateTicket->execute(array($stateEnum[0], date('Y-m-d H:i:s'),  $ticketId));
    header('Location:./my-ticket.php');
}

if ($action === $actions[1] && $role !== $roles[2]) {
    header('Location:my-ticket.php');
}


if (!empty($_POST)) {

    $message        = check($_POST['message'] ?? '');
    $file  = !empty($_FILES) ? check($_FILES['file']['name']) : "";
    $filepath       = "../files/" . basename($file); //definir le chemin où le fichier sera stocker
    $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION); //pour recuperer l'extension du fichier
    $IsSuccess       = true;

    if (empty($message)) {
        $message = "Veuillez Décire votre problème";
        $IsSuccess = false;
    }
    if (empty($file)) {
        $file = "";
    } else {

        if ($fileExtension != 'jpg' && $fileExtension != 'png' && $fileExtension != 'jpeg' && $fileExtension != 'pdf') //verifier l'extension du fichier uploader
        {
            $fileErr = "les extensions autorisées sont : .jpg , .jpeg , .png, .pdf";
            $IsSuccess = false;
        }
        if (file_exists($filepath)) //verifier si un fichier existe
        {
            $fileErr = "ce fichier existe déja";
            $IsSuccess = false;
        }
        if ($_FILES['file']['size'] > 500000) {
            $fileErr = "la taille du fichier ne dois pas depasser 500ko";
            $IsSuccess = false;
        }
        if ($IsSuccess) {
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $filepath)) {
                $fileErr = "la taille du fichier ne dois pas depasser 500ko";
                $IsSuccess = false;
            }
        }
    }



    if ($IsSuccess) {
        $userId = $_SESSION["id"];
        $ticketId =  $_GET["ticketId"];
        $db = database::connection();
        $sendMessage = $updateTicket = null;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($role === $roles[2]) {
            $updateTicket = $db->prepare("UPDATE tickets SET state = ? , updatedAt = ? WHERE id= ?");
            $updateTicket->execute(array($stateEnum[0], date('Y-m-d H:i:s'),  $ticketId));
        } else {
            $updateTicket = $db->prepare("UPDATE tickets SET state = ? , adminId = ? ,  updatedAt = ? WHERE id= ?");
            $updateTicket->execute(array($stateEnum[1], $userId, date('Y-m-d H:i:s'),  $ticketId));
        }



        if ($role === $roles[2]) {
            $sendMessage = $db->prepare("INSERT INTO message (`message`, userId , ticketId) VALUES (?, ? , ? )");
        } else {
            $sendMessage = $db->prepare("INSERT INTO message (`message`, `adminId` , ticketId) VALUES (?, ? , ? )");
        }

        $currentMessage = $sendMessage->execute(array($message, $userId, $ticketId));
        $_POST["message"] = "";
        $db =  database::dconnection();
    }
}
if ($action === $actions[1]) {

    if (!empty($_POST)) {

        $description        = check($_POST['description'] ?? '');
        $subject        = check($_POST['subject'] ?? '');
        $IsSuccess       = true;

        if (empty($description)) {
            $descriptionErr = "Veuillez Décire votre problème";
            $IsSuccess = false;
        }
        if (empty($subject)) {
            $subjectErr = "Veuillez entrez le sujet de votre problème";
        }



        if ($IsSuccess) {
            $userId = $_SESSION["id"];
            $ticketId =  $_GET["ticketId"];
            $db = database::connection();
            $sendMessage = $updateTicket = null;
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $updateTicket = $db->prepare("UPDATE tickets SET subject = ? , updatedAt = ? WHERE id= ?");
            $updateTicket->execute(array($subject, date('Y-m-d H:i:s'),  $ticketId));

            $updateMessage = $db->prepare("UPDATE message SET message = ? ,   createdAt = ? WHERE id= ?");
            $updateMessage->execute(array($description, date('Y-m-d H:i:s'),  $ticket['messageId']));


            $db =  database::dconnection();
            header("Location:ticket-lifecycle.php?ticketId={$ticketId}&action=consulter");
        }
    }
}


$db =  database::dconnection();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/ticket-lifecycle.css">
    <link rel="stylesheet" href="../public/new-ticket.css">
    <link rel="stylesheet" href="../public/index.css">
    <link rel="stylesheet" href="../public/login.css">
    <title>Inscription</title>
</head>

<body>
    <?php require('../menu.php') ?>
    <div class='container'>

        <form action=<?= "ticket-lifecycle.php?ticketId={$ticketId}&action={$action}" ?> method="post">
            <div class='top'>
                <?php

                $createdAt = explode(" ", $ticket['createdAt']);
                $updatedAt = explode(" ", $ticket['updatedAt']);
                echo "<h3>Ticket #{$ticket['id']}  Crée le {$createdAt[0]} à {$createdAt[1]} - Derniére mise à jour le {$updatedAt[0]} à {$updatedAt[1]}  </h3>";



                if ($action === $actions[2] && $_SESSION['role'] === 'client') {
                    echo " <a href='ticket-lifecycle.php?ticketId={$ticketId}&action={$action}&etat=cloture' class='btn btn--tertiary'>Cloturer le ticket</a>";
                } else if ($action === $actions[0] && $role === 'client') {

                    if ($ticket['state'] === $stateEnum[2]) {
                        echo "<a href='ticket-lifecycle.php?ticketId={$ticketId}&action={$action}&etat=rouvrir' class='btn btn--primary'>Rouvrir le ticket</a>";
                    } else {
                        echo "<a href='./ticket-lifecycle.php?ticketId={$ticketId}&action=modifier' class='btn btn--primary'>Modifier le ticket</a>";
                    }
                } else if ($action === $actions[1] && $role === 'client' && $ticket['state'] !== $stateEnum[2]) {
                    echo "<button class='btn btn--primary'>Modifier le ticket</button>";
                }

                ?>
            </div>


            <table class="table-style">

                <tbody>

                    <?php



                    echo  "
                            <tr>
                             <tr>

                               <td class='table-label' >Sujet:</td>

                               <td class='libelle toupdate  {$action}'>{$ticket['subject']}</td>
                               <td class='{$action}'> <input value=\"{$ticket['subject']}\" name='subject' type='text' id='name' placeholder='Modifier le sujet' autofocus> 
                                                  


                                 </tr>

                                     <tr>

                                        <td class='table-label'>Priorité:</td>

                                          <td class='libelle {$action}'>{$ticket['priorite']}</td>
                                          
                                      </tr>

                                  <tr>

                                          <td class='table-label'>Demande:</td>

                                            <td class='libelle {$action}'>{$ticket['libelle']}</td>

                                        </tr>
                                        <tr>

                                         <td class='table-label'>Status:</td>
                                          <td class='libelle {$action}'>{$ticket['state']}</td>
                                           
                                        </tr>

                                    <tr>
                                            <td class='table-label'>Description:</td>
                                           <td class='libelle toupdate {$action}'>{$ticket['message']}</td>
                                            <td class='{$action}'> <input value=\"{$ticket['message']}\" name='description' type='text' id='description' placeholder='Modifier la description'> 
                                        </tr>
                                                  ";

                    ?>
                    <tr>

                        <td class="middle">
                        </td>



                    </tr>

                    <?php

                    $db = database::connection();
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $getMessages = $db->query("SELECT  message , createdAt , adminId , userId
                        FROM message WHERE  message.ticketId = {$ticket['id']}");
                    $messages =
                        $getMessages->fetchAll();

                    foreach ($messages  as $data) {
                        $getAdmin = $db->query("SELECT  nom , prenom 
                        FROM admin WHERE  admin.id = '{$data['adminId']}'");
                        $admin =
                            $getAdmin->fetch();
                        $getUser = $db->query("SELECT  nom , prenom 
                        FROM clients WHERE  clients.id = '{$data['userId']}'");
                        $user =
                            $getUser->fetch();


                        $datetime = explode(" ", $data["createdAt"]);
                        $lastname = $user['nom'] ?? $admin['nom'];
                        $firstname = $user['prenom'] ?? $admin['prenom'];
                        echo "
                    <tr>
                        <td class='table-label'>Note du {$datetime[0]} {$datetime[1]} </br> Par {$lastname} {$firstname} </td>
                        <td class='libelle'>
                            <i>Status: {$ticket["state"]}</i></br>
                            
                            <pre>{$data['message']}</pre>
                         
                        </td>
                         </tr>
                     ";
                    }

                    ?>


                    <?php
                    if ($action === $actions[2] && $ticket['state'] !== $stateEnum[2]) {
                        echo "<tr>

                        <td class='table-label'>Message</td>
                        <td class='libelle'><textarea id='story' name='message' rows='5' cols='33' class='text small ' placeholder='Entrez un message'></textarea></td>
                    </tr>

                    <tr>
                        <td class='table-label'>Piéces jointes</td>
                        <td class='libelle'><input type='file' name='file' accept='image/png,image/jpeg,application/pdf' /></td>
                    </tr>

                    <tr>
                        <td class='last-btn'>
                            <button type='submit' class='btn--primary'>
                                Repondre
                            </button>
                        </td>
                    </tr>";
                    }

                    ?>

                </tbody>
            </table>

        </form>
    </div>






</body>