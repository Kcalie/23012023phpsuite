<?php 
session_start();
require('core/function.php');
$db = pdo_connect();
// if(verifUser())
// {
//     header('location:prive.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include('inc/header.php'); ?>
    <h1>Connectez vous à l'espace membres</h1>

    <?php
        if(isset($_GET['message']))
        {
            $message = urldecode($_GET['message']);
            echo '<div style="background:red;width:100%;border-radius:30px;color:white;">'.$message.'</div>';
        }
    ?><!--This PHP code checks if a GET parameter named 'message' is set. If it is, it decodes the message, and then echoes it out as a red colored div with a white text and rounded corners. The width of the div is set to be 100% of the parent container and it will show the message passed in the querystring.-->

    <form name="membres" action="action.php?e=connexion" method="post">
        <label for="login">login:</label>
        <input type="text" name="login" />
        <br />
        <label for="password">Password:</label>
        <input type="password" name="password" />
        <br />
        <button type="submit" name="submit">Se connecter</button>
    </form>
</body>
</html>