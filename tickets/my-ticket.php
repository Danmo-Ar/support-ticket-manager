<?php



session_start();


require('../auth/protectedRoute.php');

require('../database/database.php');
require('../utils.php');
mustAccess();
$getTicket = null;
$filtre = $_GET['filtre'] ?? '';



?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/index.css">
    <link rel="stylesheet" href="../public/my-ticket.css">


    <title>Inscription</title>
</head>

<body>
    <?php include_once('../menu.php') ?>;

    <main class="container">
        <section class="header-btn">
            <div class='header-btn--left'>
                <a href="my-ticket.php" class='btn btn--primary'>Tous les tickets</a>
                <a href="my-ticket.php?filtre=ouverts" class='btn btn--secondary'>Tickets ouverts</a>
                <a href="my-ticket.php?filtre=fermer" class='btn btn--quatenary'>Tickets fermés</a>
            </div>
            <?php
            if ($role === $roles[2])
                echo " <div class='header-btn--right'>
                <a href='./new-ticket.php' class='btn btn--primary'>Nouveau Ticket</a>
            </div>"
            ?>

        </section>

        <div class="section">

        </div>

        <table id="table" class="table table-bordered table-striped" data-pagination="true" data-page-size="7" data-toggle="table" data-height="460" data-search="true" data-searchable="true" data-sortable="true" data-pagination="true">
            <thead>
                <tr>
                    <th data-field="id" data-sortable="true">N°</th>
                    <th data-field="subject" data-sortable="true">Sujet</th>
                    <th data-field="creation" data-sortable="true">Création</th>
                    <th data-field="update" data-sortable="true">Mise à Jour</th>
                    <th data-field="demmande" data-sortable="true">Demande</th>
                    <th data-field="etat" data-sortable="true">Etat</th>
                    <th data-field="action" data-sortable="true">Action</th>

                </tr>
            <tbody>



                <?php

                $db = database::connection();
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if ($role === $roles[2]) {
                    $getTicket = $db->query("SELECT  tickets.id, tickets.subject, tickets.createdAt, tickets.updatedAt, tickets.state, typedemande.libelle , priorite.level as priorite 
                 FROM tickets,typedemande,priorite WHERE  tickets.typeDemandeId = typedemande.id AND tickets.priorityId = priorite.id AND tickets.userId = {$userId} ORDER BY priorite DESC");
                } else if (
                    $role === $roles[1]  &&
                    $service
                ) {
                    $getTicket = $db->query("SELECT  tickets.id, tickets.subject, tickets.createdAt, tickets.updatedAt, tickets.state, typedemande.libelle ,priorite.level as priorite 
                  FROM tickets,typedemande,priorite WHERE  tickets.typeDemandeId = typedemande.id AND tickets.priorityId = priorite.id AND tickets.typeDemandeId  = '{$service}' ORDER BY priorite DESC");
                } else if ($role === $roles[0]) {
                    $getTicket = $db->query("SELECT  tickets.id, tickets.subject, tickets.createdAt, tickets.updatedAt, tickets.state, typedemande.libelle ,priorite.level as priorite 
                  FROM tickets,typedemande,priorite WHERE  tickets.typeDemandeId = typedemande.id AND tickets.priorityId = priorite.id ORDER BY priorite DESC");
                }

                if ($getTicket) {
                    $tickets =
                        $getTicket->fetchAll();

                    foreach ($tickets as $data) {
                        if ($filtre === 'ouverts') {
                            if (
                                $data['state'] != $stateEnum[0] &&
                                $data['state'] != $stateEnum[1]
                            ) {
                                continue;
                            }
                        } else if ($filtre === 'fermer') {
                            if (
                                $data['state'] != $stateEnum[2]
                            ) {
                                continue;
                            }
                        }
                        echo  "
                         <tr class='flexify-td'>
                           <td scope='col' id='number' data-field='id'>#{$data['id']}</td>
                             <td scope='col' data-field='subject'>{$data['subject']}</td>
                             <td scope='col' data-field='creation'>{$data['createdAt']}</td>
                             <td scope='col' data-field='update'>{$data['updatedAt']}</td>
                             <td scope='col' data-field='demmande'>{$data['libelle']}</td>
                             <td scope='col' data-field='etat'>{$data['state']}</td>
                             <td scope='col' id='actions' data-field='action'>
                            <a title='consulter' href='./ticket-lifecycle.php?ticketId={$data['id']}&action=consulter'> <span class='iconify' data-width='18' data-icon='mdi:eye-outline' style='color: #4e72db;'></span></a>
                              <a class='update {$role}' title='modifier' href='./ticket-lifecycle.php?ticketId={$data['id']}&action=modifier'><span class='iconify' data-width='18' data-icon='bxs:edit' style='color: #e6bd38;'></span></a>
                            <a class='{$data['state']} {$role}' title='ouvrir la discussion' href='./ticket-lifecycle.php?ticketId={$data['id']}&action=discussion'> <span class='iconify' data-width='18'  data-icon='uiw:message' style='color: #1ac988;' ></span></a>
                            </td>
                  </tr>";
                    }
                }
                ?>
            </tbody>





            </thead>
        </table>
    </main>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.16.0/dist/locale/bootstrap-table-fr-FR.min.js"></script>
</body>

</html>