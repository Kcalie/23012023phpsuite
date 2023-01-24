<?php
session_start();
// require obligatoire pour fonctionner, require_once juste une fois, et include juste besoin
require('core/function.php');
?><!--This is a PHP script that starts a new session and includes a file called "function.php" from the "core" directory. The session_start() function starts a new session or resumes an existing one, while the require() function includes the specified file, allowing its contents to be used in the current script. The file "function.php" is in the core directory.-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
    <?php
        include('inc/header.php');// This is a PHP script that includes a file called "header.php" from the "inc" directory. The include() function includes the specified file, allowing its contents to be used in the current script. The file "header.php" is in the "inc" directory
    ?>
    
    </header>
    <h1>Page d'accueil</h1>
    <p>bienvenue sur mon site PHP avec des includes</p>
    <footer>
    <?php
        include('inc/footer.php');
    ?>
    </footer>
</body>
</html>