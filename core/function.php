<?php
function pdo_connect()/*pour se connecter a la BDD*/ 
{
    $DATABASE_HOST = 'localhost';
    $DATABASE_LOGIN = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'db_cloud';
    try{
        return new PDO('mysql:host='.$DATABASE_HOST.';dbname='.$DATABASE_NAME.';charset=utf8',$DATABASE_LOGIN,$DATABASE_PASS);
    }
    catch(PDOException $exception) {
        exit('Erreur de connexion à la base de données');
    }
}
function verifUser()
{
    global $db;
    global $_COOKIE;
    if($_COOKIE['id_user'] && $_COOKIE['pass_user'])
    {
        $verif_user = $db->prepare('SELECT * FROM `table_user` WHERE User_ID = :user AND User_Password = :pass LIMIT 1');
        $verif_user->bindParam(':user',$_COOKIE['id_user'],PDO::PARAM_STR);
        $verif_user->bindParam(':pass',$_COOKIE['pass_user'],PDO::PARAM_STR);
        $verif_user->execute();
        if($verif_user->rowCount() == 1)
        {
            return $verif_user->fetch(PDO::FETCH_OBJ);
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

$menu_footer = array(
    'mentions.php' => 'Mentions légales',
    'contact.php' => 'Nous contacter'
);/*This is a PHP code that creates an array called $menu_footer. The array contains two elements, each element is a key-value pair.
The first element has the key "mentions.php" and the value "Mentions légales", the second element has the key "contact.php" and the value "Nous contacter".
It's likely that this array is used to create a menu or a navigation list in the website's footer. The keys of the array are probably used as the URLs of the links and the values are used as the labels of the links.
It's important to note that this array is static, so if you want to add or modify the links, you will have to do it in the code and not dynamically.*/

// on définit l'emplacement par défaut dans le header
function afficheMenu($emplacement='header')
{
    $str = '<ul>';
    if($emplacement=='header')
    {
        $menu_header = array(
            'index.php' => 'Accueil',
            'inscription.php' => 'Inscription',
            'membres.php' => 'Espaces membres',
            'contact.php' => 'Nous Contacter'
        );
        // boucle for each: dans menu header chercher lien retourner titre selon le modèle du tableau
        foreach($menu_header as $lien => $titre)
        {
            $str.= '<li><a href="'.$lien.'">'.$titre.'</a></li>';
        }
    }
    else if($emplacement=='footer')
    {
        global $menu_footer;
        foreach($menu_footer as $lien => $titre)
        {
            $str.= '<li><a href="'.$lien.'">'.$titre.'</a></li>'; 
        }
    }
    $str.= '<ul>';
    return $str;
}/*PHP function named "afficheMenu" that takes an optional parameter "emplacement" with a default value of "header".The function starts by creating an empty string variable named $str, then it concatenates an opening <ul> tag to it.
It then checks the value of the "emplacement" parameter, if it's equal to "header", it creates a local array called $menu_header which contains three elements, each element is a key-value pair.
The first element has the key "index.php" and the value "Accueil", the second element has the key "membres.php" and the value "Espaces membres", the third element has the key "contact.php" and the value "Nous Contacter".
The function then uses a foreach loop to go through each element of the array, it concatenates an <li> tag containing an <a> tag with the key as the href attribute and the value as the inner HTML to the $str variable.
If the "emplacement" parameter is "footer" the function uses the global $menu_footer array and use a foreach loop to go through each element of the array, it concatenates an <li> tag containing an <a> tag with the key as the href attribute and the value as the inner HTML to the $str variable.
At the end of the function, it concatenates a closing <ul> tag to the $str variable and return the final string.
This function is likely used to create a navigation menu in the website's header or footer, it takes the emplacement parameter to decide which menu array to use, and returns a string that contains the HTML code for the menu.
It's important to note that the function doesn't check the emplacement parameter and create the appropriate menu, but it doesn't check if the emplacement parameter passed is a valid one, it only checks if it's "header" or "footer". If a different value is passed, it will not create any menu. It is also important to validate user's permission to see certain links, and check if the user is authenticated before displaying certain links.*/

$identifiants = array('admin','kevin','jessica');
$motdepasses = array('12345','css');
function verifConnect($login,$password)
{
    global $identifiants;
    global $motdepasses;
    if(in_array($login,$identifiants) && in_array($password,$motdepasses))
    {
        return true;
    }
    else{
        return false;
    }
}/*This is a PHP code that defines two arrays: $identifiants and $motdepasses, and a function called verifConnect.The $identifiants array contains three strings: 'admin', 'kevin', and 'jessica'. The $motdepasses array contains two strings: '12345' and 'css'
The verifConnect function takes two parameters: $login and $password. It starts by using the global statement to bring the $identifiants and $motdepasses arrays into the function's scope.
Then it uses the in_array() function to check if the $login and $password passed as arguments are present in the $identifiants and $motdepasses arrays, respectively.
If both values are present in the arrays, it returns true. If either of them is not present, it returns false.
This function is likely used to verify user's credentials (username and password) during login. It's important to note that this function doesn't use a secure way of storing the usernames and passwords and it's not recommended to use plain text for storing sensitive information in the application. Also, it's not recommended to use only one password for multiple users, it's recommended to use a password hashing function and compare the hash of the provided password with the stored one.*/ 

// Correction
function renomme_image($name)
{
    $date = date('d-m-Y-h-i-s');/*day month Year hours minute seconde*/
    return $date.$name;
}

$extensions = array('.pdf','.png','.jpg','.mp4','.gif'); // sensible a la casse !!!! mettre aussi en majuscules
function uploadFichier($fichier)
{
    global $extensions;
    $verif_extension = strrchr($fichier['name'],'.');
            // on génère une clé
        $chaine = 'azertyuiopqsdfghjklmwxcvbn123456789AZERTYUIOPQSDFGHJKLMWXCVBN';
        $longueur = rand(10,16);
        $key = '';
        for($i=0;$i < $longueur;$i++)
        {
            // le point à la fin pour incrémenter
            $key.= $chaine[rand(0,strlen($chaine)-1)];
        }
    if(in_array($verif_extension,$extensions))
    {
        // verif si le dossier de l'user existe
        if(!is_dir('upload/'.$_COOKIE['login']))
        {
            // si il existe pas on le créer
            mkdir('upload/'.$_COOKIE['login']);
        }
        // on renomme le fichier (correction)
        $nom_fichier = renomme_image($fichier['name']);
        // On transfère notre fichier dans son dossier, la condition sert à retourner si l'envoi a bien été effectué
        if(move_uploaded_file($fichier['tmp_name'],'upload/'.$_COOKIE['login'].'/'.$key.'_'.$nom_fichier))/*< correction*/ 
        {
            return true;
        }
    }
}
// correction
function uploadFichiers()
{
    global $extensions;
    global $_FILES;
    global $db;
    global $user;
    $message = array();
    //on fait une boucle pour parcourir les fichiers
    for($i=0;$i<count($_FILES['fichier']['name']);$i++)
    {
        // verif si l'extension est ok
        $verif_extension = strrchr($_FILES['fichier']['name'][$i],'.');
        if(in_array($verif_extension,$extensions))
        {
            $nom_fichier = renomme_image($_FILES['fichier']['name'][$i]);
            if(move_uploaded_file($_FILES['fichier']['tmp_name'][$i],'upload/'.$user->User_ID.'/'.$nom_fichier))
            {
                // message de succes
                $message[$i] = 'Fichier'.$_FILES['fichier']['name'][$i].' envoyé';
                // on insere le fichier dans la bdd
                $file = $db->prepare('INSERT INTO `table_file` SET 
                                        File_User_ID = :user_id,
                                        File_Name = :nom_fichier,
                                        File_Original_Name = :original_name,
                                        File_Date_Add = CURDATE(),
                                        File_Download = 0,
                                        File_Date_Download = CURDATE()
                                    ');
            $file->bindValue(':user_id',$user->User_ID,PDO::PARAM_INT);
            $file->bindValue(':nom_fichier',$nom_fichier,PDO::PARAM_STR);
            $file->bindValue(':original_name',$_FILES['fichier']['name'][$i],PDO::PARAM_STR);
            $file->execute();
            }
            else
            {
                $message[$i] = 'Erreur avec le fichier' .$_FILES['fichier']['name'][$i];
            }
        }
        else
        {
            $message[$i] = 'Extension non autorisé pour '.$_FILES['fichier']['name'][$i];
        }
    }
}
// Captcha methode 1
function captcha1()
{
    // on genere 2 nbre aleatoire
    $nb1 = rand(1,100);
    $nb2 = rand(1,100);
    $result = $nb1 + $nb2;
    // on enregistre le resultat dans la session
    $_SESSION['captchat'] = $result;
    // on retourne l'operation
    return $nb1.'+'.$nb2. '=';
}
// methode2
function captcha2()
{
    $question = array("Quel est le prenom de la premiere dame de france ?",
                    "Quel est la couleur des chevaux blanc d'henry 4 ?",
                    "Indiquez le prenom du formateur favoris de Gérald",
                    "Combien de secondes possède 1 heure ?",
                    "Que bois la vache ?",
                    "Que fume Geoffrey ?",
                    "Ou est la voiture de Maxime?",
                    "Quel est la meilleur chaine de magasin alimentaire ?"
);
$reponse = array(
    array("Brigitte","BRIGITTE","brigitte"),
    array("Blanc","BLANC",'blanc'),
    array("Natacha","natacha","NATACHA","Chloé","CHLOE","chloé"),
    array("3600"),
    array("eau","EAU", "Eau", "de l'eau","De l'eau"),
    array("herbe","cbd","résine"),
    array("sur un parking","au bord de la route"),
    array("lidl","Netto")
);
// choisir une question parmis la liste
$aleatoire = rand (0,(count($question)-1));
//on enregistre en session la reponse
$_SESSION['captcha2'] = serialize($reponse[$aleatoire]);
// on retourne la question
return $question[$aleatoire];
}
?>