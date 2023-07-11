<?php

header('Location:./auth/login.php');
  
  $message =  "Dont't Love Php but It still the first server Language I Learn So Thank you";
  $criterias= array('Love Pepole' , 'Love Programming');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/style.css">
    <title>Ticket Manager</title>
</head>
<body>

<header class="header">
   <nav class="nav">
     <!-- <ul class="menu">
       <li class="menu-item">Accueil</li>
       <li class="menu-item"></li>
       <li class="menu-item"></li>
     </ul> -->

    <a href="./auth/login.php">Connexion</a>
     <button class="btn btn--register">S'inscrire</button>
   </nav>
</header>

<?= "<h1>{$message}<h1>"?>

 <ul>
    <?php
      foreach($criterias as $criteria ) {
     echo "<li>{$criteria}</li>";
}
     ?>
 </ul>
    
</body>
</html>