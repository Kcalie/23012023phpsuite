<?php 
session_start();
require('core/function.php');
$db = pdo_connect();/*pour relié la function a la BDD*/ 
switch($_GET['e'])
{
    case 'inscription':
        // verif le'ensemble des champs saisie
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['email']))
            { // verif si les mdp sont identiques
                if($_POST['password'] == $_POST['password2'])
                {
                    $verif_login = $db->prepare('SELECT User_ID FROM `table_user` WHERE User_Login = :login OR User_Email = :email');
                    $verif_login->bindParam(':login',$_POST['login'],PDO::PARAM_STR);// PARAM_STR = chaine de characteres sinon marche pas
                    $verif_login->bindParam(':email',$_POST['email'],PDO::PARAM_STR);
                    $verif_login->execute();
                    // on verif si un utilisateur est retourné est si il existe deja 
                    if($verif_login->rowCount() == 0)
                    { // cryptage mdp (a voir autres façon password_hash sur php.net)
                        $password = sha1(md5($_POST['password']));
                        $user = $db->prepare('INSERT INTO `table_user` SET
                                                User_Email = :email,
                                                User_Login = :login,
                                                User_password = :password,
                                                User_Date = CURDATE()
                                            ');
                        $user->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                        $user->bindValue(':login',$_POST['login'],PDO::PARAM_STR);
                        $user->bindValue(':password',$password,PDO::PARAM_STR);
                        if($user->execute())
                        {
                            // recupere l'id de l'user et recup la clé primaire 
                            $id_user = $db->lastInsertId();
                            // on crée son repertoire
                            if(!is_dir('upload/'.$id_user))
                            {
                                mkdir('upload/'.$id_user);
                            }
                            setcookie('id_user', $id_user,(time()+3600));
                            setcookie('pass_user',$password,(time()+3600));
                            $_SESSION['connect'] = 1;
                            // on redirige l'user vers sa page privée 
                            header('location:prive.php');
                            exit;
                        } 
                        else
                        {
                            // si il y a une erreur avec avec la requete 
                            $message = 'Une erreur SQL est survenue';
                        }
                    }
                    else
                    {
                        // si l'user existe deja
                        $message = 'login ou email deja enregistré';
                    }
                }
                else
                {
                    // si les 2 mdp ne sont pas identiques
                    $message = 'les 2 mdp ne correspondent pas!!!!';
                }
            }
        }
        header('location:inscription.php?message='.urlencode($message));

        break;

    case 'connexion':
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']))
            {
                $verif_connect = $db->prepare('SELECT User_ID, User_Password FROM `table_user` WHERE User_Login = :login AND User_Password = :password');
                $verif_connect->bindParam(':login',$_POST['login'],PDO::PARAM_STR);
                $verif_connect->bindParam(':password',sha1(md5($_POST['password'])),PDO::PARAM_STR);
                $verif_connect->execute();
                if($verif_connect->rowCount() == 1)
                {
                    $user = $verif_connect->FETCH(PDO::FETCH_OBJ);
                    setcookie('id_user',$user->User_ID,(time()+3600));// fonctionne avec SELECT User_ID, User_Password
                    setcookie('pass_user',$user->User_Password,(time()+3600));// fonctionne avec SELECT User_ID, User_Password
                    $_SESSION['connect'] = 1;
                    header('location:prive.php');
                    exit;
                }
            }
            else
            {
                $message = 'Veuillez renseigner un login et mot de passe';
            }
            header('location:membres.php?message='.urlencode($message));
            exit;
        } 

    break;

    case 'deco':

        $_SESSION['connect'] = 0;
        setcookie('id_user',null,(time()-10));
        setcookie('pass_user',null,(time()-10));
        header('location:membres.php');

        break;/*This is a case statement in the switch block that checks if the value of the "e" GET parameter is "deco". If the case statement is entered, it sets the "connect" key in the $_SESSION superglobal variable to 0, sets the "login" and "password" cookies to null and the time to a negative value, this effectively deletes the cookies.
        Then it redirects the user to the "membres.php" page.
        This code is likely used as a logout function, it's important to note that it only sets the session and cookies to null but it doesn't destroy the session or unset the variables, this could make the session vulnerable to session hijacking.
        It's also important to validate the user is actually logged in before allowing them to log out and properly destroy the session and cookies.
        It's also important to note that the time value of -10 is not a recommended way of deleting cookies as it may not work in all browsers, it's recommended to use the setcookie function with an expired date in the past or use the unset() function.*/ 

    case 'upload':

        $user = verifUser();
        if($user)
        {
            if(isset($_POST['submit']))
            {
                $uploads = uploadFichiers();
                header('location:prive.php?message='.serialize($uploads));
                exit;
            }
        }

        break; 

    case 'deletefichier':
            if(!empty($_GET['fichier']))
            {
                unlink('upload/'.$_COOKIE['login'].'/'.$_GET['fichier']);
                header('location:prive.php');
                exit;
            }
        break;

        case 'download':

            if(!empty($_GET['id']))
            {
                // prepare la requete d'update
                $req = 'UPDATE `table_file` SET File_Download = File_Download+1, File_Date_Download = CURDATE() WHERE File_ID = '.intval($_GET['id']);//incremente pour les dl et met a jour la date
                // execute la requete d'update
                $db->query($req);
                // prepare la requete pour recup les infos sur le fichier
                $fichier = 'SELECT * FROM `table_file` WHERE File_ID = '.intval($_GET['id']);
                // execute la requete de recup d'info sur le fichier et on le range dans la variables
                $execute = $db->query($fichier);
                // compte le nombre de ligne retourné par la requete
                $nb_ligne = $execute->rowCount();
                if($nb_ligne == 1)
                {
                    // si on a 1 ligne retourné on cree l'objet avec les element du fichier 
                    $info = $execute->fetch(PDO::FETCH_OBJ);
                    // prepare le header avec le renommage du fichier au bon format 
                    header('Content-Disposition: attachement; filename="'.$info->File_Original_Name.'"');
                    // on lis le fichier sur le serveur
                    readfile('upload/'.$info->File_User_ID.'/'.$info->File_Name);
                }
            }

        break;

        case 'contact':
            //si le formuliare été soumis
            if(isset($_POST['submit']))
        {
            // verifier si tous les chaps sont rempli
            if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['sujet']) && !empty($_POST['message']))
            {
                if($_POST['captcha'] == $_SESSION['captchat'])
                {
            
                $captcha2 = unserialize($_SESSION['captcha2']);
                if(in_array($_POST['captcha2'],$captcha2))
                {
                        // pour faire la connexion a la base de données 
                        $contact = $db->prepare('INSERT INTO `table_contact` SET
                        Contact_Prenom = :prenom,
                        Contact_Nom = :nom,
                        Contact_Email = :email,
                        Contact_Sujet = :sujet,
                        Contact_Message = :message,
                        Contact_Date = CURDATE()
                        '); 

                    // attribut les valeurs postées par le formulaire dans les champs de la base
                    $contact->bindValue(':nom',$_POST['nom'],PDO::PARAM_STR);
                    $contact->bindValue(':prenom',$_POST['prenom'],PDO::PARAM_STR);
                    $contact->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                    $contact->bindValue(':sujet',$_POST['sujet'],PDO::PARAM_STR);
                    $contact->bindValue(':message',$_POST['message'],PDO::PARAM_STR);
                    // lancer l'envoir vers la base 
                    $contact->execute();
                    $message = 'formulaire envoyé !!';
                }
                else
                {
                    $message = "Tu n'as aucune culture général";
                }
                } 
                else
                {
                    $message = 'erreur de captcha';
                }                    
            }

            // pour afficher les message dans l'url
            header('location:contact.php?message='.urlencode($message));
        }
        break;
}
?>
faire un token pour toutes les pages de connexion