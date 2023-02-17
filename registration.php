<?php

	session_start();

	require_once "database.php";

	if(isset($_POST['nick'])){
		
		$everythings_OK=true;

		$nick = $_POST['nick'];

		if ((strlen($nick)<3) || (strlen($nick)>20)){
			$everythings_OK = false;
			$_SESSION['e_nick'] = "Nick musi posiadać od 3 do 20 znaków";
		}

		if (ctype_alnum($nick)==false){
			$everythings_OK=false;
			$_SESSION['e_nick']="Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
		}

		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email)){
			$everythings_OK=false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
		}

		$newPassword1 =$_POST['newPassword1'];
		$newPassword2 =$_POST['newPassword2'];

		if ((strlen($newPassword1)<8) || (strlen($newPassword1)>20)){
			$everythings_OK = false;
			$_SESSION['e_password']="Hasło musi posiadać od 8 do 20 znaków!";
		}

		if ($newPassword1!=$newPassword2){
			$everythings_OK = false;
			$_SESSION['e_password'] = "Podane hasła nie są identyczne";
		}


		$newPasswordHash = password_hash($newPassword1, PASSWORD_DEFAULT);

		//BOT OR NOT

		$secretKey = "";

		$check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);

		$answer = json_decode($check);

		if($answer->success==false){
			$everythings_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}

			try {
				//Czy  nick jest juz zarezerwowany:
				$result_nick = $db->prepare('SELECT id FROM users WHERE username=:nick');
				$result_nick->bindValue(':nick', $nick, PDO::PARAM_STR);
				$result_nick->execute();

				$howManyNicks = $result_nick->rowCount();

				if($howManyNicks>0){
					$everythings_OK=false;
					$_SESSION['e_nick']="Istnieje już gracz o takim nicku! Wybierz inny.";
				}

				//Czy email już istnieje

				$result = $db->prepare('SELECT id FROM users WHERE email=:email');
				$result->bindValue(':email',$email, PDO::PARAM_STR);
				$result->execute();

				$howManyEmails = $result->rowCount();

				if($howManyEmails>0){
					$everythings_OK=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail";
				}

				// Zapamiętaj wprowdzone dane

				$_SESSION['remember_nick'] = $nick;
				$_SESSION['remember_newPassword1'] = $newPassword1;
				$_SESSION['remember_newPassword2'] = $newPassword2;
				$_SESSION['remember_email'] = $email;


				if($everythings_OK==true){
					$query = $db->prepare('INSERT INTO users (username, password, email) VALUES (:nick, :passwordHash, :email)');
					$query->bindValue(':nick', $nick, PDO::PARAM_STR);
					$query->bindValue(':passwordHash', $newPasswordHash, PDO::PARAM_STR);
					$query->bindValue(':email', $email, PDO::PARAM_STR);
					$query->execute();

					$queryId = $db->prepare('SELECT id FROM users WHERE username = :userName');	
					$queryId->bindValue(':userName', $nick, PDO::PARAM_STR);
					$queryId->execute();
				
					$userId = $queryId->fetch();

					$queryNameDefault = $db->prepare('SELECT name FROM incomes_category_default');	
					$queryNameDefault->execute();

					$queryName = $queryNameDefault->fetchAll();

					foreach ($queryName as $catName){
						$insertIntoAssignedToUsers = $db->prepare('INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
						$insertIntoAssignedToUsers->bindValue(':user_id', $userId['id'], PDO::PARAM_INT);
						$insertIntoAssignedToUsers->bindValue(':name', "{$catName['name']}", PDO::PARAM_STR);
						$insertIntoAssignedToUsers->execute();	
					}
					
					$queryNameExpenseCategoryDefault = $db->prepare('SELECT name FROM expenses_category_default');	
					$queryNameExpenseCategoryDefault->execute();

					$queryNameExpenses = $queryNameExpenseCategoryDefault->fetchAll();

					foreach ($queryNameExpenses as $catExpenseName){
						$insertIntoExpensesCategoryAssignedToUsers = $db->prepare('INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
						$insertIntoExpensesCategoryAssignedToUsers->bindValue(':user_id', $userId['id'], PDO::PARAM_INT);
						$insertIntoExpensesCategoryAssignedToUsers->bindValue(':name', "{$catExpenseName['name']}", PDO::PARAM_STR);
						$insertIntoExpensesCategoryAssignedToUsers->execute();	
					}

					$queryNamePaymentMethodsDefault = $db->prepare('SELECT name FROM payment_methods_default');	
					$queryNamePaymentMethodsDefault->execute();

					$queryNamePayment = $queryNamePaymentMethodsDefault->fetchAll();

					foreach ($queryNamePayment as $paymentMethods){
						$insertIntoExpensesCategoryAssignedToUsers = $db->prepare('INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
						$insertIntoExpensesCategoryAssignedToUsers->bindValue(':user_id', $userId['id'], PDO::PARAM_INT);
						$insertIntoExpensesCategoryAssignedToUsers->bindValue(':name', "{$paymentMethods['name']}", PDO::PARAM_STR);
						$insertIntoExpensesCategoryAssignedToUsers->execute();	
					}

					$_SESSION['session_everythings_OK'] = true;
					header('Location: welcome.php');
				}

			} catch(PDOException $err) {
				echo $err->getMessage();
				exit('Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!');
			}
	}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<title>Budżet osobisty</title>
	
	<meta name="description" content="Aplikacja Budżet osobisty pomoże Ci w zarządzaniu swoimi wydatkami">
	<meta name="keywords" content="budżet osobisty, wydatki, przychody, zarządzaj swoimi wydatkami">
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/style1.css" type="text/css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/css2?family=Rubik+Distressed&display=swap" rel="stylesheet">	
	<script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>


		<header>
			<div class="row justify-content-center m-0 mb-4 p-0">
				<div class="col-xl-12 col-xxl-9">
					<div class="p-0 mt-5 dispaly-as-flex">
							<div class="text-center"><i class="icon-dollar-1"></i></div>
							<div><h1>BUDŻET OSOBISTY</h1></div>
					</div>
					<p class="bo">Aplikacja do zarządzania budżetem osobistym</p>
				</div>
			</div>
		</header>
		<main>
			<article>
				<div class="row justify-content-center m-0 p-0">
					<div class="col-sm-12 col-lg-4 col-xxl-3 log-in-or-register mt-3">
						<p class="p-2"> Masz już konto? </p>
						<div class="submit"><button type="button" onclick="document.location='index.php'" class="btn btn-warning">Zaloguj się</div>
					</div>
					<div class="col-sm-12 col-lg-4 col-xxl-3 mt-3">
						<form method="post">
							<p class="p-2"> Nie masz jeszcze konta? </p>
							<div class="input-group <?php
								if (isset($_SESSION['e_nick']))
									echo "mb-2";
								else echo "mb-3";
							?> w-75 set-margin">
								  <input type="text" class="form-control" placeholder="Podaj nowy login" name="nick" value="<?php
									if (isset($_SESSION['remember_nick'])){
										echo $_SESSION['remember_nick'];
										unset($_SESSION['remember_nick']);
									}
								  ?>" required>
							</div>
							<?php
								if (isset($_SESSION['e_nick'])){
									echo '<div class="margins"><p class="error">'.$_SESSION['e_nick'].'</p></div>';
									unset($_SESSION['e_nick']);
								}
							?>	
							<div class="input-group <?php
								if (isset($_SESSION['e_email']))
									echo "mb-2";
								else echo "mb-3";
							?> w-75 set-margin">
								  <input type="text" class="form-control" placeholder="Podaj adres e-mail" name="email" value="<?php
									if (isset($_SESSION['remember_email'])){
										echo $_SESSION['remember_email'];
										unset($_SESSION['remember_email']);
									}
								  ?>" required>
							</div>
							<?php
								if (isset($_SESSION['e_email'])){
									echo '<div class="margins"><p class="error">'.$_SESSION['e_email'].'</p></div>';
									unset($_SESSION['e_email']);
								}
							?>						
							<div class="input-group <?php
								if (isset($_SESSION['e_password']))
									echo "mb-2";
								else echo "mb-3";
							?>
							w-75 set-margin">
								  <input type="password" class="form-control" placeholder="Podaj nowe hasło" name="newPassword1" value="<?php
								  if(isset($_SESSION['remember_newPassword1'])){
									echo $_SESSION['remember_newPassword1'];
									unset ($_SESSION['remember_newPassword1']);
								  }
								 ?>" required>
							</div>
							<?php
								if (isset($_SESSION['e_password'])){
									echo '<div class="margins"><p class="error">'.$_SESSION['e_password'].'</p></div>';
									unset ($_SESSION['e_password']);
								}
							?>	
							<div class="input-group <?php
								if (isset($_SESSION['e_password']))
									echo "mb-2";
								else echo "mb-3";
							?> w-75 set-margin">
								  <input type="password" class="form-control" placeholder="Powtórz nowe hasło" name="newPassword2" value="<?php
								  	if (isset($_SESSION['remember_newPassword2'])){
										echo $_SESSION['remember_newPassword2'];
										unset ($_SESSION['remember_newPassword2']);
									}
								 ?>" required>
							</div>	
							<div class="g-recaptcha margins center" data-sitekey=""></div>		
							<?php
								if(isset($_SESSION['e_bot'])){
									echo '<div class="margins"><p class="error">'.$_SESSION['e_bot'].'</p></div>';
									unset($_SESSION['e_bot']);
								}
							?>
							<div class="submit"><button type="submit" class="btn btn-success">Zarejestruj się</button></div>	
						</form>
					</div>
				</div> 
			</article>
		</main>
		
		<footer>
			<div class="row justify-content-center m-0 p-0">
				
				<div class="col-sm-12 col-lg-6 p-4 mt-3"> 
					<p>Wszelkie prawa zastrzeżone &copy; 2023 Dziękuję za wizytę!</p>
				</div>
			</div>
		</footer>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>