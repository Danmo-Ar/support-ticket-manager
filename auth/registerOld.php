<?php ?>

<!DOCTYPE html>
<html>
          <head>
          <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
	 <link rel="stylesheet" href="../public/register.css">
           <link rel="stylesheet" href="../public/index.css">
            <title>Register</title>
          </head>

          <body>
                    <div class="register">
                              
                    <form class="formIns" >
                    <h1>Inscription</h1>
                    <label>Nom</label>  <br>
                    <input type="text" name="prenom" placeholder="Entrez votre nom" required autocomplete="off" > <br>

                   <label>Prenom</label>  <br>
                    <input type="text" name="prenom" placeholder="Entrez votre prenom" required autocomplete="off" > <br>
                   

                    <label>Non d'utilisateur</label> <br>
                    <input type="text" name="username" placeholder="Tapez un nom d'utilisateur" required autocomplete="off" >
                
                    <br>

                       
                    <label>Email</label> <br>
                    <input type="email" name="email" placeholder="Entrez votre email" required autocomplete="off"> <br>

                    <label>Mot de passe</label> <br>
                    <input type="password" name="password" placeholder="Votre mot de passe" required autocomplete="off"> <br>

                    <label>Confirme ton mot de passe </label> <br>
                    <input type="password" name="password2" placeholder="Retaper votre mot de passe" required autocomplete="off"> <br>

                    
                    <button type="submit" name="signup-submit" class="btn--secondary">Sign Up</button>
                   

                   
                    </form>
                    </div>
                    
          </body>


</html>