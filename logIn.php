<?php
    session_start();

    if((!isset($_POST['username'])) || (!isset($_POST['password']))){
        header('Location: personal-budget');
        exit();
    }

    require_once "database.php";

    if(isset($_POST['username'])){
        
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');

        try {
            $result_users = $db->prepare('SELECT * FROM users WHERE username=:nick');
            $result_users->bindValue(':nick', $username, PDO::PARAM_STR);
            $result_users->execute();

            $howManyUsers = $result_users->rowCount();

            if($howManyUsers>0){
                $user = $result_users->fetch();
                if(password_verify($password, $user['password'])){

                    $queryId = $db->prepare('SELECT id FROM users WHERE username = :userName');	
                    $queryId->bindValue(':userName', $username, PDO::PARAM_STR);
                    $queryId->execute();
                
                    $userId = $queryId->fetch();

                    $_SESSION['userId'] = $userId['id'];
                    $_SESSION['userName'] = $username;
                    $_SESSION['loggedIn'] = true;
                    unset($_SESSION['error']);
                    header('Location: home');
                } else {
                    $_SESSION['error'] = 'Nieprawidłowy login lub hasło!';
                    header('Location: personal-budget');
                }
            } else {
                $_SESSION['error'] = 'Nieprawidłowy login lub hasło!';
                header('Location: personal-budget');
            }

        } catch (PDOException $err){
            echo '<p style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</p>';
      //      echo $err->getMessage();
        } 
    }
?>