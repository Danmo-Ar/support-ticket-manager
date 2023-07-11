<link rel="stylesheet" href="./menu.css">

<header class="header">
    <div class="nav">
        <h1 class="logo">Ticket Manager</h1>
    </div>
    <?php
    // require('../utils.php');


    if (!empty($_SESSION)) {

        if (str_contains($_SERVER['HTTP_REFERER'], 'my-ticket.php')) {
            echo  "<a class='menu' href='{$_SERVER['HTTP_REFERER']}'>Accueil</a>";
        } else {
            echo  "<a class='menu' >Accueil</a>";
        }
    }

    if ($role === $roles[0] || $role === $roles[1]) {
        echo  "<a class='menu' href='../auth/register.php'>Nouvel Administrateur</a>";
    }
    ?>

    <div class="user-info">
        <?php

        if (!empty($_SESSION)) {
            echo  "<p>Bienvenue, {$_SESSION['prenom']} {$_SESSION['nom']}</p>";
            echo  "<span>espace : {$role}</span>";
            echo "<br><a href='../auth/logout.php'>Se deconnecter</a>";
        }
        ?>

    </div>

    <style>
        .header a {
            text-decoration: none;
            color: black
        }
    </style>
</header>