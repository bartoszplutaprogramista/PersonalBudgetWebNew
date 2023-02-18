<?php

	session_start();

	require_once "database.php";

	if ((!isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn']==false)){
		header('Location: personal-budget');
		exit();
	}
	if(isset($_POST['amountIncome'])){
		$amountIncome = $_POST['amountIncome'];
		$dateIncome = $_POST['dateIncome'];
		$paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
		$commentIncome = $_POST['commentIncome'];
	
		$queryPaymentCategoryIncome = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId');	
		$queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
		$queryPaymentCategoryIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryPaymentCategoryIncome->execute();

		$paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

		$queryIncome = $db->prepare('INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)');	
		$queryIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryIncome->bindValue(':paymentCategoryIncome', $paymentCategoryIncomeId['id'], PDO::PARAM_INT);
		$queryIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
		$queryIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
		$queryIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
		$queryIncome->execute(); 

		$_SESSION['executedSessionIncomes'] = "Pomyślnie dodano przychód";
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
			<div class="row justify-content-center m-0 mb-2 p-0">
				<div class="col-xl-12 col-xxl-9">
					<div class="p-0 mt-5 dispaly-as-flex">
							<div class="text-center"><i class="icon-dollar-1"></i></div>
							<div><h1>BUDŻET OSOBISTY</h1></div>
					</div>
					<p class="bo">Aplikacja do zarządzania budżetem osobistym</p>
				</div>
			</div>
			<div class="row class-center col-xl-12 col-xxl-9 p-0"><p class="display-right">Witaj 
			<?php 
				echo $_SESSION['userName'];
			?></p></div>
			<div class="row justify-content-center m-0 p-0">
				<nav class="navbar navbar-expand-xl col-xl-12 col-xxl-9 navbar-light top-bar p-0">
					<div class="container-fluid padding-991"> 
						<a class="navbar-brand text-light mx-0" href="#"></a>
						<button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon "></span>
						</button>  
						<div class="collapse navbar-collapse" id="navbarSupportedContent">
							<ul class="navbar-nav mx-auto me-auto mb-2 mb-lg-0">
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="home"><i class="icon-home"></i>Strona główna</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="add-income"><i class="icon-dollar"></i>Dodaj przychód</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="add-expense"><i class="icon-basket"></i>Dodaj wydatek</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link text-light py-3" href="browse-the-balance"><i class="icon-chart-bar"></i>Przeglądaj bilans</a>
								</li>
								<li class="nav-item dropdown li-change-color">
									<a class="nav-link dropdown-toggle text-light py-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="icon-wrench"></i>Ustawienia
									</a>
									<ul class="dropdown-menu dropdown-menu-dark mt-neg" aria-labelledby="navbarDropdown">
										<li class="dropdown-my-color"><a class="dropdown-item text-light py-2" href="#">Zmień adres e-mail</a></li>
										<li class="dropdown-my-color"><a class="dropdown-item text-light py-2" href="#">Zmień hasło</a></li>
									</ul>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="logOut.php"><i class="icon-logout-1"></i>Wyloguj się</a>
								</li>
							</ul>
						</div>
					</div> 
				</nav>
			</div>
		</header>
		<main>
			<article>
				<div class="row justify-content-center m-0 p-0">
					<div class="col-xl-12 col-xxl-9 p-0">
						<div class="content mt-2 p-4">	
							<h3>DODAJ PRZYCHÓD</h3>
							<div class="div-form-buttons mx-auto">
								<form method="post">
									<div class="mb-3">
										<label for="amount" class="form-label">Kwota (zł):</label>
										<input type="number" class="form-control" min="0" step="0.01" id="amount" value="1" name="amountIncome" onkeypress="return onlyNumberKey(event)">
									</div>
									<div class="mb-3">
										<label for="theDate" class="form-label">Data:</label>
										<input type="date" class="form-control" id="theDate" min="2000-01-01" name="dateIncome">
									</div>
									<label class="p-input-radio mb-2" for="paymentCategoryIncomeId">Kategoria płatności:</label>
									<select class="form-select form-select-sm" aria-label="kategoria platnosci" name="paymentCategoryIncomeName" id="paymentCategoryIncomeId">
										<option value="Salary">Wynagrodzenie</option>
										<option value="Interest">Odsetki</option>
										<option value="Allegro">Allegro</option>
										<option value="Another">Inne</option>
									</select>
									<div class="mt-3 mb-3">
										<label for="comment" class="form-label">Komentarz (opcjonalnie):</label>
										<textarea class="form-control" id="comment" rows="2" name="commentIncome"></textarea>
									</div>
									<div class="mt-4 submit">
										<button type="submit" class="btn btn-warning btn-lg">Dodaj</button>
									</div>
								</form>
								<?php 
									if(isset($_SESSION['executedSessionIncomes'])) {
										echo '<div class="margins-2"><p class="succesfully-added">'.$_SESSION['executedSessionIncomes'].'</p></div>';
										unset($_SESSION['executedSessionIncomes']);

										echo '<form>
										<div class="mt-2 submit">
											<button type="button" onclick="document.location=\'add-income\'" class="btn btn-warning ">Dodaj kolejny przychód</button>
										</div>
									</form>';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</article>
		</main>
		<footer>
			<div class="row justify-content-center m-0 p-0">	
				<div class="col-xl-12 col-xxl-9">
					<div class="p-2">
						<p>Wszelkie prawa zastrzeżone &copy; 2023 Dziękuję za wizytę!</p>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="js/script.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>