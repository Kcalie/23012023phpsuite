<?php
session_start();
require_once('core/function.php');
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="background:grey;"><!--CSS-->
    <?php include('inc/header.php') ?>
    <h1>Nous Contacter</h1>
    <form name="contact" method="POST" action="action.php?e=contact">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" />
    <br>
    <label for="prenom">Prenom :</label>
    <input type="text" name="prenom" />
    <br>
    <label for="email">Email :</label>
    <input type="email" name="email" />
    <br>
    <label for="sujet">Sujet :</label>
    <input type="sujet" name="sujet" />
    <br>
    <label for="message">Message :</label>
    <textarea name="message"></textarea>
    <br>
    <label for="captcha">calculer <?php echo captcha1();?> :</label>
    <input type="text" name="captcha" />
    <br>
    <label for="captcha2">repondre <?php echo captcha2();?> :</label>
    <input type="text" name="captcha2" />
    <button type="submit" name="submit">Envoyer</button>
    </form>
    <?php include('inc/footer.php') ?>
</body>
</html>