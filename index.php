<?php

	session_start();

	if ((isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn']==true)){
		header('Location: personalBudget.php');
		exit();
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
</head>

<body>

		<div class="container-fluid">
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
				<div class="row class-center p-0"><p class="display-center">
				<?php 
					if (isset($_SESSION['correctlyLogOut'])) {
						echo $_SESSION['correctlyLogOut'];
						unset ($_SESSION['correctlyLogOut']);
					}
				?></p></div>
			</header>
			<main>
				<article>
					<div class="row justify-content-center m-0 p-0">
						<div class="col-sm-12 col-lg-4 col-xxl-3 log-in-or-register mt-3">
							<form action="logIn.php" method="post">
								<p class="p-2"> Masz już konto? </p>
								<div class="input-group mb-3 w-75 set-margin">
									<input type="text" class="form-control" placeholder="Podaj login" name="username" required>
								</div>	
								<div class="input-group <?php
								if (isset($_SESSION['error']))
									echo "mb-2";
								else echo "mb-3";
								?> w-75 set-margin">
									<input type="password" class="form-control" placeholder="Podaj hasło" name="password" required>
								</div>

								<?php
									if(isset($_SESSION['error'])) {
										echo '<div class="margins"><p class="error">'.$_SESSION['error'].'</p></div>';
										unset($_SESSION['error']);
									}
								?>
								
								<div class="submit"><button type="submit" class="btn btn-warning">Zaloguj się</button></div> 
							</form>
						</div>
						<div class="col-sm-12 col-lg-4 col-xxl-3 mt-3">
							<form>
								<p class="p-2"> Nie masz jeszcze konta? </p>
								<div class="submit"><button type="button" onclick="document.location='registration.php'" class="btn btn-success ">Zarejestruj się</button></div>
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
		</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>